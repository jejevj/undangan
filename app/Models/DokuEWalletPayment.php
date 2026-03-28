<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokuEWalletPayment extends Model
{
    protected $fillable = [
        'user_id',
        'partner_reference_no',
        'amount',
        'currency',
        'payment_type',
        'reference_id',
        'channel',
        'customer_name',
        'customer_email',
        'customer_phone',
        'web_redirect_url',
        'mobile_deep_link',
        'status',
        'expired_at',
        'paid_at',
        'doku_response',
        'doku_transaction_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'doku_response' => 'array',
    ];

    /**
     * Get the user that owns the payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if payment is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if payment is processing
     */
    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }

    /**
     * Check if payment is success
     */
    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    /**
     * Check if payment is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expired_at !== null && $this->expired_at->isPast());
    }

    /**
     * Mark as success
     */
    public function markAsSuccess(): void
    {
        $this->update([
            'status' => 'success',
            'paid_at' => now(),
        ]);
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(): void
    {
        $this->update(['status' => 'failed']);
    }

    /**
     * Mark as expired
     */
    public function markAsExpired(): void
    {
        $this->update(['status' => 'expired']);
    }

    /**
     * Mark as cancelled
     */
    public function markAsCancelled(): void
    {
        $this->update(['status' => 'cancelled']);
    }

    /**
     * Get formatted amount
     */
    public function getFormattedAmountAttribute(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    /**
     * Get channel name
     */
    public function getChannelNameAttribute(): string
    {
        $channels = [
            'EMONEY_SHOPEE_PAY_SNAP' => 'ShopeePay',
            'EMONEY_DANA_SNAP' => 'DANA',
            'EMONEY_OVO_SNAP' => 'OVO',
        ];

        return $channels[$this->channel] ?? $this->channel;
    }

    /**
     * Scope for active payments
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'processing'])
                    ->where(function ($q) {
                        $q->whereNull('expired_at')
                          ->orWhere('expired_at', '>', now());
                    });
    }

    /**
     * Scope for success payments
     */
    public function scopeSuccess($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope for user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for payment type
     */
    public function scopeForPaymentType($query, $type)
    {
        return $query->where('payment_type', $type);
    }
}
