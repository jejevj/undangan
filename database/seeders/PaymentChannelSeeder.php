<?php

namespace Database\Seeders;

use App\Models\PaymentChannel;
use Illuminate\Database\Seeder;

class PaymentChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $channels = [
            // Virtual Account Channels - DGPC (16 digits total)
            // Format: BIN (varies) + Customer Number (16 - BIN length)
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_CIMB',
                'name' => 'CIMB Niaga',
                'icon' => null,
                'description' => 'Transfer via Virtual Account CIMB Niaga',
                'bin_length' => 5,
                'bin_notes' => 'DGPC - 5 digit BIN + 11 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 1,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_MANDIRI',
                'name' => 'Mandiri',
                'icon' => null,
                'description' => 'Transfer via Virtual Account Mandiri',
                'bin_length' => 8,
                'bin_notes' => 'DGPC - 8 digit BIN + 8 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 2,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_BRI',
                'name' => 'BRI',
                'icon' => null,
                'description' => 'Transfer via Virtual Account BRI',
                'bin' => '13925',
                'bin_length' => 6,
                'partner_service_id' => '13925',
                'bin_notes' => 'DGPC - 6 digit BIN + 10 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 3,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_BNI',
                'name' => 'BNI',
                'icon' => null,
                'description' => 'Transfer via Virtual Account BNI',
                'bin' => '98829172',
                'bin_length' => 9,
                'partner_service_id' => '98829172',
                'bin_notes' => 'DGPC - 9 digit BIN + 7 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 4,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_PERMATA',
                'name' => 'Permata',
                'icon' => null,
                'description' => 'Transfer via Virtual Account Permata',
                'bin_length' => 5,
                'bin_notes' => 'DGPC - 5 digit BIN + 11 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 5,
            ],

            // E-Wallet Channels
            [
                'type' => 'ewallet',
                'code' => 'EMONEY_SHOPEE_PAY_SNAP',
                'name' => 'ShopeePay',
                'icon' => null,
                'description' => 'Bayar dengan ShopeePay',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 10,
            ],
            [
                'type' => 'ewallet',
                'code' => 'EMONEY_DANA_SNAP',
                'name' => 'DANA',
                'icon' => null,
                'description' => 'Bayar dengan DANA',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 11,
            ],
            [
                'type' => 'ewallet',
                'code' => 'EMONEY_OVO_SNAP',
                'name' => 'OVO',
                'icon' => null,
                'description' => 'Bayar dengan OVO',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 12,
            ],
        ];

        foreach ($channels as $channel) {
            PaymentChannel::updateOrCreate(
                ['code' => $channel['code']],
                $channel
            );
        }

        $this->command->info('Payment channels seeded successfully!');
    }
}
