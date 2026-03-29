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
    public function showRegistrationForm()
    {
        // Track registration page view
        EventTrackingService::track('view_register', 'registration_funnel', 'View Registration Form');
        
        return view('auth.login'); // Same view with tabs
    }

    public function register(Request $request)
    {
        // Track registration attempt
        EventTrackingService::track('submit_register', 'registration_funnel', 'Submit Registration Form');
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        // Assign role "pengguna" menggunakan Spatie Permission
        $user->assignRole('pengguna');

        // Assign free plan automatically
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

        // Login user automatically
        Auth::login($user);

        // Track successful registration
        EventTrackingService::track('register_success', 'registration_funnel', 'Registration Successful', [
            'user_id' => $user->id,
            'user_email' => $user->email,
        ]);

        return redirect()->route('dashboard')->with('success', 'Registrasi berhasil! Selamat datang di ' . config('app.name'));
    }
}
