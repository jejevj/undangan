<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\GuestMessage;
use Illuminate\Http\Request;

class GuestMessageController extends Controller
{
    /**
     * Store guest message (public, no auth required)
     */
    public function store(Request $request, string $slug)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        $request->validate([
            'guest_name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
        ]);

        $guestMessage = GuestMessage::create([
            'invitation_id' => $invitation->id,
            'guest_name' => $request->guest_name,
            'message' => $request->message,
            'ip_address' => $request->ip(),
            'is_approved' => true, // Auto-approve
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Terima kasih! Pesan Anda telah terkirim.',
            'data' => [
                'id' => $guestMessage->id,
                'guest_name' => $guestMessage->formatted_name,
                'initials' => $guestMessage->initials,
                'message' => $guestMessage->message,
                'likes_count' => $guestMessage->likes_count,
                'created_at' => $guestMessage->created_at->diffForHumans(),
            ]
        ]);
    }

    /**
     * Like a guest message (public, no auth required)
     */
    public function like(Request $request, string $slug, GuestMessage $message)
    {
        $invitation = Invitation::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        if ($message->invitation_id !== $invitation->id) {
            abort(404);
        }

        // Increment likes
        $message->increment('likes_count');

        return response()->json([
            'success' => true,
            'likes_count' => $message->likes_count,
        ]);
    }
}
