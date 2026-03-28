<?php

namespace App\Console\Commands;

use App\Services\PaymentChannelAvailabilityService;
use Illuminate\Console\Command;

class CheckPaymentChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:check-channels {--channel= : Specific channel code to check}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check availability of payment channels';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new PaymentChannelAvailabilityService();
        
        if ($this->option('channel')) {
            // Check specific channel
            $code = $this->option('channel');
            $this->info("Checking channel: {$code}");
            
            $available = $service->checkChannel($code);
            
            if ($available) {
                $this->info("✓ Channel {$code} is available");
            } else {
                $this->error("✗ Channel {$code} is not available");
            }
            
            return 0;
        }
        
        // Check all channels
        $this->info('Checking all payment channels...');
        $results = $service->checkAllChannels();
        
        // Display VA results
        $this->newLine();
        $this->info('Virtual Account Channels:');
        $this->table(
            ['Channel', 'Status'],
            collect($results['virtual_account'])->map(function ($available, $code) {
                return [
                    $code,
                    $available ? '✓ Available' : '✗ Not Available'
                ];
            })->toArray()
        );
        
        // Display E-Wallet results
        $this->newLine();
        $this->info('E-Wallet Channels:');
        $this->table(
            ['Channel', 'Status'],
            collect($results['ewallet'])->map(function ($available, $code) {
                return [
                    $code,
                    $available ? '✓ Available' : '✗ Not Available'
                ];
            })->toArray()
        );
        
        $this->newLine();
        $this->info('Channel availability check completed!');
        
        return 0;
    }
}
