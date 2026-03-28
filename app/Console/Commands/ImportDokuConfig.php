<?php

namespace App\Console\Commands;

use App\Models\PaymentGatewayConfig;
use Illuminate\Console\Command;

class ImportDokuConfig extends Command
{
    protected $signature = 'doku:import-config {file}';
    protected $description = 'Import DOKU configuration from JSON file';

    public function handle()
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        $data = json_decode(file_get_contents($file), true);

        if (!$data) {
            $this->error('Invalid JSON file');
            return 1;
        }

        // Validate required fields
        $required = ['provider', 'environment', 'client_id', 'secret_key', 'private_key'];
        foreach ($required as $field) {
            if (!isset($data[$field])) {
                $this->error("Missing required field: {$field}");
                return 1;
            }
        }

        // Import configuration
        $config = PaymentGatewayConfig::updateOrCreate(
            ['provider' => $data['provider']],
            [
                'environment' => $data['environment'],
                'client_id' => $data['client_id'],
                'merchant_id' => $data['merchant_id'] ?? null,
                'partner_service_id' => $data['partner_service_id'] ?? null,
                'public_key' => $data['public_key'] ?? null,
                'doku_public_key' => $data['doku_public_key'] ?? null,
                'issuer' => $data['issuer'] ?? null,
                'base_url' => $data['base_url'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]
        );

        // Set encrypted fields directly (they are already encrypted in the export)
        $config->attributes['secret_key'] = $data['secret_key'];
        $config->attributes['private_key'] = $data['private_key'];
        $config->save();

        $this->info('✓ DOKU configuration imported successfully');
        $this->line('');
        $this->table(
            ['Field', 'Value'],
            [
                ['Provider', $config->provider],
                ['Environment', $config->environment],
                ['Client ID', $config->client_id],
                ['Merchant ID', $config->merchant_id ?? 'N/A'],
                ['Partner Service ID', $config->partner_service_id ?? 'N/A'],
                ['Base URL', $config->base_url ?? 'N/A'],
                ['Active', $config->is_active ? 'Yes' : 'No'],
            ]
        );

        return 0;
    }
}
