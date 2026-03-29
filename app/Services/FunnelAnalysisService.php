<?php

namespace App\Services;

use App\Models\UserEvent;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FunnelAnalysisService
{
    /**
     * Analyze subscription funnel
     */
    public static function subscriptionFunnel($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $steps = [
            'view_plans',
            'select_plan',
            'view_checkout',
            'select_payment',
            'payment_initiated',
            'payment_completed',
        ];

        $funnelData = [];
        $previousCount = null;

        foreach ($steps as $step) {
            $count = UserEvent::where('event_category', 'subscription_funnel')
                ->where('event_name', $step)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct('session_id')
                ->count('session_id');

            $dropoffRate = $previousCount ? (($previousCount - $count) / $previousCount * 100) : 0;
            $conversionRate = $previousCount ? ($count / $previousCount * 100) : 100;

            $funnelData[] = [
                'step' => $step,
                'label' => self::getStepLabel($step),
                'count' => $count,
                'dropoff_rate' => round($dropoffRate, 2),
                'conversion_rate' => round($conversionRate, 2),
            ];

            $previousCount = $count;
        }

        return $funnelData;
    }

    /**
     * Analyze invitation funnel
     */
    public static function invitationFunnel($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $steps = [
            'view_templates',
            'select_template',
            'start_create',
            'fill_details',
            'preview',
            'publish',
        ];

        $funnelData = [];
        $previousCount = null;

        foreach ($steps as $step) {
            $count = UserEvent::where('event_category', 'invitation_funnel')
                ->where('event_name', $step)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct('session_id')
                ->count('session_id');

            $dropoffRate = $previousCount ? (($previousCount - $count) / $previousCount * 100) : 0;
            $conversionRate = $previousCount ? ($count / $previousCount * 100) : 100;

            $funnelData[] = [
                'step' => $step,
                'label' => self::getStepLabel($step),
                'count' => $count,
                'dropoff_rate' => round($dropoffRate, 2),
                'conversion_rate' => round($conversionRate, 2),
            ];

            $previousCount = $count;
        }

        return $funnelData;
    }

    /**
     * Analyze registration funnel (for anonymous users)
     */
    public static function registrationFunnel($startDate = null, $endDate = null): array
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $steps = [
            'view_register',
            'submit_register',
            'register_success',
        ];

        $funnelData = [];
        $previousCount = null;

        foreach ($steps as $step) {
            $count = UserEvent::where('event_category', 'registration_funnel')
                ->where('event_name', $step)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->distinct('session_id')
                ->count('session_id');

            $dropoffRate = $previousCount ? (($previousCount - $count) / $previousCount * 100) : 0;
            $conversionRate = $previousCount ? ($count / $previousCount * 100) : 100;

            $funnelData[] = [
                'step' => $step,
                'label' => self::getStepLabel($step),
                'count' => $count,
                'dropoff_rate' => round($dropoffRate, 2),
                'conversion_rate' => round($conversionRate, 2),
            ];

            $previousCount = $count;
        }

        return $funnelData;
    }

    /**
     * Get user journey for a session
     */
    public static function getUserJourney(string $sessionId): array
    {
        return UserEvent::where('session_id', $sessionId)
            ->orderBy('created_at')
            ->get()
            ->map(fn($event) => [
                'timestamp' => $event->created_at->format('Y-m-d H:i:s'),
                'event' => $event->event_name,
                'category' => $event->event_category,
                'label' => $event->event_label,
                'page' => $event->page_url,
            ])
            ->toArray();
    }

    /**
     * Get conversion rate between two steps
     */
    public static function conversionRate(
        string $category,
        string $fromStep,
        string $toStep,
        $startDate = null,
        $endDate = null
    ): float {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $fromCount = UserEvent::where('event_category', $category)
            ->where('event_name', $fromStep)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('session_id')
            ->count('session_id');

        if ($fromCount === 0) {
            return 0;
        }

        $toCount = UserEvent::where('event_category', $category)
            ->where('event_name', $toStep)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->distinct('session_id')
            ->count('session_id');

        return round(($toCount / $fromCount) * 100, 2);
    }

    /**
     * Get step label
     */
    protected static function getStepLabel(string $step): string
    {
        $labels = [
            // Subscription funnel
            'view_plans' => 'View Plans',
            'select_plan' => 'Select Plan',
            'view_checkout' => 'View Checkout',
            'select_payment' => 'Select Payment',
            'payment_initiated' => 'Payment Initiated',
            'payment_completed' => 'Payment Completed',
            
            // Invitation funnel
            'view_templates' => 'View Templates',
            'select_template' => 'Select Template',
            'start_create' => 'Start Create',
            'fill_details' => 'Fill Details',
            'preview' => 'Preview',
            'publish' => 'Publish',
            
            // Registration funnel
            'view_register' => 'View Register',
            'submit_register' => 'Submit Register',
            'register_success' => 'Register Success',
        ];

        return $labels[$step] ?? ucwords(str_replace('_', ' ', $step));
    }

    /**
     * Get top dropoff points
     */
    public static function topDropoffPoints(string $category, $startDate = null, $endDate = null): array
    {
        $funnel = $category === 'subscription_funnel' 
            ? self::subscriptionFunnel($startDate, $endDate)
            : self::invitationFunnel($startDate, $endDate);

        return collect($funnel)
            ->sortByDesc('dropoff_rate')
            ->take(3)
            ->values()
            ->toArray();
    }
}
