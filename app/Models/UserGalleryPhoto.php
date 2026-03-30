<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class UserGalleryPhoto extends Model
{
    protected $fillable = [
        'user_id',
        'path',
        'caption',
        'is_paid',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    /**
     * User yang memiliki foto ini
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Invitations yang menggunakan foto ini
     */
    public function invitations()
    {
        return $this->belongsToMany(Invitation::class, 'invitation_gallery', 'photo_id', 'invitation_id')
            ->withPivot('order')
            ->withTimestamps()
            ->orderBy('invitation_gallery.order');
    }

    /**
     * Get full URL of photo
     */
    public function getUrlAttribute()
    {
        // Check if path is external URL
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return $this->path;
        }
        
        return Storage::url($this->path);
    }

    /**
     * Get full path for storage operations
     */
    public function getFullPathAttribute()
    {
        return storage_path('app/public/' . $this->path);
    }

    /**
     * Check if photo is used in any invitation
     */
    public function isUsed(): bool
    {
        return $this->invitations()->exists();
    }

    /**
     * Get count of invitations using this photo
     */
    public function usageCount(): int
    {
        return $this->invitations()->count();
    }
}
