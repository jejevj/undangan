<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Template extends Model
{
    protected $fillable = [
        'name', 'slug', 'thumbnail', 'blade_view', 'description', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(TemplateField::class)->orderBy('order');
    }

    public function fieldsByGroup(): \Illuminate\Support\Collection
    {
        return $this->fields->groupBy('group');
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }
}
