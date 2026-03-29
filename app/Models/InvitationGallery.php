<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationGallery extends Model
{
    protected $table = 'invitation_gallery';

    protected $fillable = ['invitation_id', 'photo_id', 'order'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function photo(): BelongsTo
    {
        return $this->belongsTo(UserGalleryPhoto::class, 'photo_id');
    }

    /**
     * Get photo URL (shortcut)
     */
    public function url(): string
    {
        return $this->photo ? $this->photo->url : '';
    }
}
