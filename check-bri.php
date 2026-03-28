<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "BRI Configuration:\n";
echo str_repeat('━', 70) . "\n";

$bri = DB::table('payment_channels')->where('code', 'VIRTUAL_ACCOUNT_BRI')->first();

if ($bri) {
    echo "BIN: " . ($bri->bin ?? 'NULL') . "\n";
    echo "BIN Length (field): " . ($bri->bin_length ?? 'NULL') . "\n";
    echo "BIN Length (actual): " . strlen($bri->bin ?? '') . "\n";
    echo "Partner Service ID: " . ($bri->partner_service_id ?? 'NULL') . "\n";
    echo "PSID Length: " . strlen($bri->partner_service_id ?? '') . "\n";
} else {
    echo "BRI channel not found\n";
}
