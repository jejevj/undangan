<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TemplateField extends Model
{
    protected $fillable = [
        'template_id', 'key', 'label', 'type', 'options',
        'required', 'placeholder', 'default_value', 'group', 'order'
    ];

    protected $casts = [
        'required' => 'boolean',
        'options'  => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }
}
