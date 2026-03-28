<?php

namespace App\Services;

use App\Models\DokuVirtualAccount;
use App\Models\PaymentGatewayConfig;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class DokuVirtualAccountService
{
    protected $dokuService;
    protected $config;

    public function __construct()
    {
        // Get active DOKU configuration
        $this->config = PaymentGatewayConfig::getActive('doku');
        
        if (!$this->config) {
            throw new \Exception('DOKU payment gateway not configured');
        }

        $this->dokuService = new DokuService($this->config);
    }

    /**
     * Create Virtual Account for payment
     *
     * @param User $user
     * @param string $paymentType (subscription, gift, gallery, music_upload)
     * @param float $amount
     * @param int|null $referenceId
     * @param array $options
     * @return DokuVirtualAccount
     */
    public function createVirtualAccount(
        User $user,
        string $paymentType,
        float $amount,
        ?int $referenceId = null,
        array $options = []
    ): DokuVirtualAccount {
        // Generate unique transaction ID
        $trxId = $this->generateTrxId($paymentType);

        // Generate customer number (user ID padded)
        $customerNo = str_pad($user->id, 10, '0', STR_PAD_LEFT);

        // Partner Service ID from config (first 8 chars of client_id or custom)
        $partnerServiceId = $options['partner_service_id'] ?? substr($this->config->client_id, 0, 8);

        // Generate VA number
        $virtualAccountNo = $partnerServiceId . $customerNo;

        // Channel (default to CIMB)
        $channel = $options['channel'] ?? 'VIRTUAL_ACCOUNT_BANK_CIMB';

        // Expiry (default 24 hours)
        $expiredAt = isset($options['expired_hours']) 
            ? now()->addHours($options['expired_hours'])
            : now()->addHours(24);

        // Create VA record in database
        $va = DokuVirtualAccount::create([
            'user_id' => $user->id,
            'partner_service_id' => $partnerServiceId,
            'customer_no' => $customerNo,
            'virtual_account_no' => $virtualAccountNo,
            'virtual_account_name' => $user->name,
            'virtual_account_email' => $user->email,
            'virtual_account_phone' => $user->phone ?? '',
            'trx_id' => $trxId,
            'amount' => $amount,
            'currency' => 'IDR',
            'payment_type' => $paymentType,
            'reference_id' => $referenceId,
            'channel' => $channel,
            'trx_type' => $options['trx_type'] ?? 'C', // C = Closed Amount
            'reusable' => $options['reusable'] ?? false,
            'min_amount' => $options['min_amount'] ?? null,
            'max_amount' => $options['max_amount'] ?? null,
            'expired_at' => $expiredAt,
            'status' => 'pending',
        ]);

        // Create VA in DOKU
        try {
            $dokuResponse = $this->createVAInDoku($va);

            // Update VA with DOKU response
            $va->update([
                'status' => 'active',
                'doku_response' => $dokuResponse,
                'doku_reference_no' => $dokuResponse['virtualAccountData']['partnerServiceId'] ?? null,
            ]);

            return $va->fresh();
        } catch (\Exception $e) {
            // Mark as failed
            $va->update([
                'status' => 'cancelled',
                'doku_response' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    /**
     * Create VA in DOKU API
     */
    protected function createVAInDoku(DokuVirtualAccount $va): array
    {
        // Prepare request data using DOKU library models
        $createVaRequestDto = new \Doku\Snap\Models\VA\Request\CreateVaRequestDto(
            $va->partner_service_id,
            $va->customer_no,
            $va->virtual_account_no,
            $va->virtual_account_name,
            $va->virtual_account_email,
            $va->virtual_account_phone,
            $va->trx_id,
            new \Doku\Snap\Models\TotalAmount\TotalAmount(
                number_format($va->amount, 2, '.', ''),
                $va->currency
            ),
            new \Doku\Snap\Models\VA\AdditionalInfo\CreateVaRequestAdditionalInfo(
                $va->channel,
                new \Doku\Snap\Models\VA\VirtualAccountConfig\CreateVaVirtualAccountConfig(
                    $va->reusable,
                    $va->min_amount ? number_format($va->min_amount, 2, '.', '') : null,
                    $va->max_amount ? number_format($va->max_amount, 2, '.', '') : null
                )
            ),
            $va->trx_type,
            $va->expired_at ? $va->expired_at->format('Y-m-d\TH:i:sP') : null
        );

        // Call DOKU API
        $response = $this->dokuService->createVirtualAccount($createVaRequestDto);

        // Convert response to array
        return json_decode(json_encode($response), true);
    }

    /**
     * Check VA status from DOKU
     */
    public function checkStatus(DokuVirtualAccount $va): array
    {
        $checkStatusRequestDto = new \Doku\Snap\Models\VA\Request\CheckStatusVaRequestDto(
            $va->partner_service_id,
            $va->customer_no,
            $va->virtual_account_no,
            null, // inquiryRequestId
            null, // paymentRequestId
            null  // additionalInfo
        );

        $response = $this->dokuService->checkVirtualAccountStatus($checkStatusRequestDto);

        return json_decode(json_encode($response), true);
    }

    /**
     * Update VA in DOKU
     */
    public function updateVirtualAccount(DokuVirtualAccount $va, array $data): array
    {
        $updateVaRequestDto = new \Doku\Snap\Models\VA\Request\UpdateVaRequestDto(
            $va->partner_service_id,
            $va->customer_no,
            $va->virtual_account_no,
            $data['virtual_account_name'] ?? $va->virtual_account_name,
            $data['virtual_account_email'] ?? $va->virtual_account_email,
            $data['virtual_account_phone'] ?? $va->virtual_account_phone,
            $va->trx_id,
            new \Doku\Snap\Models\TotalAmount\TotalAmount(
                number_format($data['amount'] ?? $va->amount, 2, '.', ''),
                $va->currency
            ),
            new \Doku\Snap\Models\VA\AdditionalInfo\UpdateVaRequestAdditionalInfo(
                $va->channel,
                new \Doku\Snap\Models\VA\VirtualAccountConfig\UpdateVaVirtualAccountConfig(
                    'ACTIVE',
                    $data['min_amount'] ?? ($va->min_amount ? number_format($va->min_amount, 2, '.', '') : null),
                    $data['max_amount'] ?? ($va->max_amount ? number_format($va->max_amount, 2, '.', '') : null)
                )
            ),
            $data['trx_type'] ?? $va->trx_type,
            isset($data['expired_at']) ? Carbon::parse($data['expired_at'])->format('Y-m-d\TH:i:sP') : $va->expired_at->format('Y-m-d\TH:i:sP')
        );

        $response = $this->dokuService->updateVirtualAccount($updateVaRequestDto);

        // Update local record
        $va->update($data);

        return json_decode(json_encode($response), true);
    }

    /**
     * Delete/Cancel VA
     */
    public function deleteVirtualAccount(DokuVirtualAccount $va): array
    {
        $deleteVaRequestDto = new \Doku\Snap\Models\VA\Request\DeleteVaRequestDto(
            $va->partner_service_id,
            $va->customer_no,
            $va->virtual_account_no,
            $va->trx_id,
            new \Doku\Snap\Models\VA\AdditionalInfo\DeleteVaRequestAdditionalInfo($va->channel)
        );

        $response = $this->dokuService->deleteVirtualAccount($deleteVaRequestDto);

        // Mark as cancelled
        $va->markAsCancelled();

        return json_decode(json_encode($response), true);
    }

    /**
     * Generate unique transaction ID
     */
    protected function generateTrxId(string $paymentType): string
    {
        $prefix = match($paymentType) {
            'subscription' => 'SUB',
            'gift' => 'GIFT',
            'gallery' => 'GAL',
            'music_upload' => 'MUSIC',
            default => 'PAY',
        };

        return $prefix . '-' . strtoupper(Str::random(8)) . '-' . time();
    }

    /**
     * Get available channels
     */
    public static function getAvailableChannels(): array
    {
        return [
            'VIRTUAL_ACCOUNT_BANK_CIMB' => 'CIMB Niaga',
            'VIRTUAL_ACCOUNT_BANK_MANDIRI' => 'Mandiri',
            'VIRTUAL_ACCOUNT_BANK_BRI' => 'BRI',
            'VIRTUAL_ACCOUNT_BANK_BNI' => 'BNI',
            'VIRTUAL_ACCOUNT_BANK_PERMATA' => 'Permata',
        ];
    }
}
