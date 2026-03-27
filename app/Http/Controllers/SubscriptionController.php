<?php

namespace App\Http\Controllers;

use App\Models\PricingPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans       = PricingPlan::where('is_active', true)->orderBy('price')->get();
        $activePlan  = auth()->user()->activePlan();
        $activeSub   = auth()->user()->activeSubscription();

        return view('subscription.index', compact('plans', 'activePlan', 'activeSub'));
    }

    public function checkout(PricingPlan $plan)
    {
        if ($plan->isFree()) {
            return redirect()->route('subscription.index')->with('info', 'Paket Free sudah aktif secara default.');
        }

        // Buat order pending
        $order = UserSubscription::create([
            'user_id'         => auth()->id(),
            'pricing_plan_id' => $plan->id,
            'order_number'    => UserSubscription::generateOrderNumber(),
            'amount'          => $plan->price,
            'status'          => 'pending',
            'payment_method'  => 'simulation',
        ]);

        return view('subscription.checkout', compact('plan', 'order'));
    }

    public function simulatePay(UserSubscription $subscription)
    {
        abort_if($subscription->user_id !== auth()->id(), 403);
        abort_if($subscription->isActive(), 400);

        // Nonaktifkan subscription lama
        auth()->user()->subscriptions()
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        $subscription->update([
            'status'    => 'active',
            'paid_at'   => now(),
            'starts_at' => now(),
            'expires_at'=> null,
        ]);

        return redirect()->route('subscription.index')
            ->with('success', "Selamat! Paket {$subscription->plan->name} berhasil diaktifkan.");
    }
}
