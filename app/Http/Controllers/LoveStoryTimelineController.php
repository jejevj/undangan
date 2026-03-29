<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\LoveStoryTimeline;
use Illuminate\Http\Request;

class LoveStoryTimelineController extends Controller
{
    /**
     * Get timeline items for invitation
     */
    public function index(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $timeline = $invitation->loveStoryTimeline;
        $canUseTimeline = $invitation->canUseTimelineMode();

        return view('love-story.index', compact('invitation', 'timeline', 'canUseTimeline'));
    }

    /**
     * Store new timeline item (AJAX)
     */
    public function store(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        // Check if user can use timeline mode
        if (!$invitation->canUseTimelineMode()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fitur Timeline hanya tersedia untuk paket Premium. Silakan upgrade paket Anda.'
                ], 403);
            }
            return redirect()->route('invitations.edit', $invitation)->with('error', 'Fitur Timeline hanya tersedia untuk paket Premium. Silakan upgrade paket Anda.');
        }

        $isTimeskip = $request->input('is_timeskip') == 1;
        
        if ($isTimeskip) {
            // Validation for timeskip
            $validated = $request->validate([
                'timeskip_label' => 'required|string|max:100',
                'message' => 'nullable|string|max:1000',
            ]);
            
            // Set defaults for timeskip
            $validated['sender'] = 'groom';
            $validated['is_timeskip'] = true;
            $validated['message'] = $validated['message'] ?? '';
            $validated['event_date'] = null;
            $validated['event_time'] = null;
            $validated['timeskip_label'] = $validated['timeskip_label'];
        } else {
            // Validation for normal timeline
            $validated = $request->validate([
                'sender' => 'required|in:groom,bride',
                'message' => 'required|string|max:1000',
                'event_date' => 'nullable|date',
                'event_time' => 'nullable|date_format:H:i',
            ]);
            
            // Set defaults for normal mode
            $validated['is_timeskip'] = false;
            $validated['timeskip_label'] = null;
        }

        // Get max order
        $maxOrder = $invitation->loveStoryTimeline()->withoutGlobalScope('ordered')->max('order') ?? 0;
        
        // Calculate order based on datetime if provided
        $calculatedOrder = $maxOrder + 1;
        
        if (!$isTimeskip && isset($validated['event_date'])) {
            // For items with date, calculate position based on datetime
            $eventDateTime = $validated['event_date'];
            if (isset($validated['event_time'])) {
                $eventDateTime .= ' ' . $validated['event_time'];
            } else {
                $eventDateTime .= ' 00:00:00';
            }
            
            // Count how many items have earlier datetime
            $earlierCount = $invitation->loveStoryTimeline()
                ->withoutGlobalScope('ordered')
                ->where(function($q) use ($eventDateTime) {
                    $q->whereRaw("CONCAT(COALESCE(event_date, '9999-12-31'), ' ', COALESCE(event_time, '00:00:00')) < ?", [$eventDateTime]);
                })
                ->count();
            
            $calculatedOrder = $earlierCount + 1;
            
            // Update order of items that should come after this
            $invitation->loveStoryTimeline()
                ->withoutGlobalScope('ordered')
                ->where('order', '>=', $calculatedOrder)
                ->increment('order');
        }

        $timeline = LoveStoryTimeline::create([
            'invitation_id' => $invitation->id,
            'sender' => $validated['sender'],
            'message' => $validated['message'] ?? '',
            'is_timeskip' => $validated['is_timeskip'],
            'timeskip_label' => $validated['timeskip_label'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'event_time' => $validated['event_time'] ?? null,
            'order' => $calculatedOrder,
        ]);

        // Return JSON for AJAX
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Timeline item berhasil ditambahkan.',
                'timeline' => [
                    'id' => $timeline->id,
                    'sender' => $timeline->sender,
                    'message' => $timeline->message,
                    'is_timeskip' => $timeline->is_timeskip,
                    'timeskip_label' => $timeline->timeskip_label,
                    'event_date' => $timeline->event_date ? $timeline->event_date->format('Y-m-d') : null,
                    'event_time' => $timeline->event_time,
                    'formatted_date_time' => $timeline->formatted_date_time,
                    'is_from_groom' => $timeline->isFromGroom(),
                    'is_from_bride' => $timeline->isFromBride(),
                ]
            ]);
        }

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Timeline item berhasil ditambahkan.');
    }

    /**
     * Update timeline item (AJAX)
     */
    public function update(Request $request, Invitation $invitation, LoveStoryTimeline $timeline)
    {
        $this->authorizeInvitation($invitation);

        if ($timeline->invitation_id !== $invitation->id) {
            abort(403);
        }

        $isTimeskip = $request->input('is_timeskip') == 1;
        
        if ($isTimeskip) {
            // Validation for timeskip
            $validated = $request->validate([
                'timeskip_label' => 'required|string|max:100',
                'message' => 'nullable|string|max:1000',
            ]);
            
            // Set defaults for timeskip
            $validated['sender'] = $timeline->sender;
            $validated['is_timeskip'] = true;
            $validated['message'] = $validated['message'] ?? '';
            $validated['event_date'] = null;
            $validated['event_time'] = null;
            $validated['timeskip_label'] = $validated['timeskip_label'];
        } else {
            // Validation for normal timeline
            $validated = $request->validate([
                'sender' => 'required|in:groom,bride',
                'message' => 'required|string|max:1000',
                'event_date' => 'nullable|date',
                'event_time' => 'nullable|date_format:H:i',
            ]);
            
            // Set defaults for normal mode
            $validated['is_timeskip'] = false;
            $validated['timeskip_label'] = null;
        }

        $timeline->update([
            'sender' => $validated['sender'],
            'message' => $validated['message'] ?? '',
            'is_timeskip' => $validated['is_timeskip'],
            'timeskip_label' => $validated['timeskip_label'] ?? null,
            'event_date' => $validated['event_date'] ?? null,
            'event_time' => $validated['event_time'] ?? null,
        ]);

        // Return JSON for AJAX
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Timeline item berhasil diupdate.',
                'timeline' => [
                    'id' => $timeline->id,
                    'sender' => $timeline->sender,
                    'message' => $timeline->message,
                    'is_timeskip' => $timeline->is_timeskip,
                    'timeskip_label' => $timeline->timeskip_label,
                    'event_date' => $timeline->event_date ? $timeline->event_date->format('Y-m-d') : null,
                    'event_time' => $timeline->event_time,
                    'formatted_date_time' => $timeline->formatted_date_time,
                    'is_from_groom' => $timeline->isFromGroom(),
                    'is_from_bride' => $timeline->isFromBride(),
                ]
            ]);
        }

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Timeline item berhasil diupdate.');
    }

    /**
     * Delete timeline item (AJAX)
     */
    public function destroy(Invitation $invitation, LoveStoryTimeline $timeline)
    {
        $this->authorizeInvitation($invitation);

        if ($timeline->invitation_id !== $invitation->id) {
            abort(403);
        }

        $timeline->delete();

        // Return JSON for AJAX
        if (request()->expectsJson() || request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Timeline item berhasil dihapus.'
            ]);
        }

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Timeline item berhasil dihapus.');
    }

    /**
     * Update timeline order
     */
    public function updateOrder(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $request->validate([
            'timeline_ids' => 'required|array',
            'timeline_ids.*' => 'exists:love_story_timeline,id',
        ]);

        foreach ($request->timeline_ids as $order => $timelineId) {
            LoveStoryTimeline::where('id', $timelineId)
                ->where('invitation_id', $invitation->id)
                ->update(['order' => $order]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan timeline berhasil diupdate.']);
    }

    /**
     * Switch love story mode
     */
    public function switchMode(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $request->validate([
            'mode' => 'required|in:longtext,timeline',
        ]);

        // Check if user can use timeline mode
        if ($request->mode === 'timeline' && !$invitation->canUseTimelineMode()) {
            return redirect()->route('invitations.edit', $invitation)->with('error', 'Fitur Timeline hanya tersedia untuk paket Premium. Silakan upgrade paket Anda.');
        }

        $invitation->update([
            'love_story_mode' => $request->mode,
        ]);

        return redirect()->route('invitations.edit', $invitation)->with('success', 'Mode cerita cinta berhasil diubah ke ' . ($request->mode === 'timeline' ? 'Timeline' : 'Long Text') . '.');
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }
}
