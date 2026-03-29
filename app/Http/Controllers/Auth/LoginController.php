<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\EventTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Track login page view
        EventTrackingService::track('view_login', 'authentication', 'View Login Form');
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // Track login attempt
        EventTrackingService::track('submit_login', 'authentication', 'Submit Login Form');
        
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            
            // Track successful login
            EventTrackingService::track('login_success', 'authentication', 'Login Successful', [
                'user_id' => auth()->id(),
                'user_email' => auth()->user()->email,
            ]);
            
            return redirect()->intended(route('dashboard'));
        }

        // Track failed login
        EventTrackingService::track('login_failed', 'authentication', 'Login Failed', [
            'email' => $credentials['email'],
        ]);

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        // Track logout
        EventTrackingService::track('logout', 'authentication', 'User Logout', [
            'user_id' => auth()->id(),
        ]);
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
