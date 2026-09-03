<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Order extends Model
{
    use SoftDeletes;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public const STATUS_PENDING = 'pending_confirmation';
    public const STATUS_AWAITING_PAYMENT = 'awaiting_payment';
    public const STATUS_PAYMENT_UPLOADED = 'payment_uploaded';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'rating_sent_at' => 'datetime',
            'subtotal' => 'decimal:2',
            'shipping_fee' => 'decimal:2',
            'additional_fee' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    /**
     * Get the user that owns the order.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the items for the order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the payments for the order.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the rating associated with the order.
     */
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Get the user who approved the order.
     */
    public function approvedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the human-readable status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Menunggu Konfirmasi',
            self::STATUS_AWAITING_PAYMENT => 'Menunggu Pembayaran',
            self::STATUS_PAYMENT_UPLOADED => 'Pembayaran Diunggah',
            self::STATUS_PROCESSING => 'Diproses',
            self::STATUS_SHIPPED => 'Dikirim',
            self::STATUS_COMPLETED => 'Selesai',
            self::STATUS_CANCELLED => 'Dibatalkan',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get the Tailwind color classes for the status badge.
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'bg-yellow-100 text-yellow-800',
            self::STATUS_AWAITING_PAYMENT => 'bg-orange-100 text-orange-800',
            self::STATUS_PAYMENT_UPLOADED => 'bg-blue-100 text-blue-800',
            self::STATUS_PROCESSING => 'bg-indigo-100 text-indigo-800',
            self::STATUS_SHIPPED => 'bg-purple-100 text-purple-800',
            self::STATUS_COMPLETED => 'bg-green-100 text-green-800',
            self::STATUS_CANCELLED => 'bg-red-100 text-red-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Determine if the order can be rated.
     */
    public function canBeRated(): bool
    {
        return $this->status === self::STATUS_COMPLETED &&
            !$this->rating()->exists() &&
            $this->rating_token !== null;
    }

    /**
     * Generate a new unique invoice number.
     */
    public static function generateInvoiceNumber(): string
    {
        $now = Carbon::now();
        $prefix = 'INV-' . $now->format('Ym') . '-';

        $lastOrder = self::whereYear('created_at', $now->year)
            ->whereMonth('created_at', $now->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = 1;
        if ($lastOrder && preg_match('/-(\d{4})$/', $lastOrder->invoice_number ?? '', $matches)) {
            $sequence = (int) $matches[1] + 1;
        }

        return $prefix . str_pad((string) $sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate and save a rating token.
     */
    public function generateRatingToken(): string
    {
        $this->rating_token = Str::random(64);
        $this->save();

        return $this->rating_token;
    }

    /**
     * Scope a query to only include orders of a given status.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }
}
