<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Pricing Plans Data ===\n\n";

$plans = \App\Models\PricingPlan::where('is_active', true)->orderBy('price')->get();

echo "Total active plans: " . $plans->count() . "\n\n";

if ($plans->count() > 0) {
    echo "Plans found:\n";
    foreach ($plans as $plan) {
        echo "- ID: {$plan->id}, Name: {$plan->name}, Price: {$plan->formattedPrice()}, Active: " . ($plan->is_active ? 'Yes' : 'No') . "\n";
    }
} else {
    echo "No active pricing plans found!\n";
    echo "Please run: php artisan db:seed --class=PricingPlanSeeder\n";
}

echo "\n=== End Test ===\n";
