<?php

namespace App\Services;

use App\Models\DokuEWalletPayment;
use App\Models\PaymentGatewayConfig;
use App\Models\User;
use Illuminate\Support\Str;

class DokuEWalletService
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
     * Create E-Wallet Payment
     *
     * @param User $user
     * @param string $paymentType (subscription, gift, gallery, music_upload)
     * @param float $amount
     * @param string $channel (EMONEY_SHOPEE_PAY_SNAP, EMONEY_DANA_SNAP, EMONEY_OVO_SNAP)
     * @param int|null $referenceId
     * @param array $options
     * @return DokuEWalletPayment
     */
    public function createPayment(
        User $user,
        string $paymentType,
        float $amount,
        string $channel,
        ?int $referenceId = null,
        array $options = []
    ): DokuEWalletPayment {
        // Check if payment already exists for this reference
        if ($referenceId) {
            $existingPayment = DokuEWalletPayment::where('payment_type', $paymentType)
                ->where('reference_id', $referenceId)
                ->whereIn('status', ['pending', 'processing'])
                ->where('expired_at', '>', now())
                ->first();

            if ($existingPayment) {
                \Log::info('Reusing existing E-Wallet payment', [
                    'payment_id' => $existingPayment->id,
                    'partner_reference_no' => $existingPayment->partner_reference_no,
                    'payment_type' => $paymentType,
                    'reference_id' => $referenceId,
                ]);
                return $existingPayment;
            }
        }

        // Generate unique partner reference number
        $partnerReferenceNo = $this->generatePartnerReferenceNo($paymentType);

        // Expiry (default 30 minutes for e-wallet)
        $expiredAt = isset($options['expired_minutes']) 
            ? now()->addMinutes($options['expired_minutes'])
            : now()->addMinutes(30);

        // Create payment record in database
        $payment = DokuEWalletPayment::create([
            'user_id' => $user->id,
            'partner_reference_no' => $partnerReferenceNo,
            'amount' => $amount,
            'currency' => 'IDR',
            'payment_type' => $paymentType,
            'reference_id' => $referenceId,
            'channel' => $channel,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'customer_phone' => $user->phone ?? '',
            'expired_at' => $expiredAt,
            'status' => 'pending',
        ]);

        // Create payment in DOKU
        try {
            $dokuResponse = $this->createPaymentInDoku($payment, $options);

            // Update payment with DOKU response
            $payment->update([
                'status' => 'processing',
                'web_redirect_url' => $dokuResponse['webRedirectUrl'] ?? null,
                'mobile_deep_link' => $dokuResponse['mobileDeepLink'] ?? null,
                'doku_response' => $dokuResponse,
            ]);

            return $payment->fresh();
        } catch (\Exception $e) {
            // Mark as failed
            $payment->update([
                'status' => 'failed',
                'doku_response' => ['error' => $e->getMessage()],
            ]);

            throw $e;
        }
    }

    /**
     * Create payment in DOKU API
     */
    protected function createPaymentInDoku(DokuEWalletPayment $payment, array $options): array
    {
        // Success and failed URLs
        $successUrl = $options['success_url'] ?? route('ewallet.success', $payment);
        $failedUrl = $options['failed_url'] ?? route('ewallet.failed', $payment);

        // Valid up to (ISO 8601 format)
        $validUpTo = $payment->expired_at->format('Y-m-d\TH:i:sP');

        // URL Param array
        $urlParam = [
            [
                'url' => $successUrl,
                'type' => 'PAY_RETURN',
                'isDeepLink' => 'N'  // Note: capital L in isDeepLink
            ]
        ];

        // Prepare request data using DOKU library models
        $paymentRequestDto = new \Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppRequestDto(
            $payment->partner_reference_no,
            $validUpTo,
            '01', // pointOfInitiation: 01 = QR Static, 02 = QR Dynamic
            $urlParam,
            new \Doku\Snap\Models\TotalAmount\TotalAmount(
                number_format($payment->amount, 2, '.', ''),
                $payment->currency
            ),
            new \Doku\Snap\Models\PaymentJumpApp\PaymentJumpAppAdditionalInfoRequestDto(
                $payment->channel,
                'Subscription Payment', // orderTitle
                null, // metadata
                null  // supportDeepLinkCheckoutUrl
            )
        );

        // Call DOKU API
        $response = $this->dokuService->doPaymentJumpApp(
            $paymentRequestDto,
            request()->header('User-Agent') ?? 'Unknown',
            request()->ip()
        );

        // Convert response to array
        return json_decode(json_encode($response), true);
    }

    /**
     * Check payment status from DOKU
     */
    public function checkStatus(DokuEWalletPayment $payment): array
    {
        $checkStatusRequestDto = new \Doku\Snap\Models\CheckStatus\CheckStatusRequestDto(
            $payment->partner_reference_no,
            null, // serviceCode
            null, // transactionDate
            null, // externalStoreId
            null, // amount
            null, // merchantId
            null, // subMerchantId
            null, // storeId
            new \Doku\Snap\Models\CheckStatus\CheckStatusAdditionalInfoRequestDto(
                request()->header('User-Agent') ?? 'Unknown',
                $payment->channel
            )
        );

        $response = $this->dokuService->checkStatus($checkStatusRequestDto);

        return json_decode(json_encode($response), true);
    }

    /**
     * Generate unique partner reference number
     */
    protected function generatePartnerReferenceNo(string $paymentType): string
    {
        $prefix = match($paymentType) {
            'subscription' => 'SUB',
            'gift' => 'GIFT',
            'gallery' => 'GAL',
            'music_upload' => 'MUSIC',
            default => 'PAY',
        };

        return $prefix . '-EW-' . strtoupper(Str::random(8)) . '-' . time();
    }

    /**
     * Get available e-wallet channels from database
     */
    public static function getAvailableChannels(): array
    {
        $channels = \App\Models\PaymentChannel::getEWalletChannels();
        
        $result = [];
        foreach ($channels as $channel) {
            $result[$channel->code] = [
                'name' => $channel->name,
                'icon' => $channel->icon,
                'description' => $channel->description,
            ];
        }
        
        // Fallback to static list if no channels in database
        if (empty($result)) {
            return [
                'EMONEY_SHOPEE_PAY_SNAP' => [
                    'name' => 'ShopeePay',
                    'icon' => 'shopee-pay.png',
                    'description' => 'Bayar dengan ShopeePay',
                ],
                'EMONEY_DANA_SNAP' => [
                    'name' => 'DANA',
                    'icon' => 'dana.png',
                    'description' => 'Bayar dengan DANA',
                ],
                'EMONEY_OVO_SNAP' => [
                    'name' => 'OVO',
                    'icon' => 'ovo.png',
                    'description' => 'Bayar dengan OVO',
                ],
            ];
        }
        
        return $result;
    }
}
