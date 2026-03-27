<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invitation extends Model
{
    protected $fillable = [
        'user_id', 'template_id', 'slug', 'title', 'status', 'published_at', 'expired_at'
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'expired_at'   => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function data(): HasMany
    {
        return $this->hasMany(InvitationData::class);
    }

    /**
     * Ambil semua data undangan sebagai key-value array.
     * Contoh: ['groom_name' => 'Budi', 'bride_name' => 'Ani']
     */
    public function getDataMap(): array
    {
        return $this->data
            ->mapWithKeys(fn($d) => [$d->templateField->key => $d->value])
            ->toArray();
    }

    /**
     * Ambil nilai satu field berdasarkan key.
     */
    public function getValue(string $key, mixed $default = null): mixed
    {
        $item = $this->data->first(fn($d) => $d->templateField->key === $key);
        return $item ? $item->value : $default;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
