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

    /**
     * Memproses checkout dan membuat pesanan.
     */
    public function process(Request $request)
    {
        $request->validate([
            'shipping_name' => 'required|string|max:255',
            'shipping_email' => 'required|email|max:255',
            'shipping_phone' => 'required|string|max:20',
            'shipping_address' => 'required|string',
            'notes' => 'nullable|string',
            'selected_items' => 'required|array',
        ]);

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

        // Remove only checked out items from session cart
        foreach ($selectedKeys as $key) {
            unset($cart[$key]);
        }
        session()->put('cart', $cart);

        return redirect('/pelanggan/pesanan/' . $order->id)
            ->with('success', 'Pesanan berhasil dibuat. Silakan tunggu konfirmasi admin.');
    }
}
