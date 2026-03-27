<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GalleryOrder extends Model
{
    protected $fillable = [
        'order_number', 'invitation_id', 'user_id',
        'qty', 'amount', 'price_per_photo',
        'status', 'payment_method', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public static function generateOrderNumber(): string
    {
        return 'INV-PHOTO-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
