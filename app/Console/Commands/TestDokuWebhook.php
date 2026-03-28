<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DokuVirtualAccount;
use Illuminate\Support\Facades\Http;

class TestDokuWebhook extends Command
{
    protected $signature = 'doku:test-webhook {va_id}';
    protected $description = 'Test DOKU webhook by simulating payment notification';

    public function handle()
    {
        $vaId = $this->argument('va_id');
        
        $va = DokuVirtualAccount::find($vaId);
        
        if (!$va) {
            $this->error("VA with ID {$vaId} not found");
            return 1;
        }

        $this->info("Testing webhook for VA: {$va->virtual_account_no}");
        $this->info("Amount: Rp " . number_format($va->amount, 0, ',', '.'));

        // Simulate DOKU webhook payload
        $payload = [
            'virtualAccountNo' => $va->virtual_account_no,
            'trxId' => $va->trx_id,
            'paidAmount' => [
                'value' => number_format($va->amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'transactionDate' => now()->format('Y-m-d\TH:i:sP'),
            'additionalInfo' => [
                'channel' => $va->channel,
            ],
        ];

        $this->info("\nPayload:");
        $this->line(json_encode($payload, JSON_PRETTY_PRINT));

        if (!$this->confirm('Send this webhook to local server?', true)) {
            return 0;
        }

        // Send to local webhook
        $url = config('app.url') . '/webhook/doku/payment-notification';
        
        $this->info("\nSending to: {$url}");

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'X-TIMESTAMP' => now()->format('Y-m-d\TH:i:sP'),
                'X-SIGNATURE' => 'test-signature', // Will fail validation, but good for testing
            ])->post($url, $payload);

            $this->info("\nResponse Status: " . $response->status());
            $this->info("Response Body:");
            $this->line($response->body());

            if ($response->successful()) {
                $this->info("\n✅ Webhook test successful!");
                
                // Refresh VA from database
                $va->refresh();
                $this->info("VA Status: {$va->status}");
            } else {
                $this->error("\n❌ Webhook test failed!");
            }

        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
