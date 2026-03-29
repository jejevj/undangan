<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationGallery;
use App\Models\UserGalleryPhoto;
use App\Models\GalleryOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class GalleryController extends Controller
{
    /**
     * Gallery management page for invitation
     * Shows user's photo pool and selected photos for this invitation
     */
    public function index(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);
        $invitation->load(['gallery.photo', 'template']);

        // Get user's gallery slots
        $userSlots = auth()->user()->getGallerySlots();
        $total = $userSlots->totalSlots();
        $used = $userSlots->usedSlots();
        $remaining = $userSlots->remainingSlots();

        // Get all user's photos (photo pool)
        $allUserPhotos = auth()->user()->galleryPhotos()->latest()->get();
        
        // Get selected photo IDs for this invitation
        $selectedPhotoIds = $invitation->selectedPhotos()->pluck('user_gallery_photos.id')->toArray();

        return view('gallery.index', compact('invitation', 'total', 'used', 'remaining', 'allUserPhotos', 'selectedPhotoIds'));
    }

    /**
     * Upload new photos to user's photo pool
     */
    public function store(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        // Check available slots (user-level)
        $remaining = auth()->user()->remainingGallerySlots();
        if ($remaining <= 0) {
            return back()->with('error', 'Slot foto habis. Beli slot tambahan terlebih dahulu.');
        }

        $request->validate([
            'photos'    => 'required|array|min:1',
            'photos.*'  => 'image|max:5120', // 5MB per foto
            'captions'  => 'nullable|array',
            'captions.*' => 'nullable|string|max:100',
        ]);

        $uploadedPhotos = [];

        foreach ($request->file('photos') as $i => $photo) {
            // Check slot per photo if uploading multiple
            $rem = auth()->user()->remainingGallerySlots();
            if ($rem <= 0) break;

            $path = $photo->store('gallery/' . auth()->id(), 'public');
            
            // Determine if this is paid slot
            $userSlots = auth()->user()->getGallerySlots();
            $freeRemaining = $userSlots->free_slots - auth()->user()->galleryPhotos()->where('is_paid', false)->count();
            $isPaid = $freeRemaining <= 0;
            
            $uploadedPhoto = UserGalleryPhoto::create([
                'user_id' => auth()->id(),
                'path' => $path,
                'caption' => $request->captions[$i] ?? null,
                'is_paid' => $isPaid,
            ]);

            $uploadedPhotos[] = $uploadedPhoto->id;
        }

        return back()->with('success', count($uploadedPhotos) . ' foto berhasil diupload ke gallery Anda.');
    }

    /**
     * Delete photo from user's pool
     * Will also remove from all invitations using it
     */
    public function destroy(Invitation $invitation, UserGalleryPhoto $photo)
    {
        $this->authorizeInvitation($invitation);
        
        // Ensure photo belongs to logged in user
        if ($photo->user_id !== auth()->id()) {
            abort(403);
        }
        
        Storage::disk('public')->delete($photo->path);
        $photo->delete(); // Cascade will remove from invitation_gallery
        
        return back()->with('success', 'Foto dihapus dari gallery Anda.');
    }

    /**
     * Select/assign photos to invitation (multiple selection with ordering)
     */
    public function selectPhotos(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'exists:user_gallery_photos,id',
        ]);

        $photoIds = $request->photo_ids;

        // Verify all photos belong to user
        $userPhotos = auth()->user()->galleryPhotos()->whereIn('id', $photoIds)->pluck('id')->toArray();
        if (count($userPhotos) !== count($photoIds)) {
            return response()->json([
                'success' => false,
                'message' => 'Beberapa foto tidak valid.'
            ], 400);
        }

        DB::transaction(function () use ($invitation, $photoIds) {
            // Remove all current selections
            $invitation->gallery()->delete();

            // Add new selections with order
            foreach ($photoIds as $order => $photoId) {
                InvitationGallery::create([
                    'invitation_id' => $invitation->id,
                    'photo_id' => $photoId,
                    'order' => $order,
                ]);
            }
        });

        return response()->json([
            'success' => true,
            'message' => count($photoIds) . ' foto dipilih untuk undangan ini.'
        ]);
    }

    /**
     * Update photo order for invitation
     */
    public function updateOrder(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $request->validate([
            'photo_ids' => 'required|array',
            'photo_ids.*' => 'exists:user_gallery_photos,id',
        ]);

        DB::transaction(function () use ($invitation, $request) {
            foreach ($request->photo_ids as $order => $photoId) {
                InvitationGallery::where('invitation_id', $invitation->id)
                    ->where('photo_id', $photoId)
                    ->update(['order' => $order]);
            }
        });

        return response()->json(['success' => true, 'message' => 'Urutan foto berhasil diupdate.']);
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }
}
