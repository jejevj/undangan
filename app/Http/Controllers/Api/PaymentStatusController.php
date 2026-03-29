<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DokuVirtualAccount;
use App\Models\DokuEWalletPayment;
use App\Models\PaymentGatewayConfig;
use App\Services\DokuVirtualAccountService;
use App\Services\EventTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentStatusController extends Controller
{
    /**
     * Check VA payment status from DOKU API
     */
    public function checkVAStatus(Request $request)
    {
        $request->validate([
            'va_id' => 'required|integer|exists:doku_virtual_accounts,id',
        ]);

        try {
            $va = DokuVirtualAccount::findOrFail($request->va_id);

            // Check if user owns this VA
            if ($va->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            Log::channel('va')->info('Manual status check requested', [
                'va_id' => $va->id,
                'user_id' => auth()->id(),
                'va_number' => $va->virtual_account_no,
            ]);

            // If already paid, return immediately
            if ($va->status === 'paid') {
                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'message' => 'Pembayaran sudah berhasil',
                    'data' => [
                        'va_number' => $va->display_va_number,
                        'amount' => $va->amount,
                        'paid_at' => $va->paid_at?->format('d M Y H:i'),
                        'status' => $va->status,
                    ],
                ]);
            }

            // Check if expired
            if ($va->expired_at && $va->expired_at->isPast()) {
                $va->markAsExpired();
                
                return response()->json([
                    'success' => false,
                    'status' => 'expired',
                    'message' => 'Virtual Account sudah kadaluarsa',
                    'data' => [
                        'va_number' => $va->display_va_number,
                        'expired_at' => $va->expired_at->format('d M Y H:i'),
                        'status' => 'expired',
                    ],
                ]);
            }

            // Check status from DOKU API
            try {
                $statusResult = $this->checkStatusFromDoku($va);
                
                Log::channel('va')->info('DOKU status check response', [
                    'va_id' => $va->id,
                    'response' => $statusResult,
                ]);

                if ($statusResult['success']) {
                    $responseData = $statusResult['data'];
                    
                    // Get virtualAccountData from response
                    $vaData = $responseData['virtualAccountData'] ?? null;
                    
                    if ($vaData) {
                        // Check payment flag reason first (most reliable indicator)
                        $paymentFlagReason = $vaData['paymentFlagReason']['english'] ?? '';
                        
                        // Check paid amount vs bill amount
                        $paidAmount = $vaData['paidAmount']['value'] ?? 0;
                        
                        // Bill amount can be in billAmount or billDetails array
                        $billAmount = 0;
                        if (isset($vaData['billAmount']['value'])) {
                            $billAmount = $vaData['billAmount']['value'];
                        } elseif (isset($vaData['billDetails'][0]['billAmount']['value'])) {
                            $billAmount = $vaData['billDetails'][0]['billAmount']['value'];
                        }
                        
                        // Payment is successful only if:
                        // 1. paymentFlagReason is "Success" (not "Pending")
                        // 2. paidAmount > 0 and >= billAmount
                        if (strtolower($paymentFlagReason) === 'success' && $paidAmount > 0 && $paidAmount >= $billAmount) {
                            // Mark as paid
                            $va->markAsPaid();
                            $va->update([
                                'doku_response' => array_merge($va->doku_response ?? [], [
                                    'status_check' => $responseData,
                                    'status_checked_at' => now()->toISOString(),
                                ]),
                            ]);

                            // Activate subscription if needed
                            if ($va->payment_type === 'subscription') {
                                $this->activateSubscription($va);
                            }

                            return response()->json([
                                'success' => true,
                                'status' => 'paid',
                                'message' => 'Pembayaran berhasil! Paket Anda akan segera diaktifkan.',
                                'data' => [
                                    'va_number' => $va->display_va_number,
                                    'amount' => $va->amount,
                                    'paid_amount' => $paidAmount,
                                    'status' => 'paid',
                                ],
                            ]);
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::channel('va')->error('Failed to check status from DOKU', [
                    'va_id' => $va->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Still pending
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'Pembayaran belum diterima. Silakan transfer ke nomor VA.',
                'data' => [
                    'va_number' => $va->display_va_number,
                    'amount' => $va->amount,
                    'expired_at' => $va->expired_at->format('d M Y H:i'),
                    'status' => 'pending',
                ],
            ]);

        } catch (\Exception $e) {
            Log::channel('va')->error('Status check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check E-Wallet payment status
     */
    public function checkEWalletStatus(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|integer|exists:doku_ewallet_payments,id',
        ]);

        try {
            $payment = DokuEWalletPayment::findOrFail($request->payment_id);

            // Check if user owns this payment
            if ($payment->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'status' => $payment->status,
                'message' => $this->getEWalletStatusMessage($payment->status),
                'data' => [
                    'channel' => $payment->channel_name,
                    'amount' => $payment->amount,
                    'status' => $payment->status,
                    'web_redirect_url' => $payment->web_redirect_url,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get status message for E-Wallet
     */
    protected function getEWalletStatusMessage($status)
    {
        return match($status) {
            'success' => 'Pembayaran berhasil!',
            'processing' => 'Menunggu pembayaran',
            'pending' => 'Menunggu pembayaran',
            'failed' => 'Pembayaran gagal',
            'expired' => 'Pembayaran kadaluarsa',
            default => 'Status tidak diketahui',
        };
    }

    /**
     * Check status from DOKU API using data from doku_response column
     */
    protected function checkStatusFromDoku(DokuVirtualAccount $va): array
    {
        try {
            // Get DOKU configuration
            $config = PaymentGatewayConfig::getActive('doku');
            if (!$config) {
                throw new \Exception('DOKU configuration not found');
            }

            // Get virtualAccountData from doku_response column
            $dokuResponse = $va->doku_response;
            if (!$dokuResponse || !is_array($dokuResponse)) {
                throw new \Exception('DOKU response data not found in database');
            }

            $clientId = $config->client_id;
            $isProduction = $config->environment === 'production';
            
            // Get base URL
            $baseUrl = $isProduction 
                ? 'https://api.doku.com' 
                : 'https://api-sandbox.doku.com';
            
            // Endpoint
            $endpoint = '/orders/v1.0/transfer-va/status';
            $url = $baseUrl . $endpoint;
            
            // Generate timestamp
            $timestamp = now()->format('Y-m-d\TH:i:sP');
            
            // Get access token using DokuService
            $tokenService = new \App\Services\DokuService($config);
            $tokenB2B = $tokenService->getTokenB2B();
            
            // Use data from doku_response (virtualAccountData) for the request
            // IMPORTANT: partnerServiceId and virtualAccountNo must include spaces as returned by DOKU
            $vaData = $dokuResponse['virtualAccountData'] ?? $dokuResponse;
            
            $requestBody = [
                'partnerServiceId' => $vaData['partnerServiceId'] ?? $va->partner_service_id,
                'customerNo' => $vaData['customerNo'] ?? $va->customer_no,
                'virtualAccountNo' => $vaData['virtualAccountNo'] ?? $va->virtual_account_no,
                'trxId' => $vaData['trxId'] ?? $va->trx_id,
            ];
            
            $requestBodyJson = json_encode($requestBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
            // Generate signature using HMAC SHA512 (symmetric signature)
            // This is how DOKU library generates signatures for API requests
            $bodyHash = hash('sha256', $requestBodyJson);
            $bodyHashHex = strtolower($bodyHash);
            $stringToSign = "POST:{$endpoint}:{$tokenB2B}:{$bodyHashHex}:{$timestamp}";
            
            $secretKey = $config->decrypted_secret_key;
            if (!$secretKey) {
                throw new \Exception('Secret key not found in configuration');
            }
            
            $signature = hash_hmac('sha512', $stringToSign, $secretKey, true);
            $signatureBase64 = base64_encode($signature);
            
            // Generate external ID
            $externalId = 'CHK-' . strtoupper(uniqid());
            
            Log::channel('va')->info('=== Checking VA Status from DOKU API ===', [
                'url' => $url,
                'va_id' => $va->id,
                'request_body' => $requestBody,
                'timestamp' => $timestamp,
                'external_id' => $externalId,
            ]);
            
            // Make HTTP request
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $tokenB2B,
                'X-TIMESTAMP' => $timestamp,
                'X-SIGNATURE' => $signatureBase64,
                'X-PARTNER-ID' => $clientId,
                'X-EXTERNAL-ID' => $externalId,
                'CHANNEL-ID' => 'H2H',
            ])->post($url, $requestBody);
            
            $responseData = $response->json();
            
            Log::channel('va')->info('=== DOKU API Status Check Response ===', [
                'status_code' => $response->status(),
                'response' => $responseData,
            ]);
            
            // Check response code
            // 2002600 = Success (from VaServices.php)
            if ($response->successful() && isset($responseData['responseCode'])) {
                $isSuccess = $responseData['responseCode'] === '2002600';
                
                return [
                    'success' => $isSuccess,
                    'data' => $responseData,
                ];
            }
            
            return [
                'success' => false,
                'data' => $responseData,
            ];
            
        } catch (\Exception $e) {
            Log::channel('va')->error('Error checking status from DOKU', [
                'va_id' => $va->id,
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
     * Activate subscription after payment
     */
    protected function activateSubscription($payment)
    {
        $order = \App\Models\UserSubscription::find($payment->reference_id);
        
        if (!$order) {
            Log::channel('va')->error('Subscription order not found', [
                'reference_id' => $payment->reference_id,
            ]);
            return;
        }

        // Get plan details to set proper expiration
        $plan = $order->plan;
        
        // Update order status and activate subscription
        $order->update([
            'status' => 'active',
            'starts_at' => now(),
            'expires_at' => now()->addDays(30), // Default 30 days, adjust based on plan if needed
            'paid_at' => now(),
            'payment_method' => 'doku_va',
        ]);

        // Track payment completion in funnel
        EventTrackingService::subscriptionFunnel('payment_completed', [
            'order_id' => $order->id,
            'plan_id' => $order->pricing_plan_id,
            'plan_name' => $plan->name ?? 'Unknown',
            'amount' => $order->amount,
            'payment_method' => get_class($payment),
            'check_method' => 'manual',
        ]);

        Log::channel('va')->info('Subscription activated via manual check', [
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'plan_id' => $order->pricing_plan_id,
            'plan_name' => $plan->name ?? 'Unknown',
            'starts_at' => $order->starts_at,
            'expires_at' => $order->expires_at,
        ]);
    }

    /**
     * Check QRIS payment status
     */
    public function checkQrisStatus(Request $request)
    {
        $request->validate([
            'qris_id' => 'required|integer|exists:doku_qris_payments,id',
        ]);

        try {
            $qris = \App\Models\DokuQrisPayment::findOrFail($request->qris_id);

            // Check if user owns this QRIS
            if ($qris->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized access',
                ], 403);
            }

            Log::channel('va')->info('Manual QRIS status check requested', [
                'qris_id' => $qris->id,
                'user_id' => auth()->id(),
                'partner_reference_no' => $qris->partner_reference_no,
            ]);

            // If already paid, return immediately
            if ($qris->status === 'paid') {
                return response()->json([
                    'success' => true,
                    'status' => 'paid',
                    'message' => 'Pembayaran sudah berhasil',
                    'data' => [
                        'amount' => $qris->amount,
                        'paid_at' => $qris->paid_at?->format('d M Y H:i'),
                        'status' => $qris->status,
                    ],
                ]);
            }

            // Check if expired
            if ($qris->expired_at && $qris->expired_at->isPast()) {
                $qris->markAsExpired();
                
                return response()->json([
                    'success' => false,
                    'status' => 'expired',
                    'message' => 'QRIS sudah kadaluarsa',
                    'data' => [
                        'expired_at' => $qris->expired_at->format('d M Y H:i'),
                        'status' => 'expired',
                    ],
                ]);
            }

            // Query status from DOKU API
            try {
                $qrisService = new \App\Services\DokuQrisService();
                $statusResult = $qrisService->queryQris($qris);
                
                Log::channel('va')->info('DOKU QRIS status check response', [
                    'qris_id' => $qris->id,
                    'response' => $statusResult,
                ]);

                if ($statusResult['success']) {
                    $responseData = $statusResult['data'];
                    $transactionStatus = $responseData['latestTransactionStatus'] ?? '';
                    
                    // Check if paid (status code 00 = success)
                    if ($transactionStatus === '00') {
                        // Mark as paid
                        $qris->markAsPaid();
                        $qris->update([
                            'approval_code' => $responseData['additionalInfo']['approvalCode'] ?? null,
                            'doku_response' => array_merge($qris->doku_response ?? [], [
                                'status_check' => $responseData,
                                'status_checked_at' => now()->toISOString(),
                            ]),
                        ]);

                        // Activate subscription if needed
                        if ($qris->payment_type === 'subscription') {
                            $this->activateSubscription($qris);
                        }

                        return response()->json([
                            'success' => true,
                            'status' => 'paid',
                            'message' => 'Pembayaran berhasil! Paket Anda akan segera diaktifkan.',
                            'data' => [
                                'amount' => $qris->amount,
                                'status' => 'paid',
                                'transaction_status' => $transactionStatus,
                            ],
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::channel('va')->error('Failed to check QRIS status from DOKU', [
                    'qris_id' => $qris->id,
                    'error' => $e->getMessage(),
                ]);
            }

            // Still pending
            return response()->json([
                'success' => true,
                'status' => 'pending',
                'message' => 'Pembayaran belum diterima. Silakan scan kode QRIS.',
                'data' => [
                    'amount' => $qris->amount,
                    'expired_at' => $qris->expired_at->format('d M Y H:i'),
                    'status' => 'pending',
                ],
            ]);

        } catch (\Exception $e) {
            Log::channel('va')->error('QRIS status check failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengecek status pembayaran: ' . $e->getMessage(),
            ], 500);
        }
    }
}
