<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Campaign extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'pricing_plan_id',
        'max_users',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Pricing plan yang diberikan oleh kampanye
     */
    public function pricingPlan(): BelongsTo
    {
        return $this->belongsTo(PricingPlan::class);
    }

    /**
     * Alias for pricingPlan (for backward compatibility)
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->pricingPlan();
    }

    /**
     * Users yang menggunakan kampanye ini
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Check if campaign is valid and can be used
     */
    public function isValid(): bool
    {
        // Check if active
        if (!$this->is_active) {
            return false;
        }

        // Check date range
        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        // Check max users limit
        if ($this->max_users > 0 && $this->used_count >= $this->max_users) {
            return false;
        }

        return true;
    }

    /**
     * Check if campaign has reached max users
     */
    public function hasReachedLimit(): bool
    {
        return $this->max_users > 0 && $this->used_count >= $this->max_users;
    }

    /**
     * Get remaining slots
     */
    public function getRemainingSlots(): int
    {
        if ($this->max_users === 0) {
            return PHP_INT_MAX; // Unlimited
        }
        return max(0, $this->max_users - $this->used_count);
    }

    /**
     * Increment used count
     */
    public function incrementUsedCount(): void
    {
        $this->increment('used_count');
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        if (!$this->is_active) {
            return 'Nonaktif';
        }

        $now = Carbon::now();
        if ($this->start_date && $now->lt($this->start_date)) {
            return 'Belum Dimulai';
        }
        if ($this->end_date && $now->gt($this->end_date)) {
            return 'Berakhir';
        }
        if ($this->hasReachedLimit()) {
            return 'Kuota Penuh';
        }

        return 'Aktif';
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClass(): string
    {
        $status = $this->getStatusLabel();
        return match($status) {
            'Aktif' => 'badge-success',
            'Nonaktif', 'Berakhir', 'Kuota Penuh' => 'badge-danger',
            'Belum Dimulai' => 'badge-warning',
            default => 'badge-secondary',
        };
    }
}
