<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuestMessage extends Model
{
    protected $fillable = [
        'invitation_id',
        'guest_name',
        'message',
        'likes_count',
        'ip_address',
        'is_approved',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'likes_count' => 'integer',
    ];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * Format guest name untuk display
     */
    public function getFormattedNameAttribute(): string
    {
        return ucwords(str_replace('-', ' ', $this->guest_name));
    }

    /**
     * Get initials (2 huruf pertama dari nama)
     */
    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->formatted_name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->formatted_name, 0, 2));
    }
}
