<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OrderService;
use Illuminate\Support\Facades\Auth;

class CheckoutController extends Controller
{
    protected OrderService $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Menampilkan halaman checkout.
     */
    public function index(Request $request)
    {
        $cart = session()->get('cart', []);
        
        if (empty($cart)) {
            return redirect('/pelanggan/cart')->with('error', 'Keranjang Anda kosong.');
        }

        // Filter cart based on selected items if provided
        $selectedKeys = $request->input('selected_items', []);
        $checkoutCart = [];

        if (!empty($selectedKeys)) {
            foreach ($selectedKeys as $key) {
                if (isset($cart[$key])) {
                    $checkoutCart[$key] = $cart[$key];
                }
            }
        } else {
            // Default to all if none selected (fallback)
            $checkoutCart = $cart;
        }

        if (empty($checkoutCart)) {
            return redirect('/pelanggan/cart')->with('error', 'Tidak ada item yang dipilih untuk checkout.');
        }

        return view('checkout', ['cart' => $checkoutCart, 'selectedKeys' => array_keys($checkoutCart)]);
    }

    public function process(Request $request)
    {
        // 1. Honeypot check (Bot Trap)
        if ($request->filled('fax_number')) {
            // Drop silently if bot fills it
            return redirect('/')->with('success', 'Pesanan sedang diproses.');
        }

        // 2. Cloudflare Turnstile Validation (If configured)
        if (env('TURNSTILE_SECRET_KEY')) {
            $turnstileResponse = \Illuminate\Support\Facades\Http::asForm()->post('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
                'secret' => env('TURNSTILE_SECRET_KEY'),
                'response' => $request->input('cf-turnstile-response'),
                'remoteip' => $request->ip()
            ]);

            if (!$turnstileResponse->json('success')) {
                return back()->withErrors(['captcha' => 'Verifikasi keamanan gagal. Silakan selesaikan CAPTCHA dan coba lagi.'])->withInput();
            }
        }

        // 3. Stricter Validations
        $request->validate([
            'shipping_name' => 'required|string|min:3|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => ['required', 'string', 'regex:/^(^\+62|62|0)8[1-9][0-9]{6,10}$/'],
            'shipping_address' => 'required|string|min:10',
            'notes' => 'nullable|string',
            'selected_items' => 'required|array',
        ], [
            'shipping_phone.regex' => 'Format nomor WhatsApp tidak valid (Gunakan 08x atau +628x)'
        ]);

        // 4. Blacklist Check
        $isBlacklisted = \App\Models\Blacklist::where('ip_address', $request->ip())
                                  ->orWhere('phone', $request->input('shipping_phone'))->exists();
        if ($isBlacklisted) {
            abort(403, 'Akses ditolak.');
        }

        // 5. Risk Flag Detection
        $ip = $request->ip();
        $isFirstTime = !\App\Models\Order::where('shipping_phone', $request->input('shipping_phone'))->exists();
        
        $frequentIp = \App\Models\Order::where('ip_address', $ip)
                           ->where('created_at', '>=', now()->subMinutes(5))
                           ->count() >= 2;

        $riskFlag = false;
        $riskReason = null;
        
        if ($isFirstTime) {
            $riskFlag = true;
            $riskReason = 'Nomor HP Baru';
        } elseif ($frequentIp) {
            $riskFlag = true;
            $riskReason = 'Frekuensi IP Tinggi';
        }

        $cart = session()->get('cart', []);
        $selectedKeys = $request->input('selected_items', []);
        $checkoutCart = [];

        foreach ($selectedKeys as $key) {
            if (isset($cart[$key])) {
                $checkoutCart[$key] = $cart[$key];
            }
        }

        if (empty($checkoutCart)) {
            return redirect('/pelanggan/cart')->with('error', 'Item yang dipilih tidak valid atau keranjang kosong.');
        }

        $shippingData = $request->only(['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'notes']);
        $shippingData['province_id'] = $request->input('province_id');
        $shippingData['city_id'] = $request->input('city_id');
        
        $sessionVoucher = session('voucher', null);
        if ($sessionVoucher) {
            $shippingData['voucher_code'] = $sessionVoucher['code'];
            $shippingData['discount_amount'] = $sessionVoucher['discount'];
            // Clear voucher after use
            session()->forget('voucher');
        }

        $order = $this->orderService->createOrder($shippingData, $checkoutCart);

        // Update Metadata Security
        $order->update([
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'is_high_risk' => $riskFlag,
            'risk_reason' => $riskReason
        ]);

        // Remove only checked out items from session cart
        foreach ($selectedKeys as $key) {
            unset($cart[$key]);
        }
        session()->put('cart', $cart);

        // Flow A: Direct WhatsApp
        $waMessage = "Halo Admin, saya ingin konfirmasi pesanan:\n\nNama: {$order->shipping_name}\nNo WA: {$order->shipping_phone}\nOrder ID: {$order->id}\n\nMohon diproses.";
        $adminPhone = '6281234567890'; // Sesuaikan dengan nomor admin
        $waUrl = "https://wa.me/{$adminPhone}?text=" . urlencode($waMessage);

        return redirect()->away($waUrl);
    }
}
