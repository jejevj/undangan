<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationGallery;
use App\Models\GalleryOrder;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);
        $invitation->load(['gallery', 'template', 'galleryOrders']);

        $limit     = $invitation->template->free_photo_limit;
        $total     = $invitation->totalPhotoSlots();
        $used      = $invitation->gallery()->count();
        $remaining = $invitation->remainingPhotoSlots();

        return view('gallery.index', compact('invitation', 'limit', 'total', 'used', 'remaining'));
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        // Cek slot tersedia
        $remaining = $invitation->remainingPhotoSlots();
        if ($remaining !== null && $remaining <= 0) {
            return back()->with('error', 'Slot foto habis. Beli slot tambahan terlebih dahulu.');
        }

        $request->validate([
            'photos'          => 'required|array|min:1',
            'photos.*'        => 'image|max:5120', // 5MB per foto
            'captions'        => 'nullable|array',
            'captions.*'      => 'nullable|string|max:100',
        ]);

        $order = $invitation->gallery()->count();
        foreach ($request->file('photos') as $i => $photo) {
            // Cek lagi per foto jika upload banyak sekaligus
            $rem = $invitation->remainingPhotoSlots();
            if ($rem !== null && $rem <= 0) break;

            $path = $photo->store('gallery/' . $invitation->id, 'public');
            $invitation->gallery()->create([
                'path'    => $path,
                'caption' => $request->captions[$i] ?? null,
                'order'   => $order++,
                'is_paid' => false,
            ]);
        }

        return back()->with('success', 'Foto berhasil diupload.');
    }

    public function destroy(Invitation $invitation, InvitationGallery $photo)
    {
        $this->authorizeInvitation($invitation);
        \Illuminate\Support\Facades\Storage::disk('public')->delete($photo->path);
        $photo->delete();
        return back()->with('success', 'Foto dihapus.');
    }

    /**
     * Halaman beli slot foto tambahan (simulasi)
     */
    public function buySlots(Request $request, Invitation $invitation)
    {
        $this->authorizeInvitation($invitation);

        $request->validate(['qty' => 'required|integer|min:1|max:20']);

        $qty   = (int) $request->qty;
        $price = $invitation->template->extra_photo_price;
        $total = $qty * $price;

        $order = GalleryOrder::create([
            'order_number'    => GalleryOrder::generateOrderNumber(),
            'invitation_id'   => $invitation->id,
            'user_id'         => auth()->id(),
            'qty'             => $qty,
            'amount'          => $total,
            'price_per_photo' => $price,
            'status'          => 'pending',
            'payment_method'  => 'simulation',
        ]);

        return view('gallery.buy', compact('invitation', 'order'));
    }

    /**
     * Simulasi pembayaran slot foto
     */
    public function simulatePay(GalleryOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if($order->isPaid(), 400);

        $order->update(['status' => 'paid', 'paid_at' => now()]);

        return redirect()->route('invitations.gallery.index', $order->invitation)
            ->with('success', "{$order->qty} slot foto berhasil ditambahkan!");
    }

    private function authorizeInvitation(Invitation $invitation): void
    {
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }
}
