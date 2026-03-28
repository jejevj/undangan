<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PricingPlan;
use App\Models\UserSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles', 'subscriptions.plan'])
            ->withCount('invitations')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        $plans = PricingPlan::where('is_active', true)->orderBy('price')->get();
        return view('users.create', compact('roles', 'plans'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'pricing_plan_id' => 'nullable|exists:pricing_plans,id',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Assign role yang dipilih, atau default ke "pengguna" jika tidak ada role dipilih
        if ($request->roles && count($request->roles) > 0) {
            $user->syncRoles($request->roles);
        } else {
            $user->assignRole('pengguna');
        }

        // Assign pricing plan
        $planId = $request->pricing_plan_id;
        if (!$planId) {
            // Default ke free plan jika tidak dipilih
            $freePlan = PricingPlan::where('slug', 'free')->first();
            $planId = $freePlan->id;
        }
        
        $plan = PricingPlan::find($planId);
        if ($plan) {
            UserSubscription::create([
                'user_id'         => $user->id,
                'pricing_plan_id' => $plan->id,
                'order_number'    => UserSubscription::generateOrderNumber(),
                'amount'          => 0,
                'status'          => 'active',
                'payment_method'  => 'admin_assign',
                'starts_at'       => now(),
                'paid_at'         => now(),
            ]);
        }

        return redirect()->route('users.index')->with('success', 'User berhasil dibuat.');
    }

    public function show(User $user)
    {
        $user->load(['roles', 'subscriptions.plan', 'invitations.template']);
        $plans       = PricingPlan::where('is_active', true)->orderBy('price')->get();
        $activePlan  = $user->activePlan();
        $activeSub   = $user->activeSubscription();

        return view('users.show', compact('user', 'plans', 'activePlan', 'activeSub'));
    }

    public function edit(User $user)
    {
        $roles     = Role::orderBy('name')->get();
        $userRoles = $user->roles->pluck('name')->toArray();
        $plans     = PricingPlan::where('is_active', true)->orderBy('price')->get();
        $activePlan = $user->activePlan();
        $activeSub = $user->activeSubscription();

        return view('users.edit', compact('user', 'roles', 'userRoles', 'plans', 'activePlan', 'activeSub'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'pricing_plan_id' => 'nullable|exists:pricing_plans,id',
        ]);

        $user->update(['name' => $request->name, 'email' => $request->email]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($request->roles ?? []);

        // Update pricing plan jika ada perubahan
        if ($request->filled('pricing_plan_id')) {
            $currentPlan = $user->activePlan();
            
            // Cek apakah paket berubah
            if (!$currentPlan || $currentPlan->id != $request->pricing_plan_id) {
                $plan = PricingPlan::findOrFail($request->pricing_plan_id);
                
                // Nonaktifkan subscription lama
                $user->subscriptions()->where('status', 'active')->update(['status' => 'expired']);
                
                // Buat subscription baru
                UserSubscription::create([
                    'user_id'         => $user->id,
                    'pricing_plan_id' => $plan->id,
                    'order_number'    => UserSubscription::generateOrderNumber(),
                    'amount'          => 0,
                    'status'          => 'active',
                    'payment_method'  => 'admin_assign',
                    'starts_at'       => now(),
                    'paid_at'         => now(),
                ]);
            }
        }

        return redirect()->route('users.index')->with('success', 'User berhasil diupdate.');
    }

    /**
     * Admin assign paket ke user (bypass payment)
     */
    public function assignPlan(Request $request, User $user)
    {
        $request->validate(['plan_id' => 'required|exists:pricing_plans,id']);

        $plan = PricingPlan::findOrFail($request->plan_id);

        // Nonaktifkan subscription lama
        $user->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

        // Buat subscription baru (gratis oleh admin)
        UserSubscription::create([
            'user_id'         => $user->id,
            'pricing_plan_id' => $plan->id,
            'order_number'    => UserSubscription::generateOrderNumber(),
            'amount'          => 0,
            'status'          => 'active',
            'payment_method'  => 'admin_assign',
            'starts_at'       => now(),
            'expires_at'      => $request->expires_at ?: null,
            'paid_at'         => now(),
        ]);

        return redirect()->route('users.edit', $user)
            ->with('success', "Paket {$plan->name} berhasil di-assign ke {$user->name}.");
    }

    /**
     * Admin revoke/reset paket user ke Free
     */
    public function revokePlan(User $user)
    {
        $user->subscriptions()->where('status', 'active')->update(['status' => 'expired']);

        $freePlan = PricingPlan::where('slug', 'free')->first();
        UserSubscription::create([
            'user_id'         => $user->id,
            'pricing_plan_id' => $freePlan->id,
            'order_number'    => UserSubscription::generateOrderNumber(),
            'amount'          => 0,
            'status'          => 'active',
            'payment_method'  => 'admin_revoke',
            'starts_at'       => now(),
            'paid_at'         => now(),
        ]);

        return redirect()->route('users.edit', $user)
            ->with('success', "Paket {$user->name} direset ke Free.");
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus.');
    }
}
