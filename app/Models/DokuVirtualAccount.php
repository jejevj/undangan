<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokuVirtualAccount extends Model
{
    protected $fillable = [
        'user_id',
        'partner_service_id',
        'customer_no',
        'virtual_account_no',
        'virtual_account_name',
        'virtual_account_email',
        'virtual_account_phone',
        'trx_id',
        'amount',
        'currency',
        'payment_type',
        'reference_id',
        'channel',
        'trx_type',
        'reusable',
        'min_amount',
        'max_amount',
        'expired_at',
        'status',
        'paid_at',
        'doku_response',
        'doku_reference_no',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'reusable' => 'boolean',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'doku_response' => 'array',
    ];

    /**
     * Get the user that owns the virtual account
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if VA is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->expired_at === null || $this->expired_at->isFuture());
    }

    /**
     * Check if VA is paid
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Check if VA is expired
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
     * Get VA number for display (without leading/trailing spaces)
     * The VA number in database includes spaces for API validation,
     * but customers should see it without spaces
     */
    public function getDisplayVaNumberAttribute(): string
    {
        return trim($this->virtual_account_no);
    }

    /**
     * Get bank name from channel
     */
    public function getBankNameAttribute(): string
    {
        $channelMap = [
            'VIRTUAL_ACCOUNT_BANK_CIMB' => 'CIMB Niaga',
            'VIRTUAL_ACCOUNT_BANK_MANDIRI' => 'Mandiri',
            'VIRTUAL_ACCOUNT_BANK_BRI' => 'BRI',
            'VIRTUAL_ACCOUNT_BANK_BNI' => 'BNI',
            'VIRTUAL_ACCOUNT_BANK_PERMATA' => 'Permata',
            'VIRTUAL_ACCOUNT_BANK_BCA' => 'BCA',
        ];

        return $channelMap[$this->channel] ?? $this->channel;
    }

    /**
     * Scope for active VAs
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where(function ($q) {
                        $q->whereNull('expired_at')
                          ->orWhere('expired_at', '>', now());
                    });
    }

    /**
     * Scope for pending VAs
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for paid VAs
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
