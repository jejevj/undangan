<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGallerySlot extends Model
{
    protected $fillable = [
        'user_id',
        'free_slots',
        'purchased_slots',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Total slot yang dimiliki user
     */
    public function totalSlots(): int
    {
        return $this->free_slots + $this->purchased_slots;
    }

    /**
     * Slot yang sudah terpakai
     */
    public function usedSlots(): int
    {
        return UserGalleryPhoto::where('user_id', $this->user_id)->count();
    }

    /**
     * Slot yang tersisa
     */
    public function remainingSlots(): int
    {
        return max(0, $this->totalSlots() - $this->usedSlots());
    }

    /**
     * Tambah slot yang dibeli
     */
    public function addPurchasedSlots(int $qty): void
    {
        $this->increment('purchased_slots', $qty);
    }

    /**
     * Get or create slot record for user
     */
    public static function getOrCreateForUser(User $user): self
    {
        return static::firstOrCreate(
            ['user_id' => $user->id],
            [
                'free_slots' => 10, // Default free slots
                'purchased_slots' => 0,
            ]
        );
    }
}
