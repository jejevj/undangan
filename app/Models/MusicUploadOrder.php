<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MusicUploadOrder extends Model
{
    protected $fillable = [
        'order_number',
        'user_id',
        'qty',
        'amount',
        'price_per_slot',
        'admin_fee',
        'status',
        'payment_method',
        'payment_channel_id',
        'va_number',
        'payment_url',
        'qr_string',
        'qr_url',
        'paid_at',
        'expired_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentChannel(): BelongsTo
    {
        return $this->belongsTo(PaymentChannel::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public static function generateOrderNumber(): string
    {
        return 'MUSIC-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
