<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransCallbackController extends Controller
{
    protected MidtransService $midtransService;

    public function __construct(MidtransService $midtransService)
    {
        $this->midtransService = $midtransService;
    }

    /**
     * Handle incoming webhook notification from Midtrans.
     */
    public function handle(Request $request): JsonResponse
    {
        $payload = $request->all();

        Log::info('Midtrans Webhook Received', $payload);

        $orderId = $payload['order_id'] ?? null;
        $statusCode = $payload['status_code'] ?? null;
        $grossAmount = $payload['gross_amount'] ?? null;
        $signatureKey = $payload['signature_key'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $type = $payload['payment_type'] ?? 'midtrans';
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId || !$statusCode || !$grossAmount || !$signatureKey) {
            return response()->json(['message' => 'Invalid payload format'], 400);
        }

        // Verify signature when server key is properly configured
        $serverKey = config('midtrans.server_key', '');
        if ($serverKey && !str_contains($serverKey, 'YOUR_SERVER_KEY')) {
            $isValid = $this->midtransService->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey);
            if (!$isValid) {
                Log::warning('Midtrans Webhook Invalid Signature', ['order_id' => $orderId]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }
        }

        // Find Order safely — avoid PostgreSQL integer type crash
        $order = Order::where('invoice_number', $orderId)
            ->orWhere('payment_gateway_ref', $orderId)
            ->first();

        if (!$order) {
            // Try numeric ID lookup only if the value is actually numeric
            $numericId = filter_var($orderId, FILTER_VALIDATE_INT);
            if ($numericId !== false) {
                $order = Order::find($numericId);
            }
        }

        if (!$order) {
            Log::error('Midtrans Webhook Order Not Found', ['order_id' => $orderId]);
            return response()->json(['message' => 'Order not found'], 404);
        }

        // Process status
        if ($transactionStatus === 'capture') {
            if ($type === 'credit_card') {
                if ($fraudStatus === 'challenge') {
                    $order->update(['status' => Order::STATUS_AWAITING_PAYMENT]);
                } else if ($fraudStatus === 'accept') {
                    $this->markAsPaid($order, $payload);
                }
            }
        } else if ($transactionStatus === 'settlement') {
            $this->markAsPaid($order, $payload);
        } else if ($transactionStatus === 'pending') {
            $order->update(['status' => Order::STATUS_AWAITING_PAYMENT]);
        } else if (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $order->update(['status' => Order::STATUS_CANCELLED]);
        }

        return response()->json(['message' => 'Webhook notification processed successfully']);
    }

    /**
     * Mark order as paid and create payment record (idempotent — prevents duplicates).
     */
    protected function markAsPaid(Order $order, array $payload): void
    {
        // Prevent duplicate payment records on webhook retries
        $transactionId = $payload['transaction_id'] ?? ('midtrans_' . time());

        $existingPayment = Payment::where('order_id', $order->id)
            ->where('proof_image', 'midtrans_ref_' . $transactionId)
            ->first();

        if ($existingPayment) {
            Log::info("Duplicate Midtrans webhook for Order #{$order->id} — skipped.");
            return;
        }

        $order->update([
            'status' => Order::STATUS_PROCESSING,
        ]);

        Payment::create([
            'order_id' => $order->id,
            'payment_method' => 'midtrans_' . ($payload['payment_type'] ?? 'gateway'),
            'amount' => $payload['gross_amount'] ?? $order->grand_total,
            'proof_image' => 'midtrans_ref_' . $transactionId,
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        Log::info('Order #' . $order->id . ' marked as PAID via Midtrans Webhook.');
    }
}
