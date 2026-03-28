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
        $logContext = [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'payment_type' => $paymentType,
            'amount' => $amount,
            'reference_id' => $referenceId,
            'options' => $options,
            'timestamp' => now()->toIso8601String(),
        ];

        \Log::channel('va')->info('=== VA Creation Request Started ===', $logContext);

        // Check if VA already exists for this payment
        if ($referenceId) {
            $existingVA = DokuVirtualAccount::where('payment_type', $paymentType)
                ->where('reference_id', $referenceId)
                ->whereIn('status', ['pending', 'active'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingVA) {
                \Log::channel('va')->info('Reusing existing VA', [
                    'va_id' => $existingVA->id,
                    'va_number' => $existingVA->virtual_account_no,
                    'payment_type' => $paymentType,
                    'reference_id' => $referenceId,
                ]);
                return $existingVA;
            }
        }

        // Generate unique transaction ID
        $trxId = $this->generateTrxId($paymentType);

        // Channel (default to CIMB) - using DOKU format without _BANK_
        $channel = $options['channel'] ?? 'VIRTUAL_ACCOUNT_CIMB';

        // Get BIN from payment channel
        $paymentChannel = \App\Models\PaymentChannel::where('code', $channel)->first();
        
        if (!$paymentChannel) {
            throw new \Exception("Payment channel {$channel} not found in database");
        }
        
        // Use partner_service_id if configured, otherwise use bin
        $rawBin = $paymentChannel->partner_service_id ?? $paymentChannel->bin;
        
        if (empty($rawBin)) {
            throw new \Exception("BIN/Partner Service ID not configured for channel {$channel}. Please configure in payment channel settings or contact DOKU support.");
        }
        
        // Partner Service ID: DOKU requires exactly 8 characters in API request
        // If PSID < 8 chars, left-pad with spaces
        // Example: '13925' (5 chars) → '   13925' (3 spaces + 5 digits = 8 chars)
        // Example: '98829172' (8 chars) → '98829172' (no padding)
        $partnerServiceId = str_pad($rawBin, 8, ' ', STR_PAD_LEFT);
        
        // Validate: must be exactly 8 characters after padding
        if (strlen($partnerServiceId) !== 8) {
            $error = "Invalid Partner Service ID for channel {$channel}. After padding, must be exactly 8 characters. Got: '{$partnerServiceId}' (length: " . strlen($partnerServiceId) . "). Raw BIN: '{$rawBin}'";
            \Log::channel('va')->error('Partner Service ID validation failed', [
                'error' => $error,
                'channel' => $channel,
                'raw_bin' => $rawBin,
                'formatted_psid' => $partnerServiceId,
            ]);
            throw new \Exception($error);
        }
        
        \Log::channel('va')->info('Partner Service ID formatted', [
            'channel' => $channel,
            'raw_psid' => $rawBin,
            'raw_length' => strlen($rawBin),
            'formatted_psid' => $partnerServiceId,
            'formatted_length' => strlen($partnerServiceId),
            'bank' => $paymentChannel->name,
            'billing_type' => $paymentChannel->billing_type,
            'feature' => $paymentChannel->feature,
            'va_trx_type' => $paymentChannel->va_trx_type,
        ]);

        // Generate customer number
        // CRITICAL: For DOKU API, virtualAccountNo MUST be literal concatenation of:
        //   partnerServiceId (8 chars with spaces) + customerNo
        // 
        // IMPORTANT: We send our generated values to DOKU, and DOKU may return
        // different customerNo and virtualAccountNo in the response.
        // We MUST update our database with DOKU's returned values.
        //
        // For DGPC: We use 8-digit customer number for consistency
        // DOKU will generate the actual customer number and return it in response
        
        $customerNoLength = 8; // Always use 8 digits for customer number
        
        // Generate unique customer number using user_id + timestamp
        $timestamp = substr((string)time(), -4); // Last 4 digits of timestamp
        $userIdPart = str_pad($user->id, 4, '0', STR_PAD_LEFT); // First 4 digits for user_id
        $customerNo = $userIdPart . $timestamp; // Total 8 digits

        // Generate VA number: LITERAL concatenation of partnerServiceId + customerNo
        // This MUST include the spaces from partnerServiceId
        $baseVirtualAccountNo = $partnerServiceId . $customerNo;
        $virtualAccountNo = $baseVirtualAccountNo;
        
        // Expected total length: 8 (partnerServiceId) + 8 (customerNo) = 16 chars
        $expectedLength = 16;
        if (strlen($virtualAccountNo) !== $expectedLength) {
            throw new \Exception("VA Number length mismatch for {$channel}. Expected: {$expectedLength} chars, Got: " . strlen($virtualAccountNo) . " chars. VA: '{$virtualAccountNo}'");
        }
        
        // Check for uniqueness and add suffix if needed
        $suffix = 0;
        while (DokuVirtualAccount::where('virtual_account_no', $virtualAccountNo)->exists()) {
            $suffix++;
            
            // Modify last digits of customer number
            $suffixLength = strlen((string)$suffix);
            $modifiedCustomerNo = substr($customerNo, 0, -$suffixLength) . $suffix;
            $virtualAccountNo = $partnerServiceId . $modifiedCustomerNo;
            
            // Prevent infinite loop
            if ($suffix > 999) {
                throw new \Exception('Unable to generate unique VA number after 999 attempts');
            }
        }
        
        \Log::channel('va')->info('Generated VA Number', [
            'partner_service_id' => $partnerServiceId,
            'partner_service_id_length' => strlen($partnerServiceId),
            'partner_service_id_visual' => str_replace(' ', '·', $partnerServiceId),
            'customer_no' => $customerNo,
            'customer_no_length' => strlen($customerNo),
            'va_number' => $virtualAccountNo,
            'va_number_length' => strlen($virtualAccountNo),
            'va_number_visual' => str_replace(' ', '·', $virtualAccountNo),
            'suffix' => $suffix,
            'note' => 'DOKU will return actual VA number in response which may differ',
        ]);

        // Expiry (default 24 hours)
        $expiredAt = isset($options['expired_hours']) 
            ? now()->addHours($options['expired_hours'])
            : now()->addHours(24);

        // Phone validation: must be 9-30 characters
        $phone = $options['phone'] ?? $user->phone ?? '';
        
        // If phone is empty or too short, use default format
        if (empty($phone) || strlen($phone) < 9) {
            // Default phone: 62 + user_id padded to 11 digits = 13 chars
            $phone = '62' . str_pad($user->id, 11, '0', STR_PAD_LEFT);
        }
        
        // Ensure phone starts with country code (62 for Indonesia)
        if (!str_starts_with($phone, '62') && !str_starts_with($phone, '+62')) {
            // Remove leading 0 if exists
            $phone = ltrim($phone, '0');
            $phone = '62' . $phone;
        }
        
        // Remove + if exists
        $phone = str_replace('+', '', $phone);
        
        // Validate length: must be 9-30 characters
        if (strlen($phone) < 9) {
            $phone = str_pad($phone, 9, '0', STR_PAD_RIGHT);
        } elseif (strlen($phone) > 30) {
            $phone = substr($phone, 0, 30);
        }

        // Create VA record in database
        $va = DokuVirtualAccount::create([
            'user_id' => $user->id,
            'partner_service_id' => $partnerServiceId,
            'customer_no' => $customerNo,
            'virtual_account_no' => $virtualAccountNo,
            'virtual_account_name' => $user->name,
            'virtual_account_email' => $user->email,
            'virtual_account_phone' => $phone,
            'trx_id' => $trxId,
            'amount' => $amount,
            'currency' => 'IDR',
            'payment_type' => $paymentType,
            'reference_id' => $referenceId,
            'channel' => $channel,
            'trx_type' => $options['trx_type'] ?? $paymentChannel->va_trx_type ?? 'C', // C = Closed Amount, O = Open Amount
            'reusable' => $options['reusable'] ?? false,
            'min_amount' => $options['min_amount'] ?? null,
            'max_amount' => $options['max_amount'] ?? null,
            'expired_at' => $expiredAt,
            'status' => 'pending',
        ]);

        \Log::channel('va')->info('VA record created in database', [
            'va_id' => $va->id,
            'trx_id' => $trxId,
            'va_number' => $virtualAccountNo,
            'amount' => $amount,
            'expired_at' => $expiredAt->toIso8601String(),
        ]);

        // Create VA in DOKU
        try {
            \Log::channel('va')->info('Calling DOKU API to create VA');
            
            $dokuResponse = $this->createVAInDoku($va);

            \Log::channel('va')->info('DOKU API response received', [
                'response' => $dokuResponse,
            ]);

            // Update VA with DOKU response
            // IMPORTANT: DOKU may return different customerNo and virtualAccountNo
            // We must use the values returned by DOKU, not our generated values
            $dokuVaData = $dokuResponse['virtualAccountData'] ?? [];
            
            $va->update([
                'status' => 'active',
                'doku_response' => $dokuResponse,
                'doku_reference_no' => $dokuVaData['partnerServiceId'] ?? null,
                // Update with actual values from DOKU
                'virtual_account_no' => $dokuVaData['virtualAccountNo'] ?? $va->virtual_account_no,
                'customer_no' => $dokuVaData['customerNo'] ?? $va->customer_no,
                'partner_service_id' => $dokuVaData['partnerServiceId'] ?? $va->partner_service_id,
            ]);

            \Log::channel('va')->info('=== VA Creation Completed Successfully ===', [
                'va_id' => $va->id,
                'va_number_sent' => $virtualAccountNo,
                'va_number_from_doku' => $dokuVaData['virtualAccountNo'] ?? null,
                'customer_no_sent' => $customerNo,
                'customer_no_from_doku' => $dokuVaData['customerNo'] ?? null,
                'status' => 'active',
            ]);

            return $va->fresh();
        } catch (\Exception $e) {
            \Log::channel('va')->error('=== VA Creation Failed ===', [
                'va_id' => $va->id,
                'error_message' => $e->getMessage(),
                'error_trace' => $e->getTraceAsString(),
            ]);

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
        // Log before creating DTO
        \Log::channel('va')->info('=== Preparing DOKU API Request ===', [
            'va_id' => $va->id,
            'partner_service_id' => $va->partner_service_id,
            'customer_no' => $va->customer_no,
            'virtual_account_no' => $va->virtual_account_no,
            'trx_id' => $va->trx_id,
            'amount' => $va->amount,
            'channel' => $va->channel,
        ]);

        // Prepare request data using DOKU library models
        try {
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

            \Log::channel('va')->info('DTO Created Successfully', [
                'dto_class' => get_class($createVaRequestDto),
            ]);

        } catch (\Exception $e) {
            \Log::channel('va')->error('Failed to create DTO', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }

        \Log::channel('va')->info('DOKU API Request Payload', [
            'partner_service_id' => $va->partner_service_id,
            'partner_service_id_length' => strlen($va->partner_service_id),
            'customer_no' => $va->customer_no,
            'customer_no_length' => strlen($va->customer_no),
            'virtual_account_no' => $va->virtual_account_no,
            'virtual_account_no_length' => strlen($va->virtual_account_no),
            'virtual_account_name' => $va->virtual_account_name,
            'virtual_account_email' => $va->virtual_account_email,
            'virtual_account_phone' => $va->virtual_account_phone,
            'trx_id' => $va->trx_id,
            'amount' => number_format($va->amount, 2, '.', ''),
            'currency' => $va->currency,
            'channel' => $va->channel,
            'trx_type' => $va->trx_type,
            'expired_at' => $va->expired_at ? $va->expired_at->format('Y-m-d\TH:i:sP') : null,
            'reusable' => $va->reusable,
        ]);

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
     * Get available channels from database
     */
    public static function getAvailableChannels(): array
    {
        $channels = \App\Models\PaymentChannel::getVirtualAccountChannels();
        
        $result = [];
        foreach ($channels as $channel) {
            $result[$channel->code] = $channel->name;
        }
        
        // Fallback to static list if no channels in database (using DOKU format without _BANK_)
        if (empty($result)) {
            return [
                'VIRTUAL_ACCOUNT_CIMB' => 'CIMB Niaga',
                'VIRTUAL_ACCOUNT_MANDIRI' => 'Mandiri',
                'VIRTUAL_ACCOUNT_BRI' => 'BRI',
                'VIRTUAL_ACCOUNT_BNI' => 'BNI',
                'VIRTUAL_ACCOUNT_PERMATA' => 'Permata',
            ];
        }
        
        return $result;
    }
}
