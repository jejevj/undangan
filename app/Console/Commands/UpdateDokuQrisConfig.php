<?php

namespace App\Console\Commands;

use App\Models\PaymentGatewayConfig;
use Illuminate\Console\Command;

class UpdateDokuQrisConfig extends Command
{
    protected $signature = 'doku:update-qris-config';
    protected $description = 'Update DOKU QRIS configuration with merchant ID and partner service ID';

    public function handle()
    {
        $config = PaymentGatewayConfig::where('provider', 'doku')->first();
        
        if (!$config) {
            $this->error('DOKU configuration not found!');
            return 1;
        }

        $config->merchant_id = '75143';
        $config->partner_service_id = '888994';
        $config->environment = 'production';
        $config->base_url = 'https://api.doku.com';
        $config->save();

        $this->info('DOKU QRIS configuration updated successfully!');
        $this->line('');
        $this->line('Merchant ID: ' . $config->merchant_id);
        $this->line('Partner Service ID: ' . $config->partner_service_id);
        $this->line('Environment: ' . $config->environment);
        $this->line('Base URL: ' . $config->base_url);
        $this->line('');
        $this->info('QRIS is now configured with Terminal ID: A01');
        $this->info('Client ID for QRIS: 75143');

        return 0;
    }
}
