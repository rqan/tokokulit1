<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Format phone number for WhatsApp.
     */
    public static function formatPhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        }
        return $phone;
    }

    /**
     * Get API key from configuration.
     */
    protected static function getApiKey(): string
    {
        return config('services.fonnte.api_key', '');
    }

    /**
     * Send direct WhatsApp message via Gateway API (Fonnte/Wablas).
     */
    public static function sendDirectMessage(string $phone, string $message): bool
    {
        $formattedPhone = self::formatPhone($phone);
        $apiKey = self::getApiKey();

        if (empty($apiKey)) {
            Log::info("WA Direct Message skipped (No API key set). Target: {$formattedPhone}, Msg: {$message}");
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post('https://api.fonnte.com/send', [
                'target' => $formattedPhone,
                'message' => $message,
            ]);

            if ($response->successful()) {
                Log::info("WA Direct Message Sent to {$formattedPhone}");
                return true;
            }

            Log::error('WA Gateway Error', ['body' => $response->body()]);
        } catch (\Throwable $e) {
            Log::error('WA Gateway Exception: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Send invoice notification via WA API.
     */
    public static function notifyInvoiceCreated(Order $order): void
    {
        $invoiceUrl = route('invoice.show', $order->invoice_number ?? $order->id);
        $text = "Halo {$order->shipping_name}! 🛍️\n\n"
            . "Pesanan Anda di TOKO RAFI Boutique telah dibuat.\n"
            . "No. Invoice: " . ($order->invoice_number ?? "ORDER-{$order->id}") . "\n"
            . "Total Pembayaran: Rp" . number_format($order->grand_total ?? $order->subtotal, 0, ',', '.') . "\n\n"
            . "Silakan cek invoice & bayar melalui link berikut:\n{$invoiceUrl}\n\n"
            . "Terima kasih! 🙏";

        self::sendDirectMessage($order->shipping_phone, $text);
    }

    /**
     * Send order shipped notification via WA API.
     */
    public static function notifyOrderShipped(Order $order): void
    {
        $text = "Halo {$order->shipping_name}! 🚚\n\n"
            . "Pesanan Anda (Invoice: {$order->invoice_number}) telah dikirim!\n"
            . "Kurir: " . strtoupper($order->shipping_courier ?? 'JNE') . "\n"
            . "No. Resi: {$order->tracking_number}\n\n"
            . "Terima kasih telah berbelanja di TOKO RAFI Boutique! ✨";

        self::sendDirectMessage($order->shipping_phone, $text);
    }

    /**
     * Generate rating link for WhatsApp (URL fallback).
     */
    public static function generateRatingLink(Order $order): string
    {
        $phone = self::formatPhone($order->shipping_phone);
        $ratingUrl = route('rating.form', $order->rating_token);

        $message = urlencode(
            "Halo {$order->shipping_name}! 🌟\n\n"
            . "Terima kasih telah berbelanja di TOKO RAFI Store.\n"
            . "Pesanan Anda (Invoice: {$order->invoice_number}) telah selesai.\n\n"
            . "Mohon kesediaannya untuk memberikan rating dan ulasan pesanan Anda melalui link berikut:\n"
            . "{$ratingUrl}\n\n"
            . "Masukan Anda sangat berarti bagi kami. Terima kasih! 🙏"
        );

        return "https://wa.me/{$phone}?text={$message}";
    }

    /**
     * Generate invoice link for WhatsApp (URL fallback).
     */
    public static function generateInvoiceLink(Order $order): string
    {
        $phone = self::formatPhone($order->shipping_phone);
        $invoiceUrl = route('invoice.show', $order->invoice_number ?? $order->id);

        $message = urlencode(
            "Halo {$order->shipping_name}! 🛍️\n\n"
            . "Invoice pesanan Anda sudah siap:\n"
            . "No. Invoice: " . ($order->invoice_number ?? "ORDER-{$order->id}") . "\n"
            . "Total: Rp" . number_format($order->grand_total ?? $order->subtotal, 0, ',', '.') . "\n\n"
            . "Lihat invoice lengkap:\n{$invoiceUrl}\n\n"
            . "Terima kasih! 🙏"
        );

        return "https://wa.me/{$phone}?text={$message}";
    }
}
