<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PricingPlan extends Model
{
    protected $fillable = [
        'slug', 'name', 'price', 'badge_color', 'is_popular',
        'max_invitations', 'max_gallery_photos', 'max_music_uploads',
        'gift_section_included', 'can_delete_music', 'features', 'is_active',
    ];

    protected $casts = [
        'is_popular'             => 'boolean',
        'gift_section_included'  => 'boolean',
        'can_delete_music'       => 'boolean',
        'is_active'              => 'boolean',
        'features'               => 'array',
        'price'                  => 'integer',
        'max_invitations'        => 'integer',
        'max_gallery_photos'     => 'integer',
        'max_music_uploads'      => 'integer',
    ];

    public function isFree(): bool { return $this->price === 0; }

    public function formattedPrice(): string
    {
        return $this->price === 0 ? 'Gratis' : 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }
}
