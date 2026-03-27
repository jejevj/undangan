<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationGallery extends Model
{
    protected $table = 'invitation_gallery';

    protected $fillable = ['invitation_id', 'path', 'caption', 'order', 'is_paid'];

    protected $casts = ['is_paid' => 'boolean'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function url(): string
    {
        return asset('storage/' . $this->path);
    }
}
