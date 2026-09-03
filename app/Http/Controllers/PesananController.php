<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    /**
     * Menampilkan riwayat pesanan pelanggan.
     */
    public function riwayat()
    {
        $orders = Order::where('user_id', Auth::id())
            ->withCount('items')
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pesanan', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan pelanggan.
     */
    public function detail($id, \App\Services\MidtransService $midtransService)
    {
        $order = Order::with(['items.product', 'payments', 'rating'])->findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses halaman ini.');
        }

        $snapToken = null;
        if (in_array($order->status, [Order::STATUS_AWAITING_PAYMENT, Order::STATUS_PENDING])) {
            $snapToken = $midtransService->createSnapToken($order);
        }

        $midtransClientKey = $midtransService->getClientKey();
        $midtransSnapUrl = $midtransService->getSnapBaseUrl();
        $paymentMethods = PaymentMethod::where('is_active', true)->get();

        return view('pesanan-detail', compact('order', 'paymentMethods', 'snapToken', 'midtransClientKey', 'midtransSnapUrl'));
    }

    /**
     * Mengunggah bukti pembayaran pesanan.
     */
    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'proof_image' => 'required|image|max:5120',
            'payment_method' => 'required|string|max:255',
        ]);

        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses tindakan ini.');
        }

        $path = $request->file('proof_image')->store('payment-proofs', 'public');

        Payment::create([
            'order_id' => $order->id,
            'payment_method' => $request->payment_method,
            'proof_image' => $path,
            'amount' => $order->grand_total ?? $order->subtotal,
            'status' => 'pending',
        ]);

        $order->update(['status' => 'payment_uploaded']);

        return back()->with('success', 'Bukti pembayaran berhasil diunggah.');
    }

    /**
     * Mengkonfirmasi pesanan telah diterima oleh pelanggan.
     */
    public function confirmReceived($id)
    {
        $order = Order::findOrFail($id);

        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses tindakan ini.');
        }

        if ($order->status !== 'shipped') {
            return back()->with('error', 'Status pesanan tidak valid untuk dikonfirmasi.');
        }

        $order->update(['status' => 'completed']);

        return back()->with('success', 'Pesanan telah dikonfirmasi diterima.');
    }
}
