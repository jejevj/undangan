<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicUploadOrder extends Model
{
    protected $fillable = [
        'user_id', 'order_number', 'amount', 'status', 'payment_method',
        'paid_at', 'temp_title', 'temp_artist', 'temp_file_path', 'music_id',
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

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function formattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public static function generateOrderNumber(): string
    {
        return 'MUP-' . strtoupper(uniqid());
    }
}
