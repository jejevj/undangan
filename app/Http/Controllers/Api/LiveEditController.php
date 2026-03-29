<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invitation;
use App\Models\InvitationData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LiveEditController extends Controller
{
    /**
     * Update single field via AJAX
     */
    public function updateField(Request $request, Invitation $invitation)
    {
        \Log::info('LiveEdit updateField called', [
            'invitation_id' => $invitation->id,
            'user_id' => auth()->id(),
            'field_key' => $request->field_key,
            'value' => substr($request->value ?? '', 0, 100),
        ]);
        
        // Authorization
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            \Log::warning('LiveEdit unauthorized access attempt', [
                'invitation_id' => $invitation->id,
                'invitation_owner' => $invitation->user_id,
                'user_id' => auth()->id(),
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'field_key' => 'required|string',
            'value' => 'nullable',
        ]);

        $fieldKey = $request->field_key;
        $value = $request->value;

        // Find template field
        $templateField = $invitation->template->fields()->where('key', $fieldKey)->first();
        
        if (!$templateField) {
            \Log::error('LiveEdit field not found', [
                'invitation_id' => $invitation->id,
                'field_key' => $fieldKey,
            ]);
            return response()->json(['error' => 'Field not found'], 404);
        }

        // Handle image upload
        if ($templateField->type === 'image' && $request->hasFile('value')) {
            $file = $request->file('value');
            $path = $file->store('invitations/' . $invitation->id, 'public');
            $value = $path;
        }

        // Update or create invitation data
        InvitationData::updateOrCreate(
            [
                'invitation_id' => $invitation->id,
                'template_field_id' => $templateField->id,
            ],
            [
                'value' => $value,
            ]
        );

        \Log::info('LiveEdit field updated successfully', [
            'invitation_id' => $invitation->id,
            'field_key' => $fieldKey,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Field updated successfully',
            'field_key' => $fieldKey,
            'value' => $value,
        ]);
    }

    /**
     * Get current field value
     */
    public function getField(Invitation $invitation, string $fieldKey)
    {
        // Authorization
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $value = $invitation->getValue($fieldKey);

        return response()->json([
            'success' => true,
            'field_key' => $fieldKey,
            'value' => $value,
        ]);
    }

    /**
     * Get all template fields with current values
     */
    public function getAllFields(Invitation $invitation)
    {
        // Authorization
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $invitation->load(['data.templateField', 'template.fields']);
        
        $fields = $invitation->template->fields->map(function($field) use ($invitation) {
            return [
                'key' => $field->key,
                'label' => $field->label,
                'type' => $field->type,
                'value' => $invitation->getValue($field->key),
                'options' => $field->options,
            ];
        });

        return response()->json([
            'success' => true,
            'fields' => $fields,
        ]);
    }

    /**
     * Get user's gallery photos
     */
    public function getUserPhotos(Invitation $invitation)
    {
        // Authorization
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $photos = auth()->user()->galleryPhotos()->latest()->get()->map(function($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'path' => $photo->path,
                'caption' => $photo->caption,
            ];
        });

        return response()->json([
            'success' => true,
            'photos' => $photos,
        ]);
    }

    /**
     * Get user's music
     */
    public function getUserMusic(Invitation $invitation)
    {
        // Authorization
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $user = auth()->user();

        // Get music that user owns (uploaded, free, or purchased)
        $ownedMusic = \App\Models\Music::where('is_active', true)
            ->where(function($query) use ($user) {
                // Music uploaded by user
                $query->where('uploaded_by', $user->id)
                      // Free music
                      ->orWhere('type', 'free')
                      // Purchased music
                      ->orWhereHas('users', function($q) use ($user) {
                          $q->where('user_id', $user->id);
                      });
            })
            ->get()
            ->map(function($music) {
                return [
                    'id' => $music->id,
                    'title' => $music->title,
                    'artist' => $music->artist,
                    'file_path' => $music->file_path,
                    'type' => $music->type,
                ];
            });

        return response()->json([
            'success' => true,
            'music' => $ownedMusic,
        ]);
    }

    /**
     * Get gallery photos for this invitation
     */
    public function getGalleryPhotos(Invitation $invitation)
    {
        // Authorization
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Get all user's photos
        $allPhotos = auth()->user()->galleryPhotos()->latest()->get()->map(function($photo) {
            return [
                'id' => $photo->id,
                'url' => $photo->url,
                'path' => $photo->path,
                'caption' => $photo->caption,
            ];
        });

        // Get selected photo IDs for this invitation
        $selectedPhotoIds = $invitation->selectedPhotos()->pluck('user_gallery_photos.id')->toArray();

        return response()->json([
            'success' => true,
            'allPhotos' => $allPhotos,
            'selectedPhotoIds' => $selectedPhotoIds,
        ]);
    }

    /**
     * Update gallery selection for invitation
     */
    public function updateGallerySelection(Request $request, Invitation $invitation)
    {
        // Authorization
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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

        \DB::transaction(function () use ($invitation, $photoIds) {
            // Remove all current selections
            $invitation->gallery()->delete();

            // Add new selections with order
            foreach ($photoIds as $order => $photoId) {
                \App\Models\InvitationGallery::create([
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
}
