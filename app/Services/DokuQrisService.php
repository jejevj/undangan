<?php

namespace App\Services;

use App\Models\DokuQrisPayment;
use App\Models\PaymentGatewayConfig;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class DokuQrisService
{
    protected $config;
    protected $dokuService;

    public function __construct()
    {
        $this->config = PaymentGatewayConfig::getActive('doku');
        if ($this->config) {
            $this->dokuService = new DokuService($this->config);
        }
    }

    /**
     * Generate QRIS for payment
     */
    public function generateQris(User $user, array $options = [])
    {
        try {
            Log::channel('va')->info('=== QRIS Generation Request Started ===', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'payment_type' => $options['payment_type'] ?? 'subscription',
                'amount' => $options['amount'] ?? 0,
                'reference_id' => $options['reference_id'] ?? null,
                'timestamp' => now()->toISOString(),
            ]);

            // Generate partner reference number (our transaction ID)
            $partnerReferenceNo = $this->generatePartnerReferenceNo($options['payment_type'] ?? 'subscription');

            // Get merchant ID and terminal ID from config
            // For QRIS, merchantId might be different from client_id (Brand ID)
            // Use merchant_id if available, otherwise fallback to client_id
            $merchantId = $this->config->merchant_id ?? $this->config->client_id;
            // Terminal ID must be alphanumeric 1-16 characters
            // Use fixed terminal ID: A01
            $terminalId = 'A01';

            // Calculate expiry (default 30 minutes)
            $expiryMinutes = $options['expired_minutes'] ?? 30;
            $expiredAt = now()->addMinutes($expiryMinutes);

            // Create QRIS record in database
            $qris = DokuQrisPayment::create([
                'user_id' => $user->id,
                'partner_reference_no' => $partnerReferenceNo,
                'merchant_id' => $merchantId,
                'terminal_id' => $terminalId,
                'amount' => $options['amount'],
                'currency' => 'IDR',
                'payment_type' => $options['payment_type'] ?? 'subscription',
                'reference_id' => $options['reference_id'] ?? null,
                'status' => 'pending',
                'expired_at' => $expiredAt,
                'postal_code' => $options['postal_code'] ?? '12345',
                'fee_type' => '1', // No Tips
            ]);

            Log::channel('va')->info('QRIS record created in database', [
                'qris_id' => $qris->id,
                'partner_reference_no' => $partnerReferenceNo,
                'amount' => $options['amount'],
                'expired_at' => $expiredAt->toISOString(),
            ]);

            // Call DOKU API to generate QRIS
            Log::channel('va')->info('Calling DOKU API to generate QRIS');

            $response = $this->callGenerateQrisAPI($qris);

            if ($response['success']) {
                // Update QRIS record with DOKU response
                $qris->update([
                    'reference_no' => $response['data']['referenceNo'] ?? null,
                    'qr_content' => $response['data']['qrContent'] ?? null,
                    'doku_response' => $response['data'],
                ]);

                Log::channel('va')->info('=== QRIS Generation Completed Successfully ===', [
                    'qris_id' => $qris->id,
                    'reference_no' => $qris->reference_no,
                    'status' => 'success',
                ]);

                return [
                    'success' => true,
                    'qris' => $qris->fresh(),
                ];
            } else {
                // Mark as failed
                $qris->update(['status' => 'failed']);

                Log::channel('va')->error('QRIS generation failed', [
                    'error' => $response['error'] ?? 'Unknown error',
                ]);

                return [
                    'success' => false,
                    'error' => $response['error'] ?? 'Failed to generate QRIS',
                ];
            }

        } catch (\Exception $e) {
            Log::channel('va')->error('Exception during QRIS generation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Call DOKU API to generate QRIS
     */
    protected function callGenerateQrisAPI(DokuQrisPayment $qris): array
    {
        try {
            $isProduction = $this->config->environment === 'production';
            $baseUrl = $isProduction ? 'https://api.doku.com' : 'https://api-sandbox.doku.com';
            $endpoint = '/snap-adapter/b2b/v1.0/qr/qr-mpm-generate';
            $url = $baseUrl . $endpoint;

            // Get B2B token
            $tokenB2B = $this->dokuService->getTokenB2B();

            // Prepare request body
            $requestBody = [
                'partnerReferenceNo' => $qris->partner_reference_no,
                'amount' => [
                    'value' => number_format($qris->amount, 2, '.', ''),
                    'currency' => $qris->currency,
                ],
                'merchantId' => $qris->merchant_id,
                'terminalId' => $qris->terminal_id,
                'validityPeriod' => $qris->expired_at->format('Y-m-d\TH:i:sP'), // ISO 8601 with timezone
                'additionalInfo' => [
                    'postalCode' => $qris->postal_code,
                    'feeType' => $qris->fee_type,
                ],
            ];

            $requestBodyJson = json_encode($requestBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            // Generate timestamp
            $timestamp = now()->format('Y-m-d\TH:i:sP');

            // Generate signature (HMAC SHA512)
            $bodyHash = hash('sha256', $requestBodyJson);
            $bodyHashHex = strtolower($bodyHash);
            $stringToSign = "POST:{$endpoint}:{$tokenB2B}:{$bodyHashHex}:{$timestamp}";

            $secretKey = $this->config->decrypted_secret_key;
            $signature = hash_hmac('sha512', $stringToSign, $secretKey, true);
            $signatureBase64 = base64_encode($signature);

            // Generate external ID
            $externalId = 'QRIS-' . strtoupper(uniqid());

            Log::channel('va')->info('=== DOKU QRIS API Request ===', [
                'url' => $url,
                'request_body' => $requestBody,
                'timestamp' => $timestamp,
                'external_id' => $externalId,
            ]);

            // Make HTTP request
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $tokenB2B,
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signatureBase64,
                'X-PARTNER-ID' => $this->config->client_id,
                'X-EXTERNAL-ID' => $externalId,
                'CHANNEL-ID' => 'H2H',
            ])->post($url, $requestBody);

            $responseData = $response->json();

            Log::channel('va')->info('=== DOKU QRIS API Response ===', [
                'status_code' => $response->status(),
                'response' => $responseData,
            ]);

            // Check if successful (response code 2004700 or 2005500 for QRIS)
            if ($response->successful() && isset($responseData['responseCode']) && in_array($responseData['responseCode'], ['2004700', '2005500'])) {
                return [
                    'success' => true,
                    'data' => $responseData,
                ];
            }

            // Log detailed error
            $errorMessage = $responseData['responseMessage'] ?? 'Unknown error';
            $errorCode = $responseData['responseCode'] ?? 'N/A';
            
            Log::channel('va')->error('DOKU QRIS API Error', [
                'error_code' => $errorCode,
                'error_message' => $errorMessage,
                'full_response' => $responseData,
            ]);

            // Provide more helpful error message for common issues
            if ($errorCode === '5004701') {
                $errorMessage = 'QRIS belum diaktifkan untuk merchant ini. Silakan hubungi DOKU untuk aktivasi QRIS atau gunakan metode pembayaran lain (Virtual Account / E-Wallet).';
            }

            return [
                'success' => false,
                'error' => $errorMessage,
                'error_code' => $errorCode,
                'data' => $responseData,
            ];

        } catch (\Exception $e) {
            Log::channel('va')->error('Error calling DOKU QRIS API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate partner reference number
     */
    protected function generatePartnerReferenceNo(string $paymentType): string
    {
        $prefix = strtoupper(substr($paymentType, 0, 3));
        $random = strtoupper(substr(uniqid(), -8));
        $timestamp = time();
        
        return "{$prefix}-{$random}-{$timestamp}";
    }

    /**
     * Query QRIS status
     */
    public function queryQris(DokuQrisPayment $qris): array
    {
        try {
            $isProduction = $this->config->environment === 'production';
            $baseUrl = $isProduction ? 'https://api.doku.com' : 'https://api-sandbox.doku.com';
            $endpoint = '/snap-adapter/b2b/v1.0/qr/qr-mpm-query';
            $url = $baseUrl . $endpoint;

            $tokenB2B = $this->dokuService->getTokenB2B();

            $requestBody = [
                'originalReferenceNo' => $qris->reference_no,
                'originalPartnerReferenceNo' => $qris->partner_reference_no,
                'serviceCode' => '47',
                'merchantId' => $qris->merchant_id,
            ];

            $requestBodyJson = json_encode($requestBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $timestamp = now()->format('Y-m-d\TH:i:sP');

            $bodyHash = hash('sha256', $requestBodyJson);
            $bodyHashHex = strtolower($bodyHash);
            $stringToSign = "POST:{$endpoint}:{$tokenB2B}:{$bodyHashHex}:{$timestamp}";

            $secretKey = $this->config->decrypted_secret_key;
            $signature = hash_hmac('sha512', $stringToSign, $secretKey, true);
            $signatureBase64 = base64_encode($signature);

            $externalId = 'QRY-' . strtoupper(uniqid());

            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $tokenB2B,
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signatureBase64,
                'X-PARTNER-ID' => $this->config->client_id,
                'X-EXTERNAL-ID' => $externalId,
                'CHANNEL-ID' => 'H2H',
            ])->post($url, $requestBody);

            $responseData = $response->json();

            Log::channel('va')->info('QRIS Query Response', [
                'qris_id' => $qris->id,
                'response' => $responseData,
            ]);

            return [
                'success' => $response->successful(),
                'data' => $responseData,
            ];

        } catch (\Exception $e) {
            Log::channel('va')->error('Error querying QRIS', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
