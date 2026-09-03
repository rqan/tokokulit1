<?php

namespace App\Services;

use App\Models\Voucher;

class VoucherService
{
    /**
     * Validate voucher code and return array result.
     */
    public function applyVoucher(string $code, float $subtotal): array
    {
        $voucher = Voucher::where('code', strtoupper(trim($code)))->first();

        if (!$voucher) {
            return [
                'success' => false,
                'message' => 'Kode voucher tidak ditemukan.',
                'discount' => 0,
            ];
        }

        if (!$voucher->isValid($subtotal)) {
            $msg = 'Voucher tidak dapat digunakan.';
            if ($subtotal < $voucher->min_spend) {
                $msg = 'Minimal pembelian untuk voucher ini adalah Rp' . number_format($voucher->min_spend, 0, ',', '.');
            } elseif ($voucher->expires_at && $voucher->expires_at->isPast()) {
                $msg = 'Voucher sudah kedaluwarsa.';
            } elseif ($voucher->usage_limit !== null && $voucher->used_count >= $voucher->usage_limit) {
                $msg = 'Batas kuota penggunaan voucher telah habis.';
            }

            return [
                'success' => false,
                'message' => $msg,
                'discount' => 0,
            ];
        }

        $discount = $voucher->calculateDiscount($subtotal);

        return [
            'success' => true,
            'message' => 'Voucher berhasil dipasang!',
            'voucher' => $voucher,
            'discount' => $discount,
        ];
    }

    /**
     * Increment the usage count of a voucher.
     */
    public function incrementUsage(string $code): void
    {
        $voucher = Voucher::where('code', strtoupper(trim($code)))->first();
        if ($voucher) {
            $voucher->increment('used_count');
        }
    }
}
