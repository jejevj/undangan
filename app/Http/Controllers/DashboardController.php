<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Invitation;
use App\Models\UserSubscription;
use App\Models\DokuVirtualAccount;
use App\Models\DokuEWalletPayment;
use App\Models\DokuQrisPayment;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Admin gets full statistics
        if ($user->hasRole('admin')) {
            return $this->adminDashboard();
        }
        
        // Regular users get simple dashboard
        return $this->userDashboard();
    }
    
    protected function adminDashboard()
    {
        // User Statistics
        $totalUsers = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        $activeSubscriptions = UserSubscription::where('status', 'active')->count();
        
        // Invitation Statistics
        $totalInvitations = Invitation::count();
        $publishedInvitations = Invitation::where('is_published', true)->count();
        $invitationsThisMonth = Invitation::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Revenue Statistics
        $totalRevenue = UserSubscription::where('status', 'active')
            ->sum('amount');
        $revenueThisMonth = UserSubscription::where('status', 'active')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');
        
        // Payment Method Statistics
        $paymentMethods = [
            'va' => DokuVirtualAccount::where('status', 'paid')->count(),
            'ewallet' => DokuEWalletPayment::where('status', 'success')->count(),
            'qris' => DokuQrisPayment::where('status', 'paid')->count(),
        ];
        
        // Subscription by Plan
        $subscriptionsByPlan = UserSubscription::where('status', 'active')
            ->select('pricing_plan_id', DB::raw('count(*) as total'))
            ->groupBy('pricing_plan_id')
            ->with('plan:id,name')
            ->get()
            ->mapWithKeys(fn($item) => [$item->plan->name ?? 'Unknown' => $item->total]);
        
        // Monthly Revenue Chart (last 6 months)
        $monthlyRevenue = UserSubscription::where('status', 'active')
            ->where('paid_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(paid_at, "%Y-%m") as month'),
                DB::raw('SUM(amount) as revenue')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month');
        
        // User Growth Chart (last 6 months)
        $userGrowth = User::where('created_at', '>=', now()->subMonths(6))
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('total', 'month');
        
        // Recent Subscriptions
        $recentSubscriptions = UserSubscription::with(['user:id,name,email', 'plan:id,name'])
            ->where('status', 'active')
            ->latest('paid_at')
            ->take(10)
            ->get();
        
        // Payment Channel Usage
        $vaChannels = DokuVirtualAccount::where('status', 'paid')
            ->select('channel', DB::raw('count(*) as total'))
            ->groupBy('channel')
            ->get()
            ->pluck('total', 'channel');
        
        return view('dashboard-admin', compact(
            'totalUsers',
            'newUsersThisMonth',
            'activeSubscriptions',
            'totalInvitations',
            'publishedInvitations',
            'invitationsThisMonth',
            'totalRevenue',
            'revenueThisMonth',
            'paymentMethods',
            'subscriptionsByPlan',
            'monthlyRevenue',
            'userGrowth',
            'recentSubscriptions',
            'vaChannels'
        ));
    }
    
    protected function userDashboard()
    {
        $user = auth()->user();
        
        // User's own statistics
        $myInvitations = Invitation::where('user_id', $user->id)->count();
        $publishedInvitations = Invitation::where('user_id', $user->id)
            ->where('is_published', true)
            ->count();
        $activePlan = $user->activePlan();
        $activeSubscription = $user->activeSubscription();
        
        return view('dashboard', compact(
            'myInvitations',
            'publishedInvitations',
            'activePlan',
            'activeSubscription'
        ));
    }
}
