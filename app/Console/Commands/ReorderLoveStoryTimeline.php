<?php

namespace App\Console\Commands;

use App\Models\Invitation;
use App\Models\LoveStoryTimeline;
use Illuminate\Console\Command;

class ReorderLoveStoryTimeline extends Command
{
    protected $signature = 'timeline:reorder {invitation_id?}';
    protected $description = 'Reorder love story timeline based on datetime';

    public function handle()
    {
        $invitationId = $this->argument('invitation_id');
        
        if ($invitationId) {
            $invitations = Invitation::where('id', $invitationId)->get();
        } else {
            $invitations = Invitation::where('love_story_mode', 'timeline')->get();
        }
        
        $this->info("Processing {$invitations->count()} invitations...");
        
        foreach ($invitations as $invitation) {
            $this->info("Reordering timeline for invitation #{$invitation->id}");
            
            // Get all timeline items ordered by datetime
            $items = $invitation->loveStoryTimeline()
                ->withoutGlobalScope('ordered')
                ->orderByRaw('
                    CASE 
                        WHEN event_date IS NOT NULL AND event_time IS NOT NULL 
                        THEN CONCAT(event_date, " ", event_time)
                        WHEN event_date IS NOT NULL 
                        THEN CONCAT(event_date, " 00:00:00")
                        ELSE "9999-12-31 23:59:59"
                    END ASC
                ')
                ->orderBy('id', 'asc')
                ->get();
            
            // Update order field
            $order = 1;
            foreach ($items as $item) {
                $item->update(['order' => $order]);
                $order++;
            }
            
            $this->info("  ✓ Reordered {$items->count()} items");
        }
        
        $this->info("Done!");
        
        return 0;
    }
}
