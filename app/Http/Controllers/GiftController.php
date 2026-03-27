<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\InvitationBankAccount;
use App\Models\InvitationFeatureOrder;
use Illuminate\Http\Request;

class GiftController extends Controller
{
    public function index(Invitation $invitation)
    {
        $this->authorize($invitation);
        $invitation->load(['bankAccounts', 'template', 'featureOrders']);

        $needsPayment = !$invitation->isGiftActive()
                     && $invitation->template->gift_feature_price > 0;

        return view('gift.index', compact('invitation', 'needsPayment'));
    }

    public function store(Request $request, Invitation $invitation)
    {
        $this->authorize($invitation);
        $this->requireGiftActive($invitation);

        $request->validate([
            'bank_name'      => 'required|string|max:50',
            'account_number' => 'required|string|max:30',
            'account_name'   => 'required|string|max:100',
        ]);

        $invitation->bankAccounts()->create([
            'bank_name'      => $request->bank_name,
            'account_number' => $request->account_number,
            'account_name'   => $request->account_name,
            'order'          => $invitation->bankAccounts()->count(),
        ]);

        return back()->with('success', 'Rekening berhasil ditambahkan.');
    }

    public function update(Request $request, Invitation $invitation, InvitationBankAccount $account)
    {
        $this->authorize($invitation);
        $this->requireGiftActive($invitation);

        $request->validate([
            'bank_name'      => 'required|string|max:50',
            'account_number' => 'required|string|max:30',
            'account_name'   => 'required|string|max:100',
        ]);

        $account->update($request->only('bank_name', 'account_number', 'account_name'));
        return back()->with('success', 'Rekening berhasil diupdate.');
    }

    public function destroy(Invitation $invitation, InvitationBankAccount $account)
    {
        $this->authorize($invitation);
        $account->delete();
        return back()->with('success', 'Rekening dihapus.');
    }

    /** Halaman konfirmasi beli fitur gift (simulasi) */
    public function buyFeature(Invitation $invitation)
    {
        $this->authorize($invitation);

        if ($invitation->isGiftActive()) {
            return redirect()->route('invitations.gift.index', $invitation)
                ->with('info', 'Fitur Gift Section sudah aktif.');
        }

        $price = $invitation->template->gift_feature_price;
        if ($price <= 0) {
            // Gratis — langsung aktifkan
            $invitation->update(['gift_enabled' => true]);
            return redirect()->route('invitations.gift.index', $invitation)
                ->with('success', 'Gift Section berhasil diaktifkan.');
        }

        $order = InvitationFeatureOrder::firstOrCreate(
            ['invitation_id' => $invitation->id, 'feature' => 'gift_section', 'status' => 'pending'],
            [
                'order_number'   => InvitationFeatureOrder::generateOrderNumber(),
                'user_id'        => auth()->id(),
                'amount'         => $price,
                'payment_method' => 'simulation',
            ]
        );

        return view('gift.buy', compact('invitation', 'order'));
    }

    /** Simulasi bayar fitur gift */
    public function simulatePay(InvitationFeatureOrder $order)
    {
        abort_if($order->user_id !== auth()->id(), 403);
        abort_if($order->isPaid(), 400);

        $order->update(['status' => 'paid', 'paid_at' => now()]);
        $order->invitation->update(['gift_enabled' => true]);

        return redirect()->route('invitations.gift.index', $order->invitation)
            ->with('success', 'Pembayaran berhasil! Gift Section sudah aktif.');
    }

    private function authorize(Invitation $invitation): void
    {
        if ($invitation->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            abort(403);
        }
    }

    private function requireGiftActive(Invitation $invitation): void
    {
        if (!$invitation->isGiftActive()) {
            abort(403, 'Gift Section belum diaktifkan.');
        }
    }
}
