<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'min_spend' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Check if voucher is valid for a given spend.
     */
    public function isValid(float $subtotal): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return false;
        }

        if ($subtotal < $this->min_spend) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if (!$this->isValid($subtotal)) {
            return 0;
        }

        if ($this->type === 'percent') {
            $discount = ($subtotal * $this->amount) / 100;
            if ($this->max_discount && $discount > $this->max_discount) {
                $discount = $this->max_discount;
            }
            return $discount;
        }

        return min($this->amount, $subtotal);
    }
}
