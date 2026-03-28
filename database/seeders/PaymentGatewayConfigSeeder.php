<?php

namespace Database\Seeders;

use App\Models\PaymentGatewayConfig;
use Illuminate\Database\Seeder;

class PaymentGatewayConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DOKU Payment Gateway Configuration
        PaymentGatewayConfig::updateOrCreate(
            ['provider' => 'doku'],
            [
                'environment' => 'production',
                'client_id' => 'BRN-0204-1754870435962',
                'merchant_id' => '75143', // Merchant ID for QRIS
                'partner_service_id' => '  888994', // 8 characters with leading spaces for VA
                'public_key' => "-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtgnas5vKvu99nT0/RGPP\r\nMT1SwS/lh3ak2IbZG8L66Dvf4HjMoYSZOG7SZTBHMsyuBrJU5Lm2Un1QJc5deGRM\r\namJ43BdNgo+zQGknzdYmsy6+pin2KCM7cwLLFPFY4Bcded+MOTOTtXJyaNjzm4aN\r\nnFgNnX0LeAwykGTJf2xHarT/Nj+tqEXiDostTs4/pNie1UPzb5p/5ZK/Pe7zEOr8\r\ncaCbE0+xxbnr2xIAUBYl4QEr45iGWBEJEwBF7TYpJysdh8pnupUH1ZIRksCeFHsE\r\nGRCb2RTl2pDE9xouhz6aJl0aUluvucgNyiucrBbhEcbZ4Rj3ilAHzLtoWsSl5OFW\r\nMQIDAQAB\r\n-----END PUBLIC KEY-----",
                'doku_public_key' => "-----BEGIN PUBLIC KEY-----\r\nMIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA0eauB3QupDEbp4Z7n19EQRVKhBqo5y6hkjWPG/Jpl5Cyf/+ONIaq3LaZ+FJEMn9ucOPohFoODZLPQp9HL6FNZ7Sa1VTMoZLZAqDUGxPrCuv+a9/+03MwVCqKc8M6t0Pb+diRBU4KApJIW/hW3Cze76zry9KE5qb0SOOsXr63MRq1CPdFXwdnA8vU2+zflTHQvtVyKFuOW1F3ZTX3KEt4QXwWzP4FwkDzai/iI9AezNnl4oGT1gCjBR2JREyP3/Fxbbusqn5cj3j12KMpTnXq7FO5BChTtc09u/XWHQkFnubsUBhbIcnZ18pkLPtlMnTTvwAiDU2bnlxypLAOyPCzQwIDAQAB\r\n-----END PUBLIC KEY-----",
                'issuer' => null,
                'base_url' => 'https://api.doku.com',
                'is_active' => true,
                
                // NOTE: secret_key and private_key are encrypted and should be set manually
                // after running this seeder using:
                // php artisan tinker
                // $config = \App\Models\PaymentGatewayConfig::where('provider', 'doku')->first();
                // $config->secret_key = 'YOUR_SECRET_KEY';
                // $config->private_key = 'YOUR_PRIVATE_KEY';
                // $config->save();
            ]
        );

        $this->command->info('✓ DOKU Payment Gateway configuration seeded successfully');
        $this->command->warn('⚠ Remember to set secret_key and private_key manually using tinker');
    }
}
