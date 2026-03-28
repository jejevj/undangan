<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\DokuQrisService;
use Illuminate\Console\Command;

class TestQrisGeneration extends Command
{
    protected $signature = 'test:qris-generate {user_id=1} {amount=1000}';
    protected $description = 'Test QRIS generation';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $amount = $this->argument('amount');

        $user = User::find($userId);
        if (!$user) {
            $this->error("User with ID {$userId} not found");
            return 1;
        }

        $this->info("Testing QRIS Generation");
        $this->info("User: {$user->name} ({$user->email})");
        $this->info("Amount: Rp " . number_format($amount, 0, ',', '.'));
        $this->newLine();

        try {
            $qrisService = new DokuQrisService();
            
            $result = $qrisService->generateQris($user, [
                'payment_type' => 'subscription',
                'amount' => $amount,
                'reference_id' => null,
                'postal_code' => '12345',
                'expired_minutes' => 30,
            ]);

            if ($result['success']) {
                $qris = $result['qris'];
                
                $this->info('✓ QRIS Generated Successfully!');
                $this->newLine();
                $this->table(
                    ['Field', 'Value'],
                    [
                        ['QRIS ID', $qris->id],
                        ['Partner Reference No', $qris->partner_reference_no],
                        ['Reference No', $qris->reference_no ?? 'N/A'],
                        ['Merchant ID', $qris->merchant_id],
                        ['Terminal ID', $qris->terminal_id],
                        ['Amount', 'Rp ' . number_format($qris->amount, 0, ',', '.')],
                        ['Status', $qris->status],
                        ['Expired At', $qris->expired_at->format('Y-m-d H:i:s')],
                        ['QR Content Length', strlen($qris->qr_content ?? '') . ' chars'],
                    ]
                );
                
                if ($qris->qr_content) {
                    $this->newLine();
                    $this->info('QR Content (first 100 chars):');
                    $this->line(substr($qris->qr_content, 0, 100) . '...');
                }
                
                return 0;
            } else {
                $this->error('✗ QRIS Generation Failed');
                $this->error('Error: ' . ($result['error'] ?? 'Unknown error'));
                
                if (isset($result['error_code'])) {
                    $this->error('Error Code: ' . $result['error_code']);
                }
                
                return 1;
            }

        } catch (\Exception $e) {
            $this->error('Exception: ' . $e->getMessage());
            $this->error('Trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
}
