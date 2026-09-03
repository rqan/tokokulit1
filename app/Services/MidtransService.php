<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MidtransService
{
    protected string $serverKey;
    protected string $clientKey;
    protected bool $isProduction;

    public function __construct()
    {
        $this->serverKey = config('midtrans.server_key', '');
        $this->clientKey = config('midtrans.client_key', '');
        $this->isProduction = (bool) config('midtrans.is_production', false);
    }

    /**
     * Get Midtrans Snap Base URL.
     */
    public function getSnapBaseUrl(): string
    {
        return $this->isProduction
            ? 'https://app.midtrans.com/snap/snap.js'
            : 'https://app.sandbox.midtrans.com/snap/snap.js';
    }

    /**
     * Get Client Key for frontend.
     */
    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    /**
     * Create Snap Token for an Order.
     * Ensures item_details sum equals gross_amount (including discount as negative item).
     */
    public function createSnapToken(Order $order): ?string
    {
        if (!empty($order->snap_token)) {
            return $order->snap_token;
        }

        $endpoint = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';

        $orderId = $order->invoice_number ?? ('ORDER-' . $order->id . '-' . time());
        $grossAmount = (int) round($order->grand_total ?? $order->subtotal);

        $itemDetails = [];
        if ($order->relationLoaded('items') || $order->items()->exists()) {
            foreach ($order->items as $item) {
                $itemDetails[] = [
                    'id' => (string) $item->product_id,
                    'price' => (int) round($item->product_price),
                    'quantity' => (int) $item->quantity,
                    'name' => mb_substr($item->product_name, 0, 50),
                ];
            }
        }

        if (($order->shipping_fee ?? 0) > 0) {
            $itemDetails[] = [
                'id' => 'SHIPPING',
                'price' => (int) round($order->shipping_fee),
                'quantity' => 1,
                'name' => 'Ongkos Kirim',
            ];
        }

        if (($order->additional_fee ?? 0) > 0) {
            $itemDetails[] = [
                'id' => 'FEE',
                'price' => (int) round($order->additional_fee),
                'quantity' => 1,
                'name' => mb_substr($order->additional_fee_note ?? 'Biaya Tambahan', 0, 50),
            ];
        }

        // Add negative discount item so item_details sum == gross_amount
        if (($order->discount_amount ?? 0) > 0) {
            $itemDetails[] = [
                'id' => 'DISCOUNT',
                'price' => -1 * (int) round($order->discount_amount),
                'quantity' => 1,
                'name' => 'Diskon Voucher (' . ($order->voucher_code ?? 'PROMO') . ')',
            ];
        }

        $payload = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $grossAmount,
            ],
            'customer_details' => [
                'first_name' => $order->shipping_name,
                'email' => $order->shipping_email,
                'phone' => $order->shipping_phone,
                'shipping_address' => [
                    'first_name' => $order->shipping_name,
                    'phone' => $order->shipping_phone,
                    'address' => $order->shipping_address,
                ]
            ],
            'item_details' => $itemDetails,
        ];

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . base64_encode($this->serverKey . ':'),
            ])->post($endpoint, $payload);

            if ($response->successful()) {
                $token = $response->json('token');
                $order->update([
                    'snap_token' => $token,
                    'payment_gateway_ref' => $orderId,
                ]);
                return $token;
            }

            Log::error('Midtrans Snap Token Error', ['status' => $response->status(), 'body' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Midtrans Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify notification signature hash from Midtrans webhook.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $hash = hash('sha512', $orderId . $statusCode . $grossAmount . $this->serverKey);
        return hash_equals($hash, $signatureKey);
    }
}
