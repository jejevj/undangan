<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id', 'pricing_plan_id', 'order_number', 'amount',
        'status', 'payment_method', 'starts_at', 'expires_at', 'paid_at',
    ];

    protected $casts = [
        'starts_at'  => 'datetime',
        'expires_at' => 'datetime',
        'paid_at'    => 'datetime',
    ];

    public function user(): BelongsTo      { return $this->belongsTo(User::class); }
    public function plan(): BelongsTo      { return $this->belongsTo(PricingPlan::class, 'pricing_plan_id'); }
    public function isActive(): bool       { return $this->status === 'active'; }
    public function isPaid(): bool         { return $this->status === 'active'; }

    public static function generateOrderNumber(): string
    {
        return 'SUB-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}
