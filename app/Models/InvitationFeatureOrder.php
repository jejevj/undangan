<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvitationFeatureOrder extends Model
{
    protected $fillable = [
        'order_number', 'invitation_id', 'user_id',
        'feature', 'amount', 'status', 'payment_method', 'paid_at',
    ];

    protected $casts = ['paid_at' => 'datetime'];

    public function invitation(): BelongsTo { return $this->belongsTo(Invitation::class); }
    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function isPaid(): bool         { return $this->status === 'paid'; }

    public static function generateOrderNumber(): string
    {
        return 'INV-FEAT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
