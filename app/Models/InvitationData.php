<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationData extends Model
{
    protected $fillable = ['invitation_id', 'template_field_id', 'value'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function templateField(): BelongsTo
    {
        return $this->belongsTo(TemplateField::class);
    }
}
