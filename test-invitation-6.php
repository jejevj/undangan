<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$invitation = App\Models\Invitation::with(['data.templateField', 'template'])->find(6);

echo "Template: " . $invitation->template->name . "\n";
echo "Blade View: " . $invitation->template->blade_view . "\n";
echo "\nInvitation Data:\n";

$data = $invitation->getDataMap();
foreach ($data as $key => $value) {
    echo "  $key: " . (is_string($value) ? substr($value, 0, 50) : $value) . "\n";
}
