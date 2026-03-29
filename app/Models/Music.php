<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Music extends Model
{
    protected $table = 'music';

    protected $fillable = [
        'title', 'artist', 'file_path', 'duration',
        'type', 'price', 'cover', 'is_active', 'uploaded_by', 'is_paid_upload',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price'     => 'integer',
    ];

    public function isFree(): bool
    {
        return $this->type === 'free';
    }

    /** Lagu yang diupload oleh user (bukan admin) */
    public function isUserUpload(): bool
    {
        return $this->uploaded_by !== null;
    }

    public function formattedPrice(): string
    {
        return $this->isFree() ? 'Gratis' : 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function audioUrl(): string
    {
        // Lagu sistem: public/invitation-assets/music/
        // Lagu user upload: storage/app/public/music-uploads/
        if ($this->isUserUpload()) {
            return asset('storage/' . $this->file_path);
        }
        return asset('invitation-assets/music/' . basename($this->file_path));
    }

    /**
     * Ambil semua lagu yang bisa diakses oleh user tertentu:
     * 1. Lagu gratis (sistem)
     * 2. Lagu premium yang sudah dibeli user
     * 3. Lagu yang diupload sendiri oleh user
     * 4. Lagu premium gratis untuk paket Basic/Pro
     */
    public static function accessibleByUser(User $user): \Illuminate\Database\Eloquent\Collection
    {
        $purchasedIds = $user->musicLibrary()->pluck('music_id')->toArray();
        $plan = $user->activePlan();
        
        // Basic dan Pro bisa akses semua lagu premium gratis
        $hasPremiumAccess = in_array($plan->slug, ['basic', 'pro']) || $user->isAdmin();

        return static::where('is_active', true)
            ->where(function ($q) use ($user, $purchasedIds, $hasPremiumAccess) {
                $q->where('type', 'free')                          // gratis
                  ->orWhereIn('id', $purchasedIds)                 // sudah dibeli
                  ->orWhere('uploaded_by', $user->id);             // upload sendiri
                
                // Jika punya akses premium (Basic/Pro/Admin), tambahkan semua lagu premium
                if ($hasPremiumAccess) {
                    $q->orWhere('type', 'premium');
                }
            })
            ->orderByRaw("CASE WHEN uploaded_by = {$user->id} THEN 0 ELSE 1 END") // upload sendiri di atas
            ->orderBy('type')
            ->orderBy('title')
            ->get();
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'music_user')
                    ->withPivot('granted_at')
                    ->withTimestamps();
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MusicOrder::class);
    }
}
