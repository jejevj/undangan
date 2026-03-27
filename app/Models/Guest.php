<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Guest extends Model
{
    protected $fillable = [
        'invitation_id', 'name', 'slug', 'phone_code', 'phone', 'notes', 'is_attending',
    ];

    protected $casts = [
        'is_attending' => 'boolean',
    ];

    /**
     * Nomor WA lengkap: hilangkan leading 0, gabung kode negara
     * Contoh: +62, 08123456789 → 628123456789
     */
    public function getWhatsappNumber(): ?string
    {
        if (!$this->phone) return null;

        $code   = preg_replace('/[^0-9]/', '', $this->phone_code ?? '62');
        $number = preg_replace('/[^0-9]/', '', $this->phone);
        $number = ltrim($number, '0');

        return $code . $number;
    }

    protected static function booted(): void
    {
        static::creating(function (Guest $guest) {
            if (empty($guest->slug)) {
                $guest->slug = static::generateSlug($guest->name, $guest->invitation_id);
            }
        });
    }

    public static function generateSlug(string $name, int $invitationId): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (static::where('invitation_id', $invitationId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }
}
