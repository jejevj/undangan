<?php

namespace App\Console\Commands;

use App\Models\PaymentGatewayConfig;
use Illuminate\Console\Command;

class ExportDokuConfig extends Command
{
    protected $signature = 'doku:export-config {--output=doku-config.json}';
    protected $description = 'Export DOKU configuration to JSON file (including encrypted keys)';

    public function handle()
    {
        $config = PaymentGatewayConfig::where('provider', 'doku')->first();

        if (!$config) {
            $this->error('DOKU configuration not found in database');
            return 1;
        }

        $outputFile = $this->option('output');

        // Export all fields including encrypted ones
        $data = [
            'provider' => $config->provider,
            'environment' => $config->environment,
            'client_id' => $config->client_id,
            'merchant_id' => $config->merchant_id,
            'partner_service_id' => $config->partner_service_id,
            'secret_key' => $config->secret_key, // Encrypted
            'private_key' => $config->private_key, // Encrypted
            'public_key' => $config->public_key,
            'doku_public_key' => $config->doku_public_key,
            'issuer' => $config->issuer,
            'base_url' => $config->base_url,
            'is_active' => $config->is_active,
            'exported_at' => now()->toIso8601String(),
        ];

        file_put_contents($outputFile, json_encode($data, JSON_PRETTY_PRINT));

        $this->info("✓ DOKU configuration exported to: {$outputFile}");
        $this->warn('⚠ This file contains encrypted keys. Keep it secure!');
        $this->line('');
        $this->line('To import this configuration on another system:');
        $this->line("  php artisan doku:import-config {$outputFile}");

        return 0;
    }
}
