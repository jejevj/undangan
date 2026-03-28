<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokuQrisPayment extends Model
{
    protected $fillable = [
        'user_id',
        'partner_reference_no',
        'reference_no',
        'merchant_id',
        'terminal_id',
        'amount',
        'currency',
        'payment_type',
        'reference_id',
        'qr_content',
        'qr_image_url',
        'status',
        'expired_at',
        'paid_at',
        'postal_code',
        'fee_type',
        'approval_code',
        'doku_response',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'doku_response' => 'array',
    ];

    /**
     * Get the user that owns the QRIS payment
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if QRIS is active
     */
    public function isActive(): bool
    {
        return $this->status === 'pending' && 
               ($this->expired_at === null || $this->expired_at->isFuture());
    }

    /**
     * Check if QRIS is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if QRIS is expired
     */
    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expired_at !== null && $this->expired_at->isPast());
    }

    /**
     * Mark as paid
     */
    public function markAsPaid(): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
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
     * Scope for active QRIS
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'pending')
                    ->where(function ($q) {
                        $q->whereNull('expired_at')
                          ->orWhere('expired_at', '>', now());
                    });
    }

    /**
     * Scope for pending QRIS
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid QRIS
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
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
