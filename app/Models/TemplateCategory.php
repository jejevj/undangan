<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TemplateCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function templates(): HasMany
    {
        return $this->hasMany(Template::class, 'category_id');
    }

    public function activeTemplates(): HasMany
    {
        return $this->templates()->where('is_active', true);
    }
}
