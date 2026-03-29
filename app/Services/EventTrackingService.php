<?php

namespace App\Services;

use App\Models\UserEvent;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class EventTrackingService
{
    /**
     * Track an event
     */
    public static function track(
        string $eventName,
        string $eventCategory,
        ?string $eventLabel = null,
        ?array $eventData = null
    ): void {
        try {
            // Ensure session is started
            if (!Session::isStarted()) {
                Session::start();
            }
            
            $sessionId = Session::getId() ?: Str::uuid()->toString();

            UserEvent::create([
                'user_id' => auth()->check() ? auth()->id() : null, // Allow null for anonymous users
                'session_id' => $sessionId,
                'event_name' => $eventName,
                'event_category' => $eventCategory,
                'event_label' => $eventLabel,
                'event_data' => $eventData,
                'page_url' => request()->fullUrl(),
                'referrer' => request()->header('referer'),
                'user_agent' => request()->userAgent(),
                'ip_address' => request()->ip(),
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silent fail - don't break user experience
            \Log::error('Event tracking failed', [
                'event' => $eventName,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Track page view
     */
    public static function pageView(string $pageName, ?array $data = null): void
    {
        self::track('page_view', 'navigation', $pageName, $data);
    }

    /**
     * Track subscription funnel events
     */
    public static function subscriptionFunnel(string $step, ?array $data = null): void
    {
        $steps = [
            'view_plans' => 'Viewed pricing plans',
            'select_plan' => 'Selected a plan',
            'view_checkout' => 'Viewed checkout page',
            'select_payment' => 'Selected payment method',
            'payment_initiated' => 'Initiated payment',
            'payment_completed' => 'Completed payment',
        ];

        self::track(
            $step,
            'subscription_funnel',
            $steps[$step] ?? $step,
            $data
        );
    }

    /**
     * Track invitation funnel events
     */
    public static function invitationFunnel(string $step, ?array $data = null): void
    {
        $steps = [
            'view_templates' => 'Viewed templates',
            'select_template' => 'Selected template',
            'start_create' => 'Started creating invitation',
            'fill_details' => 'Filled invitation details',
            'preview' => 'Previewed invitation',
            'publish' => 'Published invitation',
        ];

        self::track(
            $step,
            'invitation_funnel',
            $steps[$step] ?? $step,
            $data
        );
    }
}
