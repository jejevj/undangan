<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    protected $fillable = [
        'name', 'slug', 'url', 'icon', 'parent_id', 'order', 'is_active', 'permission_name'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(Menu::class, 'parent_id')->orderBy('order');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'parent_id');
    }

    public static function getMenuTree()
    {
        return static::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->with(['children' => function ($q) {
                $q->where('is_active', true)->orderBy('order');
            }])
            ->get();
    }
}
