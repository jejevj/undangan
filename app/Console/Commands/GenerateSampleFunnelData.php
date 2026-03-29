<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserEvent;
use App\Models\User;
use Illuminate\Support\Str;

class GenerateSampleFunnelData extends Command
{
    protected $signature = 'funnel:generate-sample {--days=7 : Number of days to generate data for}';
    protected $description = 'Generate sample funnel data for testing';

    public function handle()
    {
        $days = $this->option('days');
        $this->info("Generating sample funnel data for the last {$days} days...");

        $users = User::limit(10)->get();
        if ($users->isEmpty()) {
            $this->error('No users found. Please create some users first.');
            return 1;
        }

        $subscriptionSteps = [
            'view_plans',
            'select_plan',
            'view_checkout',
            'select_payment',
            'payment_initiated',
            'payment_completed',
        ];

        $invitationSteps = [
            'view_templates',
            'select_template',
            'start_create',
            'fill_details',
            'preview',
            'publish',
        ];

        $registrationSteps = [
            'view_register',
            'submit_register',
            'register_success',
        ];

        $totalEvents = 0;

        // Generate subscription funnel data
        for ($day = 0; $day < $days; $day++) {
            $date = now()->subDays($day);
            
            // Simulate 20-50 sessions per day
            $sessionsCount = rand(20, 50);
            
            for ($i = 0; $i < $sessionsCount; $i++) {
                $sessionId = Str::uuid()->toString();
                $user = $users->random();
                
                // Simulate dropoff - not all sessions complete all steps
                $completedSteps = rand(1, count($subscriptionSteps));
                
                foreach (array_slice($subscriptionSteps, 0, $completedSteps) as $index => $step) {
                    $eventTime = $date->copy()->addMinutes(rand(0, 1440));
                    
                    UserEvent::create([
                        'user_id' => rand(0, 1) ? $user->id : null, // 50% anonymous
                        'session_id' => $sessionId,
                        'event_name' => $step,
                        'event_category' => 'subscription_funnel',
                        'event_label' => $this->getStepLabel($step),
                        'event_data' => [
                            'plan_id' => rand(1, 3),
                            'amount' => rand(50000, 200000),
                        ],
                        'page_url' => 'http://127.0.0.1:8000/dash/subscription',
                        'referrer' => $index === 0 ? 'http://127.0.0.1:8000/' : null,
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                        'ip_address' => '127.0.0.1',
                        'created_at' => $eventTime,
                        'updated_at' => $eventTime,
                    ]);
                    $totalEvents++;
                }
            }
        }

        // Generate invitation funnel data
        for ($day = 0; $day < $days; $day++) {
            $date = now()->subDays($day);
            
            // Simulate 30-60 sessions per day
            $sessionsCount = rand(30, 60);
            
            for ($i = 0; $i < $sessionsCount; $i++) {
                $sessionId = Str::uuid()->toString();
                $user = $users->random();
                
                // Simulate dropoff
                $completedSteps = rand(1, count($invitationSteps));
                
                foreach (array_slice($invitationSteps, 0, $completedSteps) as $index => $step) {
                    $eventTime = $date->copy()->addMinutes(rand(0, 1440));
                    
                    UserEvent::create([
                        'user_id' => rand(0, 1) ? $user->id : null,
                        'session_id' => $sessionId,
                        'event_name' => $step,
                        'event_category' => 'invitation_funnel',
                        'event_label' => $this->getStepLabel($step),
                        'event_data' => [
                            'template_id' => rand(1, 5),
                        ],
                        'page_url' => 'http://127.0.0.1:8000/dash/invitations',
                        'referrer' => $index === 0 ? 'http://127.0.0.1:8000/dash' : null,
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                        'ip_address' => '127.0.0.1',
                        'created_at' => $eventTime,
                        'updated_at' => $eventTime,
                    ]);
                    $totalEvents++;
                }
            }
        }

        // Generate registration funnel data (anonymous users)
        for ($day = 0; $day < $days; $day++) {
            $date = now()->subDays($day);
            
            // Simulate 10-30 registration attempts per day
            $sessionsCount = rand(10, 30);
            
            for ($i = 0; $i < $sessionsCount; $i++) {
                $sessionId = Str::uuid()->toString();
                
                // Simulate dropoff - not all complete registration
                $completedSteps = rand(1, count($registrationSteps));
                
                foreach (array_slice($registrationSteps, 0, $completedSteps) as $index => $step) {
                    $eventTime = $date->copy()->addMinutes(rand(0, 1440));
                    
                    UserEvent::create([
                        'user_id' => null, // Always null for registration funnel (anonymous)
                        'session_id' => $sessionId,
                        'event_name' => $step,
                        'event_category' => 'registration_funnel',
                        'event_label' => $this->getStepLabel($step),
                        'event_data' => [],
                        'page_url' => 'http://127.0.0.1:8000/register',
                        'referrer' => $index === 0 ? 'http://127.0.0.1:8000/' : null,
                        'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
                        'ip_address' => '127.0.0.1',
                        'created_at' => $eventTime,
                        'updated_at' => $eventTime,
                    ]);
                    $totalEvents++;
                }
            }
        }

        $this->info("✓ Generated {$totalEvents} sample events");
        $this->info("✓ You can now view the funnel report at: /dash/admin/funnel-report");

        return 0;
    }

    protected function getStepLabel($step)
    {
        $labels = [
            'view_plans' => 'View Plans',
            'select_plan' => 'Select Plan',
            'view_checkout' => 'View Checkout',
            'select_payment' => 'Select Payment',
            'payment_initiated' => 'Payment Initiated',
            'payment_completed' => 'Payment Completed',
            'view_templates' => 'View Templates',
            'select_template' => 'Select Template',
            'start_create' => 'Start Create',
            'fill_details' => 'Fill Details',
            'preview' => 'Preview',
            'publish' => 'Publish',
        ];

        return $labels[$step] ?? ucwords(str_replace('_', ' ', $step));
    }
}
