<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Mail\InvoiceMail;
use App\Services\VoucherService;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Create order from session cart.
     * Fetches fresh prices from DB to prevent session price tampering.
     */
    public function createOrder(array $shippingData, array $cart): Order
    {
        return DB::transaction(function () use ($shippingData, $cart) {
            $subtotal = 0;
            $orderItems = [];

            foreach ($cart as $cartKey => $item) {
                // Fetch fresh price from database to prevent tampering
                $product = Product::find($item['product']['id']);
                $price = $product ? (float) $product->price : (float) $item['product']['price'];

                $subtotal += $price * $item['quantity'];

                $productName = $product->name ?? $item['product']['name'];
                if (!empty($item['size']) || !empty($item['color'])) {
                    $extras = [];
                    if (!empty($item['size'])) $extras[] = 'Size: ' . $item['size'];
                    if (!empty($item['color'])) $extras[] = 'Color: ' . $item['color'];
                    $productName .= ' (' . implode(', ', $extras) . ')';
                }

                $orderItems[] = [
                    'product_id' => $item['product']['id'],
                    'product_name' => $productName,
                    'product_price' => $price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $price * $item['quantity'],
                ];

                // Decrement product stock
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'shipping_name' => $shippingData['shipping_name'],
                'shipping_email' => $shippingData['shipping_email'],
                'shipping_phone' => $shippingData['shipping_phone'],
                'shipping_address' => $shippingData['shipping_address'],
                'notes' => $shippingData['notes'] ?? null,
                'province_id' => $shippingData['province_id'] ?? null,
                'city_id' => $shippingData['city_id'] ?? null,
                'voucher_code' => $shippingData['voucher_code'] ?? null,
                'discount_amount' => $shippingData['discount_amount'] ?? 0,
                'status' => Order::STATUS_PENDING,
                'subtotal' => $subtotal,
                'grand_total' => max(0, $subtotal - ($shippingData['discount_amount'] ?? 0)),
            ]);

            if (!empty($shippingData['voucher_code'])) {
                app(VoucherService::class)->incrementUsage($shippingData['voucher_code']);
            }

            foreach ($orderItems as $itemData) {
                OrderItem::create(array_merge(['order_id' => $order->id], $itemData));
            }

            return $order;
        });
    }

    /**
     * Approve order: set shipping fee, generate invoice number, calculate grand total.
     * Accounts for discount_amount from vouchers.
     */
    public function approveOrder(Order $order, float $shippingFee, float $additionalFee = 0, ?string $additionalFeeNote = null): Order
    {
        $discount = (float) ($order->discount_amount ?? 0);
        $grandTotal = max(0, $order->subtotal + $shippingFee + $additionalFee - $discount);

        $order->update([
            'invoice_number' => Order::generateInvoiceNumber(),
            'shipping_fee' => $shippingFee,
            'additional_fee' => $additionalFee,
            'additional_fee_note' => $additionalFeeNote,
            'grand_total' => $grandTotal,
            'status' => Order::STATUS_AWAITING_PAYMENT,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        // Send invoice email
        try {
            Mail::to($order->shipping_email)->send(new InvoiceMail($order));
        } catch (\Throwable $e) {
            // Ignore mail failure in dev environment
        }

        // Send WhatsApp notification
        WhatsAppService::notifyInvoiceCreated($order);

        return $order;
    }

    /**
     * Generate WhatsApp invoice link.
     */
    public function generateWhatsAppInvoiceLink(Order $order): string
    {
        $phone = preg_replace('/[^0-9]/', '', $order->shipping_phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }

        $invoiceUrl = route('invoice.show', $order->invoice_number);
        $message = urlencode(
            "Halo {$order->shipping_name}! 🛍️\n\n"
            . "Invoice pesanan Anda sudah siap:\n"
            . "No. Invoice: {$order->invoice_number}\n"
            . "Total: Rp" . number_format($order->grand_total, 0, ',', '.') . "\n\n"
            . "Lihat invoice lengkap:\n{$invoiceUrl}\n\n"
            . "Terima kasih! 🙏"
        );

        return "https://wa.me/{$phone}?text={$message}";
    }
}

