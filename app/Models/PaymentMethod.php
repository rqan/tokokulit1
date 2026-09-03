<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'bank_name',
        'account_number',
        'account_holder',
        'qris_image',
        'is_active',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope a query to only include active payment methods.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include bank transfer payment methods.
     */
    public function scopeBankTransfers(Builder $query): Builder
    {
        return $query->where('type', 'bank_transfer');
    }

    /**
     * Scope a query to only include QRIS payment methods.
     */
    public function scopeQris(Builder $query): Builder
    {
        return $query->where('type', 'qris');
    }
}
