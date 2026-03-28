<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingPlan extends Model
{
    protected $fillable = [
        'slug', 'name', 'visibility', 'price', 'billing_period', 'badge_color', 'is_popular',
        'max_invitations', 'max_premium_templates', 'max_gallery_photos', 'max_music_uploads',
        'gift_section_included', 'show_partnership_logo', 'can_delete_music', 'features', 'is_active',
    ];

    protected $casts = [
        'is_popular'             => 'boolean',
        'gift_section_included'  => 'boolean',
        'show_partnership_logo'  => 'boolean',
        'can_delete_music'       => 'boolean',
        'is_active'              => 'boolean',
        'features'               => 'array',
        'price'                  => 'integer',
        'max_invitations'        => 'integer',
        'max_premium_templates'  => 'integer',
        'max_gallery_photos'     => 'integer',
        'max_music_uploads'      => 'integer',
    ];

    public function isFree(): bool { return $this->price === 0; }
    
    public function isBusinessPlan(): bool { return $this->visibility === 'business'; }

    public function formattedPrice(): string
    {
        if ($this->price === 0) {
            return 'Gratis';
        }
        
        $price = 'Rp ' . number_format($this->price, 0, ',', '.');
        
        if ($this->billing_period === 'monthly') {
            $price .= ' / bulan';
        } elseif ($this->billing_period === 'yearly') {
            $price .= ' / tahun';
        }
        
        return $price;
    }
    
    /**
     * Scope untuk hanya paket public (bisa dibeli langsung)
     */
    public function scopePublicPlans($query)
    {
        return $query->where('visibility', 'public');
    }
    
    /**
     * Scope untuk paket business (hubungi admin)
     */
    public function scopeBusinessPlans($query)
    {
        return $query->where('visibility', 'business');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }
    
    /**
     * Check if this plan is higher tier than another plan
     * Based on price comparison
     */
    public function isHigherThan(PricingPlan $otherPlan): bool
    {
        return $this->price > $otherPlan->price;
    }
    
    /**
     * Check if this plan is lower tier than another plan
     */
    public function isLowerThan(PricingPlan $otherPlan): bool
    {
        return $this->price < $otherPlan->price;
    }
    
    /**
     * Check if this plan is same tier as another plan
     */
    public function isSameTierAs(PricingPlan $otherPlan): bool
    {
        return $this->price === $otherPlan->price;
    }
}
