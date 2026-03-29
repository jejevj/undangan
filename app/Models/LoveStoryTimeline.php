<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoveStoryTimeline extends Model
{
    protected $table = 'love_story_timeline';

    protected $fillable = [
        'invitation_id',
        'sender',
        'message',
        'is_timeskip',
        'timeskip_label',
        'event_date',
        'event_time',
        'order',
    ];

    protected $casts = [
        'event_date' => 'date',
        'is_timeskip' => 'boolean',
    ];

    /**
     * Default ordering by order field (manual), then by datetime
     */
    protected static function booted()
    {
        static::addGlobalScope('ordered', function ($builder) {
            $builder->orderBy('order', 'asc')
                    ->orderByRaw('
                        CASE 
                            WHEN event_date IS NOT NULL AND event_time IS NOT NULL 
                            THEN CONCAT(event_date, " ", event_time)
                            WHEN event_date IS NOT NULL 
                            THEN CONCAT(event_date, " 00:00:00")
                            ELSE "9999-12-31 23:59:59"
                        END ASC
                    ');
        });
    }

    public function invitation(): BelongsTo
    {
        return $this->belongsTo(Invitation::class);
    }

    /**
     * Get formatted date time
     */
    public function getFormattedDateTimeAttribute(): string
    {
        if (!$this->event_date) {
            return '';
        }

        $formatted = $this->event_date->translatedFormat('d M Y');
        
        if ($this->event_time) {
            $formatted .= ' • ' . substr($this->event_time, 0, 5);
        }

        return $formatted;
    }

    /**
     * Check if sender is groom
     */
    public function isFromGroom(): bool
    {
        return $this->sender === 'groom';
    }

    /**
     * Check if sender is bride
     */
    public function isFromBride(): bool
    {
        return $this->sender === 'bride';
    }
}
