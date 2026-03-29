<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invitation extends Model
{
    protected $fillable = [
        'user_id', 'template_id', 'slug', 'title', 'greeting',
        'status', 'gallery_display', 'gift_enabled', 'published_at', 'expired_at', 'is_preview',
        // Preview invitation fields
        'event_date', 'event_time', 'event_location', 'event_address',
        'groom_name', 'groom_father', 'groom_mother',
        'bride_name', 'bride_father', 'bride_mother',
        'opening_text', 'closing_text', 'is_published', 'love_story_mode'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function data(): HasMany
    {
        return $this->hasMany(InvitationData::class);
    }

    public function guests(): HasMany
    {
        return $this->hasMany(Guest::class)->orderBy('name');
    }

    public function gallery(): HasMany
    {
        return $this->hasMany(InvitationGallery::class, 'invitation_id')->orderBy('order');
    }

    /**
     * Get selected photos for this invitation (through pivot)
     */
    public function selectedPhotos()
    {
        return $this->belongsToMany(UserGalleryPhoto::class, 'invitation_gallery', 'invitation_id', 'photo_id')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('invitation_gallery.order');
    }

    public function galleryOrders(): HasMany
    {
        return $this->hasMany(GalleryOrder::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(InvitationBankAccount::class)->orderBy('order');
    }

    public function featureOrders(): HasMany
    {
        return $this->hasMany(InvitationFeatureOrder::class);
    }

    public function loveStoryTimeline(): HasMany
    {
        return $this->hasMany(LoveStoryTimeline::class)->orderBy('order');
    }

    public function guestMessages(): HasMany
    {
        return $this->hasMany(GuestMessage::class)
            ->where('is_approved', true)
            ->orderByDesc('likes_count')
            ->latest();
    }

    /**
     * Check if user can use timeline mode (premium only)
     */
    public function canUseTimelineMode(): bool
    {
        // Check if user has premium plan
        $user = $this->user;
        if (!$user) return false;

        $plan = $user->activePlan();
        if (!$plan) return false;

        // Free plan cannot use timeline
        return !$plan->isFree();
    }

    /** Apakah gift section bisa ditampilkan */
    public function isGiftActive(): bool
    {
        // Premium: selalu aktif
        if ($this->template->isPremium()) return true;
        // Free: cek flag gift_enabled
        return (bool) $this->gift_enabled;
    }
    /**
     * Total slot foto yang sudah dibayar (dari gallery orders)
     */
    public function paidPhotoSlots(): int
    {
        return $this->galleryOrders()
            ->where('status', 'paid')
            ->sum('qty');
    }

    /**
     * Total slot foto yang tersedia (gratis + yang sudah dibeli)
     */
    public function totalPhotoSlots(): int|null
    {
        $limit = $this->template->free_photo_limit ?? null;
        if ($limit === null) return null; // unlimited

        return $limit + $this->paidPhotoSlots();
    }

    /**
     * Sisa slot foto yang bisa diupload
     */
    public function remainingPhotoSlots(): int|null
    {
        $total = $this->totalPhotoSlots();
        if ($total === null) return null; // unlimited

        $used = $this->gallery()->count();
        return max(0, $total - $used);
    }

    /**
     * Render pesan pengantar dengan mengganti placeholder:
     * {nama_tamu} → nama tamu
     * {link}      → URL undangan dengan ?to=slug_tamu
     */
    public function renderGreeting(Guest $guest): string
    {
        $url  = route('invitation.show', $this->slug) . '?to=' . urlencode($guest->slug);
        $text = $this->greeting ?? '';

        $text = str_replace('{nama_tamu}', $guest->name, $text);
        $text = str_replace('{link}', $url, $text);

        return $text;
    }

    /**
     * Ambil semua data undangan sebagai key-value array.
     * Contoh: ['groom_name' => 'Budi', 'bride_name' => 'Ani']
     */
    public function getDataMap(): array
    {
        return $this->data
            ->mapWithKeys(fn($d) => [$d->templateField->key => $d->value])
            ->toArray();
    }

    /**
     * Ambil nilai satu field berdasarkan key.
     */
    public function getValue(string $key, mixed $default = null): mixed
    {
        $item = $this->data->first(fn($d) => $d->templateField->key === $key);
        return $item ? $item->value : $default;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
