<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\FunnelAnalysisService;
use Illuminate\Http\Request;

class FunnelReportController extends Controller
{
    public function index(Request $request)
    {
        // Check if user is admin
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Unauthorized access. Admin only.');
        }

        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        // Subscription Funnel
        $subscriptionFunnel = FunnelAnalysisService::subscriptionFunnel($startDate, $endDate);
        $subscriptionDropoffs = FunnelAnalysisService::topDropoffPoints('subscription_funnel', $startDate, $endDate);

        // Invitation Funnel
        $invitationFunnel = FunnelAnalysisService::invitationFunnel($startDate, $endDate);
        $invitationDropoffs = FunnelAnalysisService::topDropoffPoints('invitation_funnel', $startDate, $endDate);

        // Registration Funnel (for anonymous users)
        $registrationFunnel = FunnelAnalysisService::registrationFunnel($startDate, $endDate);
        $registrationDropoffs = FunnelAnalysisService::topDropoffPoints('registration_funnel', $startDate, $endDate);

        // Overall conversion rates
        $subscriptionConversion = FunnelAnalysisService::conversionRate(
            'subscription_funnel',
            'view_plans',
            'payment_completed',
            $startDate,
            $endDate
        );

        $invitationConversion = FunnelAnalysisService::conversionRate(
            'invitation_funnel',
            'view_templates',
            'publish',
            $startDate,
            $endDate
        );

        $registrationConversion = FunnelAnalysisService::conversionRate(
            'registration_funnel',
            'view_register',
            'register_success',
            $startDate,
            $endDate
        );

        return view('admin.funnel-report', compact(
            'subscriptionFunnel',
            'subscriptionDropoffs',
            'subscriptionConversion',
            'invitationFunnel',
            'invitationDropoffs',
            'invitationConversion',
            'registrationFunnel',
            'registrationDropoffs',
            'registrationConversion',
            'startDate',
            'endDate'
        ));
    }
}
