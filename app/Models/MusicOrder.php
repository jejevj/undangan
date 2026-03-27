<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicOrder extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'music_id', 'amount',
        'status', 'payment_method', 'paid_at', 'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount'  => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function music(): BelongsTo
    {
        return $this->belongsTo(Music::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public static function generateOrderNumber(): string
    {
        return 'INV-MUSIC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
