<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name', 'slug', 'type', 'price', 'free_photo_limit', 'extra_photo_price',
        'gift_feature_price', 'guest_limit', 'thumbnail', 'preview_url',
        'blade_view', 'asset_folder', 'version', 'description', 'is_active',
    ];

    protected $casts = [
        'is_active'          => 'boolean',
        'price'              => 'integer',
        'free_photo_limit'   => 'integer',
        'extra_photo_price'  => 'integer',
        'gift_feature_price' => 'integer',
        'guest_limit'        => 'integer',
    ];

    // ── Helpers ──────────────────────────────────────────────────────────

    public function isFree(): bool
    {
        return $this->type === 'free' || $this->price === 0;
    }

    public function isPremium(): bool
    {
        return $this->type === 'premium';
    }

    public function isCustom(): bool
    {
        return $this->type === 'custom';
    }

    public function formattedPrice(): string
    {
        if ($this->price === 0) return 'Gratis';
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /** Batas foto galeri: null = unlimited */
    public function hasPhotoLimit(): bool
    {
        return $this->free_photo_limit !== null;
    }

    public function hasGuestLimit(): bool
    {
        return $this->guest_limit !== null;
    }

    public function formattedExtraPhotoPrice(): string
    {
        return 'Rp ' . number_format($this->extra_photo_price, 0, ',', '.');
    }

    /**
     * Path folder assets di public/
     * Contoh: public/invitation-assets/premium-white-1/
     */
    public function assetPath(): string
    {
        return 'invitation-assets/' . ($this->asset_folder ?? $this->slug);
    }

    /**
     * URL asset helper — dipakai di dalam blade template
     * Contoh: $template->asset('css/style.css')
     *         → /invitation-assets/premium-white-1/css/style.css
     */
    public function asset(string $file): string
    {
        return asset($this->assetPath() . '/' . ltrim($file, '/'));
    }

    /**
     * Blade view path untuk template undangan
     * Contoh: invitation-templates.premium-white-1.index
     */
    public function viewPath(string $view = 'index'): string
    {
        $folder = $this->asset_folder ?? $this->slug;
        return 'invitation-templates.' . $folder . '.' . $view;
    }

    // ── Relations ─────────────────────────────────────────────────────────

    public function fields(): HasMany
    {
        return $this->hasMany(TemplateField::class)->orderBy('order');
    }

    public function fieldsByGroup(): \Illuminate\Support\Collection
    {
        return $this->fields->groupBy('group');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
