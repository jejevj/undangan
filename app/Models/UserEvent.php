<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserEvent extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'event_name',
        'event_category',
        'event_label',
        'event_data',
        'page_url',
        'referrer',
        'user_agent',
        'ip_address',
        'created_at',
    ];

    protected $casts = [
        'event_data' => 'array',
        'created_at' => 'datetime',
    ];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for specific event category
     */
    public function scopeCategory($query, string $category)
    {
        return $query->where('event_category', $category);
    }

    /**
     * Scope for specific event name
     */
    public function scopeEventName($query, string $name)
    {
        return $query->where('event_name', $name);
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}
