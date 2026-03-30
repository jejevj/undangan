<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\PricingPlan;
use App\Models\UserSubscription;
use App\Services\EventTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {
        // Track registration page view
        EventTrackingService::track('view_register', 'registration_funnel', 'View Registration Form');
        
        // Check for campaign code in URL
        $campaignCode = $request->query('ref');
        $campaign = null;
        $campaignWarning = null;
        
        if ($campaignCode) {
            $campaign = \App\Models\Campaign::where('code', $campaignCode)->first();
            
            if ($campaign) {
                // Check if campaign is valid
                if (!$campaign->isValid()) {
                    // Campaign exists but not valid - show warning
                    if (!$campaign->is_active) {
                        $campaignWarning = "Kampanye '{$campaign->name}' sudah tidak aktif.";
                    } elseif ($campaign->start_date && now()->lt($campaign->start_date)) {
                        $campaignWarning = "Kampanye '{$campaign->name}' belum dimulai.";
                    } elseif ($campaign->end_date && now()->gt($campaign->end_date)) {
                        $campaignWarning = "Kampanye '{$campaign->name}' sudah berakhir.";
                    } elseif ($campaign->hasReachedLimit()) {
                        $campaignWarning = "Kuota kampanye '{$campaign->name}' sudah terpenuhi.";
                    }
                    $campaign = null; // Don't use invalid campaign
                }
            } else {
                // Campaign code not found
                $campaignWarning = "Kode kampanye tidak ditemukan.";
            }
        }
        
        return view('auth.login', compact('campaign', 'campaignWarning'));
    }

    public function register(Request $request)
    {
        // Track registration attempt
        EventTrackingService::track('submit_register', 'registration_funnel', 'Submit Registration Form');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
            'campaign_code' => 'nullable|string|exists:campaigns,code',
        ]);

        // Check campaign if provided
        $campaign = null;
        $planToAssign = null;
        
        if (!empty($validated['campaign_code'])) {
            $campaign = \App\Models\Campaign::where('code', $validated['campaign_code'])->first();
            
            // Validate campaign
            if ($campaign && $campaign->isValid()) {
                $planToAssign = $campaign->pricingPlan;
            } else {
                $campaign = null; // Invalid campaign
            }
        }

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'campaign_id' => $campaign ? $campaign->id : null,
        ]);

        // Assign role "pengguna" menggunakan Spatie Permission
        $user->assignRole('pengguna');

        // Assign plan based on campaign or default to free
        if ($planToAssign) {
            // Campaign plan
            UserSubscription::create([
                'user_id' => $user->id,
                'pricing_plan_id' => $planToAssign->id,
                'order_number' => UserSubscription::generateOrderNumber(),
                'amount' => 0, // Free from campaign
                'status' => 'active',
                'payment_method' => 'campaign',
                'starts_at' => now(),
                'expires_at' => null, // Permanent
                'paid_at' => now(),
            ]);
            
            // Increment campaign used count
            $campaign->incrementUsedCount();
            
            $successMessage = "Registrasi berhasil! Anda mendapatkan akses {$planToAssign->name} gratis dari kampanye {$campaign->name}!";
        } else {
            // Free plan
            $freePlan = PricingPlan::where('price', 0)->first();
            
            if ($freePlan) {
                UserSubscription::create([
                    'user_id' => $user->id,
                    'pricing_plan_id' => $freePlan->id,
                    'order_number' => UserSubscription::generateOrderNumber(),
                    'amount' => 0,
                    'status' => 'active',
                    'payment_method' => 'free',
                    'starts_at' => now(),
                    'expires_at' => null,
                    'paid_at' => now(),
                ]);
            }
            
            $successMessage = 'Registrasi berhasil! Selamat datang di ' . config('app.name');
        }

        // Login user automatically
        Auth::login($user);

        // Track successful registration
        EventTrackingService::track('register_success', 'registration_funnel', 'Registration Successful', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'campaign_id' => $campaign ? $campaign->id : null,
            'campaign_code' => $campaign ? $campaign->code : null,
        ]);

        return redirect()->route('dashboard')->with('success', $successMessage);
    }
}
