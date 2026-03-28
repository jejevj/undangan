<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Menu;

class PaymentGatewayMenuSeeder extends Seeder
{
    public function run(): void
    {
        // Create parent menu "Pembayaran"
        $parentMenu = Menu::firstOrCreate(
            ['slug' => 'pembayaran'],
            [
                'name' => 'Pembayaran',
                'icon' => 'fa fa-credit-card',
                'url' => null,
                'parent_id' => null,
                'order' => 100,
                'permission_name' => null,
            ]
        );

        // Create child menu "Konfigurasi Gateway"
        Menu::firstOrCreate(
            ['slug' => 'payment-gateway'],
            [
                'name' => 'Konfigurasi Gateway',
                'icon' => 'fa fa-cog',
                'url' => '/dash/payment-gateway',
                'parent_id' => $parentMenu->id,
                'order' => 1,
                'permission_name' => 'payment-gateway.view',
            ]
        );

        $this->command->info('Payment Gateway menu created successfully.');
    }
}
