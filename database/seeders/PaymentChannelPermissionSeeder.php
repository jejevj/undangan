<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PaymentChannelPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create permissions for payment channels
        $permissions = [
            'payment-channels.view',
            'payment-channels.create',
            'payment-channels.edit',
            'payment-channels.delete',
            'payment-channels.check-availability',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Assign all permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo($permissions);
        }

        $this->command->info('Payment Channel permissions created and assigned to admin role.');
    }
}
