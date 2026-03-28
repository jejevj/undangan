<?php

namespace Database\Seeders;

use App\Models\PaymentChannel;
use Illuminate\Database\Seeder;

class LatestPaymentChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * This seeder contains the latest payment channel configuration
     * exported from production database on 2026-03-29
     */
    public function run(): void
    {
        $channels = [
            // Virtual Account Channels
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_BRI',
                'name' => 'BRI',
                'description' => 'Transfer via Virtual Account BRI',
                'bin_length' => 6,
                'bin_notes' => 'DGPC - 6 digit BIN + 10 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'partner_service_id' => '139250',
                'prefix_customer_no' => '0',
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => true,
                'sort_order' => 3,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_BNI',
                'name' => 'BNI',
                'description' => 'Transfer via Virtual Account BNI',
                'bin_length' => 9,
                'bin_notes' => 'DGPC - 9 digit BIN + 7 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'partner_service_id' => '988291723',
                'prefix_customer_no' => null,
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => true,
                'sort_order' => 4,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_BANK_CIMB',
                'name' => 'CIMB Niaga',
                'description' => 'Transfer via Virtual Account CIMB Niaga',
                'bin_length' => 5,
                'bin_notes' => 'DGPC - 5 digit BIN + 11 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'partner_service_id' => '18999',
                'prefix_customer_no' => null,
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => true,
                'sort_order' => 1,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_MANDIRI',
                'name' => 'Mandiri',
                'description' => 'Transfer via Virtual Account Mandiri',
                'bin_length' => 8,
                'bin_notes' => 'DGPC - 8 digit BIN + 8 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'partner_service_id' => null,
                'prefix_customer_no' => null,
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 2,
            ],
            [
                'type' => 'virtual_account',
                'code' => 'VIRTUAL_ACCOUNT_PERMATA',
                'name' => 'Permata',
                'description' => 'Transfer via Virtual Account Permata',
                'bin_length' => 5,
                'bin_notes' => 'DGPC - 5 digit BIN + 11 digit customer number = 16 total',
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'partner_service_id' => '896599',
                'prefix_customer_no' => '99',
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => true,
                'sort_order' => 5,
            ],

            // E-Wallet Channels
            [
                'type' => 'ewallet',
                'code' => 'EMONEY_DANA_SNAP',
                'name' => 'DANA',
                'description' => 'Bayar dengan DANA',
                'bin_length' => null,
                'bin_notes' => null,
                'billing_type' => 'FIX_BILL',
                'feature' => 'DGPC',
                'bin_type' => 'Doku General BIN',
                'partner_service_id' => null,
                'prefix_customer_no' => null,
                'va_trx_type' => 'C',
                'is_active' => true,
                'is_available' => false,
                'sort_order' => 11,
            ],
        ];

        foreach ($channels as $channelData) {
            PaymentChannel::updateOrCreate(
                ['code' => $channelData['code']],
                $channelData
            );
        }

        $this->command->info('✓ Payment channels seeded successfully');
        $this->command->line('');
        $this->command->table(
            ['Type', 'Code', 'Name', 'Available', 'Sort Order'],
            collect($channels)->map(fn($ch) => [
                $ch['type'],
                $ch['code'],
                $ch['name'],
                $ch['is_available'] ? 'Yes' : 'No',
                $ch['sort_order'],
            ])->toArray()
        );
    }
}
