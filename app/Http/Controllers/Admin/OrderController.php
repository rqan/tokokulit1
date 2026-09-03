<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\OrderService;
use App\Services\AuditService;
use App\Services\WhatsAppService;
use App\Mail\RatingRequestMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService,
    ) {}

    /**
     * Menampilkan daftar pesanan.
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'payments' => function ($q) {
            $q->latest();
        }]);

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan.
     */
    public function show($id)
    {
        $order = Order::with(['user', 'items.product', 'payments.verifiedByUser', 'rating'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Memperbarui biaya pengiriman.
     */
    public function updateShipping(Request $request, $id)
    {
        $request->validate([
            'shipping_fee' => 'required|numeric|min:0',
            'additional_fee' => 'nullable|numeric|min:0',
            'additional_fee_note' => 'nullable|string|max:255',
        ]);

        $order = Order::findOrFail($id);
        $oldValues = $order->only(['shipping_fee', 'additional_fee', 'additional_fee_note', 'grand_total']);

        $shippingFee = (float) $request->shipping_fee;
        $additionalFee = (float) ($request->additional_fee ?: 0);

        $order->update([
            'shipping_fee' => $shippingFee,
            'additional_fee' => $additionalFee,
            'additional_fee_note' => $request->additional_fee_note,
            'grand_total' => max(0, $order->subtotal + $shippingFee + $additionalFee - (float) ($order->discount_amount ?? 0)),
        ]);

        AuditService::log(
            'order.update_shipping',
            'order',
            $order->id,
            $oldValues,
            $order->only(['shipping_fee', 'additional_fee', 'additional_fee_note', 'grand_total'])
        );

        return back()->with('success', 'Biaya pengiriman berhasil diperbarui.');
    }

    /**
     * Menyetujui pesanan — generate invoice, kirim email.
     */
    public function approve($id)
    {
        $order = Order::findOrFail($id);

        if ($order->status !== Order::STATUS_PENDING) {
            return back()->with('error', 'Pesanan tidak dalam status menunggu konfirmasi.');
        }

        if (is_null($order->shipping_fee)) {
            return back()->with('error', 'Ongkir belum diisi. Silakan isi ongkir terlebih dahulu.');
        }

        $oldValues = $order->only(['status', 'invoice_number', 'grand_total']);

        $this->orderService->approveOrder(
            $order,
            (float) $order->shipping_fee,
            (float) ($order->additional_fee ?? 0),
            $order->additional_fee_note
        );

        $order->refresh();

        AuditService::log(
            'order.approved',
            'order',
            $order->id,
            $oldValues,
            $order->only(['status', 'invoice_number', 'grand_total'])
        );

        return back()->with('success', "Pesanan disetujui. Invoice {$order->invoice_number} telah dikirim ke email pelanggan.");
    }

    /**
     * Memverifikasi pembayaran.
     */
    public function verifyPayment($id)
    {
        $order = Order::findOrFail($id);
        $payment = $order->payments()->where('status', 'pending')->latest()->first();

        if (!$payment) {
            return back()->with('error', 'Tidak ada pembayaran yang menunggu verifikasi.');
        }

        $payment->update([
            'status' => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        $order->update(['status' => Order::STATUS_PROCESSING]);

        AuditService::log('payment.verified', 'payment', $payment->id);
        AuditService::log('order.status_changed', 'order', $order->id, ['status' => 'payment_uploaded'], ['status' => 'processing']);

        return back()->with('success', 'Pembayaran berhasil diverifikasi. Pesanan sedang diproses.');
    }

    /**
     * Menolak pembayaran.
     */
    public function rejectPayment(Request $request, $id)
    {
        $request->validate([
            'rejection_note' => 'required|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $payment = $order->payments()->where('status', 'pending')->latest()->first();

        if (!$payment) {
            return back()->with('error', 'Tidak ada pembayaran yang menunggu verifikasi.');
        }

        $payment->update([
            'status' => 'rejected',
            'rejection_note' => $request->rejection_note,
        ]);

        $order->update(['status' => Order::STATUS_AWAITING_PAYMENT]);

        AuditService::log('payment.rejected', 'payment', $payment->id, null, ['rejection_note' => $request->rejection_note]);

        return back()->with('success', 'Pembayaran ditolak. Pelanggan dapat mengunggah bukti ulang.');
    }

    /**
     * Memperbarui resi pengiriman.
     */
    public function updateTracking(Request $request, $id)
    {
        $request->validate([
            'tracking_number' => 'required|string|max:100',
            'shipping_courier' => 'required|string|max:100',
        ]);

        $order = Order::findOrFail($id);

        $order->update([
            'tracking_number' => $request->tracking_number,
            'shipping_courier' => $request->shipping_courier,
            'status' => Order::STATUS_SHIPPED,
        ]);

        AuditService::log('order.shipped', 'order', $order->id, null, [
            'tracking_number' => $request->tracking_number,
            'shipping_courier' => $request->shipping_courier,
        ]);

        // Send WhatsApp notification to customer
        WhatsAppService::notifyOrderShipped($order);

        return back()->with('success', 'Resi pengiriman berhasil diperbarui. Status pesanan: Dikirim.');
    }

    /**
     * Menandai pesanan sebagai selesai.
     */
    public function markCompleted($id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => Order::STATUS_COMPLETED]);

        AuditService::log('order.completed', 'order', $order->id);

        return back()->with('success', 'Pesanan ditandai selesai.');
    }

    /**
     * Membatalkan pesanan.
     */
    public function cancel(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => Order::STATUS_CANCELLED]);

        AuditService::log('order.cancelled', 'order', $order->id);

        return back()->with('success', 'Pesanan berhasil dibatalkan.');
    }

    /**
     * Mengirim link rating via WhatsApp atau Email.
     */
    public function sendRatingLink(Request $request, $id)
    {
        $order = Order::with('user')->findOrFail($id);

        if ($order->status !== Order::STATUS_COMPLETED) {
            return back()->with('error', 'Pesanan belum selesai.');
        }

        if ($order->rating()->exists()) {
            return back()->with('error', 'Pelanggan sudah memberikan rating untuk pesanan ini.');
        }

        // Generate token jika belum ada
        if (!$order->rating_token) {
            $order->rating_token = Str::random(64);
        }
        $order->rating_sent_at = now();
        $order->save();

        $ratingUrl = route('rating.form', $order->rating_token);
        $channel = $request->input('channel', 'email');

        AuditService::log('rating.link_sent', 'order', $order->id, null, ['channel' => $channel]);

        if ($channel === 'whatsapp') {
            $phone = WhatsAppService::formatPhone($order->shipping_phone);
            $message = urlencode(
                "Halo {$order->shipping_name}! 🙏\n\n"
                . "Terima kasih sudah berbelanja di ENY LEATHER.\n"
                . "Pesanan Anda ({$order->invoice_number}) telah selesai.\n\n"
                . "Kami sangat menghargai jika Anda bersedia memberikan rating:\n"
                . "{$ratingUrl}\n\n"
                . "Terima kasih! ⭐"
            );
            $waUrl = "https://wa.me/{$phone}?text={$message}";

            return redirect()->away($waUrl);
        }

        // Email channel
        Mail::to($order->shipping_email)->send(new RatingRequestMail($order, $ratingUrl));

        return back()->with('success', 'Link rating berhasil dikirim via email.');
    }
}
