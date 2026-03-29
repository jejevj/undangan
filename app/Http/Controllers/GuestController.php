<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Invitation;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function index(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);
        $guests = $invitation->guests()->get();
        return view('guests.index', compact('invitation', 'guests'));
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        // Cek batas tamu untuk template free
        $template = $invitation->template;
        if ($template->hasGuestLimit()) {
            $current = $invitation->guests()->count();
            if ($current >= $template->guest_limit) {
                return back()->with('error',
                    "Batas maksimal tamu untuk template {$template->name} adalah {$template->guest_limit} orang."
                );
            }
        }

        $request->validate([
            'name'       => 'required|string|max:255',
            'phone_code' => 'nullable|string|max:10',
            'phone'      => 'nullable|string|max:20',
            'notes'      => 'nullable|string|max:500',
        ]);

        $invitation->guests()->create($request->only('name', 'phone_code', 'phone', 'notes'));

        return redirect()->route('invitations.guests.index', $invitation)
            ->with('success', 'Tamu berhasil ditambahkan.');
    }

    public function update(Request $request, Invitation $invitation, Guest $guest)
    {
        $this->authorizeInvitation($invitation);

        $request->validate([
            'name'       => 'required|string|max:255',
            'phone_code' => 'nullable|string|max:10',
            'phone'      => 'nullable|string|max:20',
            'notes'      => 'nullable|string|max:500',
        ]);

        $guest->update([
            'name'       => $request->name,
            'phone_code' => $request->phone_code,
            'phone'      => $request->phone,
            'notes'      => $request->notes,
            'slug'       => Guest::generateSlug($request->name, $invitation->id),
        ]);

        return redirect()->route('invitations.guests.index', $invitation)
            ->with('success', 'Data tamu berhasil diupdate.');
    }

    public function destroy(Invitation $invitation, Guest $guest)
    {
        $this->authorizeInvitation($invitation);
        $guest->delete();
        return redirect()->route('invitations.guests.index', $invitation)
            ->with('success', 'Tamu berhasil dihapus.');
    }

    /**
     * Return rendered greeting + WA URL untuk tamu tertentu
     */
    public function greeting(Invitation $invitation, Guest $guest)
    {
        $this->authorizeInvitation($invitation);

        $invitation->load(['data.templateField']);
        
        // Format: nama-tamu-disini (slugified from name)
        $toParam = \Illuminate\Support\Str::slug($guest->name);
        $link    = route('invitation.show', $invitation->slug) . '?to=' . urlencode($toParam);
        
        // Replace {link} in greeting message
        $message = $this->renderGreetingMessage($invitation, $guest, $link);
        $waUrl   = $this->buildWhatsappUrl($guest, $message);

        return response()->json([
            'name'      => $guest->name,
            'message'   => $message,
            'link'      => $link,
            'wa_url'    => $waUrl,
            'has_phone' => (bool) $guest->getWhatsappNumber(),
        ]);
    }

    /**
     * Render greeting message with placeholders
     */
    private function renderGreetingMessage(Invitation $invitation, Guest $guest, string $link): string
    {
        $text = $invitation->greeting ?? '';
        $text = str_replace('{nama_tamu}', $guest->name, $text);
        $text = str_replace('{link}', $link, $text);
        return $text;
    }

    /**
     * Build WhatsApp URL: wa.me/{number}?text={encoded_message}
     */
    private function buildWhatsappUrl(Guest $guest, string $message): ?string
    {
        $number = $guest->getWhatsappNumber();
        if (!$number) return null;

        return 'https://wa.me/' . $number . '?text=' . rawurlencode($message);
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }
}
