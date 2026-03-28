<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupPaymentChannels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:payment-channels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup payment channels menu, permissions, and seed initial data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Payment Channels...');
        $this->newLine();

        // Run permission seeder
        $this->info('1. Creating permissions...');
        $this->call('db:seed', ['--class' => 'PaymentChannelPermissionSeeder']);

        // Run menu seeder
        $this->info('2. Creating menu...');
        $this->call('db:seed', ['--class' => 'PaymentChannelMenuSeeder']);

        // Run channel seeder
        $this->info('3. Seeding payment channels...');
        $this->call('db:seed', ['--class' => 'PaymentChannelSeeder']);

        $this->newLine();
        $this->info('✅ Payment Channels setup completed!');
        $this->newLine();
        
        $this->line('You can now access:');
        $this->line('  • Menu: Pembayaran > Channel Pembayaran');
        $this->line('  • URL: /dash/admin/payment-channels');
        $this->newLine();
        
        $this->line('Next steps:');
        $this->line('  1. Login to DOKU Dashboard');
        $this->line('  2. Get BIN/Partner Service ID for each bank');
        $this->line('  3. Update payment channels via admin panel');

        return Command::SUCCESS;
    }
}
