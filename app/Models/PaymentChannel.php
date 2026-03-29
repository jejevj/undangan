<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentChannel extends Model
{
    protected $fillable = [
        'type',
        'code',
        'name',
        'category',
        'icon',
        'logo_url',
        'description',
        'fee_type',
        'fee_amount',
        'bin',
        'bin_length',
        'bin_notes',
        'billing_type',
        'feature',
        'bin_type',
        'merchant_bin',
        'partner_service_id',
        'prefix_customer_no',
        'va_trx_type',
        'is_active',
        'is_available',
        'last_checked_at',
        'last_error',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_available' => 'boolean',
        'last_checked_at' => 'datetime',
    ];

    /**
     * Scope: Get active channels
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Get available channels
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: Get active and available channels
     */
    public function scopeActiveAndAvailable($query)
    {
        return $query->where('is_active', true)
                     ->where('is_available', true);
    }

    /**
     * Scope: Get by type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Get Virtual Account channels
     */
    public static function getVirtualAccountChannels()
    {
        return self::ofType('virtual_account')
                   ->activeAndAvailable()
                   ->orderBy('sort_order')
                   ->get();
    }

    /**
     * Get E-Wallet channels
     */
    public static function getEWalletChannels()
    {
        return self::ofType('ewallet')
                   ->activeAndAvailable()
                   ->orderBy('sort_order')
                   ->get();
    }

    /**
     * Mark as available
     */
    public function markAsAvailable()
    {
        $this->update([
            'is_available' => true,
            'last_checked_at' => now(),
            'last_error' => null,
        ]);
    }

    /**
     * Mark as unavailable
     */
    public function markAsUnavailable(string $error = null)
    {
        $this->update([
            'is_available' => false,
            'last_checked_at' => now(),
            'last_error' => $error,
        ]);
    }

    /**
     * Check if channel needs availability check
     * (Check every 1 hour)
     */
    public function needsAvailabilityCheck(): bool
    {
        if (!$this->last_checked_at) {
            return true;
        }

        return $this->last_checked_at->diffInHours(now()) >= 1;
    }

    /**
     * Calculate admin fee based on subtotal
     */
    public function calculateFee(float $subtotal): float
    {
        if ($this->fee_type === 'fixed') {
            return (float) $this->fee_amount;
        } elseif ($this->fee_type === 'percentage') {
            return ceil($subtotal * $this->fee_amount / 100);
        }
        
        return 0;
    }
}
