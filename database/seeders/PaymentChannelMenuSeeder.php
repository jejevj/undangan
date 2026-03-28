<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class PaymentChannelMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Get parent menu "Pembayaran"
        $parentMenu = Menu::where('slug', 'pembayaran')->first();

        if (!$parentMenu) {
            // Create parent menu if not exists
            $parentMenu = Menu::create([
                'slug' => 'pembayaran',
                'name' => 'Pembayaran',
                'icon' => 'fa fa-credit-card',
                'url' => null,
                'parent_id' => null,
                'order' => 100,
                'permission_name' => null,
            ]);
        }

        // Create child menu "Channel Pembayaran"
        Menu::firstOrCreate(
            ['slug' => 'payment-channels'],
            [
                'name' => 'Channel Pembayaran',
                'icon' => 'fa fa-list',
                'url' => '/dash/admin/payment-channels',
                'parent_id' => $parentMenu->id,
                'order' => 2,
                'permission_name' => 'payment-channels.view',
            ]
        );

        $this->command->info('Payment Channel menu created successfully.');
    }
}
