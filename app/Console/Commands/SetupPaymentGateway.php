<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\Menu;

class SetupPaymentGateway extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:payment-gateway {--force : Force setup even if already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup payment gateway permissions and menu';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up Payment Gateway...');
        $this->newLine();

        // Step 1: Run seeders
        $this->info('[1/4] Running seeders...');
        $this->call('db:seed', [
            '--class' => 'PaymentGatewayPermissionSeeder',
            '--force' => true
        ]);
        $this->call('db:seed', [
            '--class' => 'PaymentGatewayMenuSeeder',
            '--force' => true
        ]);

        // Step 2: Assign permissions to admin role
        $this->info('[2/4] Assigning permissions to admin role...');
        $adminRole = Role::where('name', 'admin')->first();
        
        if ($adminRole) {
            $permissions = [
                'payment-gateway.view',
                'payment-gateway.create',
                'payment-gateway.edit',
                'payment-gateway.delete'
            ];
            
            foreach ($permissions as $permission) {
                if (!$adminRole->hasPermissionTo($permission)) {
                    $adminRole->givePermissionTo($permission);
                    $this->line("  ✓ Assigned: {$permission}");
                } else {
                    $this->line("  - Already has: {$permission}");
                }
            }
        } else {
            $this->error('  ✗ Admin role not found!');
        }

        // Step 3: Clear caches
        $this->info('[3/4] Clearing caches...');
        $this->call('optimize:clear');
        $this->call('permission:cache-reset');

        // Step 4: Verify
        $this->info('[4/4] Verifying setup...');
        $permissionCount = Permission::where('name', 'like', 'payment-gateway%')->count();
        $menu = Menu::where('name', 'Payment Gateway')->first();
        
        $this->table(
            ['Item', 'Status'],
            [
                ['Permissions', $permissionCount === 4 ? '✓ Found (4)' : '✗ Missing'],
                ['Menu', $menu ? '✓ Found' : '✗ Missing'],
                ['Menu Active', $menu && $menu->is_active ? '✓ Yes' : '✗ No'],
            ]
        );

        $this->newLine();
        $this->info('Setup completed successfully!');
        $this->newLine();
        
        $this->warn('Next steps:');
        $this->line('1. Logout from dashboard');
        $this->line('2. Clear browser cache (Ctrl+Shift+Delete)');
        $this->line('3. Login again');
        $this->line('4. Menu "Payment Gateway" should now appear');
        
        return Command::SUCCESS;
    }
}
