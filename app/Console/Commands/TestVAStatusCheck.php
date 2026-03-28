<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DokuVirtualAccount;
use App\Models\PaymentGatewayConfig;
use App\Services\DokuService;
use Illuminate\Support\Facades\Log;

class TestVAStatusCheck extends Command
{
    protected $signature = 'test:va-status {va_id}';
    protected $description = 'Test VA status check from DOKU API';

    public function handle()
    {
        $vaId = $this->argument('va_id');
        
        $va = DokuVirtualAccount::find($vaId);
        
        if (!$va) {
            $this->error("VA with ID {$vaId} not found");
            return 1;
        }
        
        $this->info("Testing VA Status Check");
        $this->info("VA ID: {$va->id}");
        $this->info("VA Number: {$va->display_va_number}");
        $this->info("TRX ID: {$va->trx_id}");
        $this->info("Current Status: {$va->status}");
        $this->line("");
        
        try {
            $result = $this->checkStatusFromDoku($va);
            
            $this->info("=== API Response ===");
            $this->line(json_encode($result, JSON_PRETTY_PRINT));
            
            if ($result['success']) {
                $this->info("\n✓ Status check successful!");
                
                $responseData = $result['data'];
                $vaData = $responseData['virtualAccountData'] ?? null;
                
                if ($vaData) {
                    $paymentFlagReason = $vaData['paymentFlagReason']['english'] ?? '';
                    $paidAmount = $vaData['paidAmount']['value'] ?? 0;
                    
                    // Bill amount can be in billAmount or billDetails array
                    $billAmount = 0;
                    if (isset($vaData['billAmount']['value'])) {
                        $billAmount = $vaData['billAmount']['value'];
                    } elseif (isset($vaData['billDetails'][0]['billAmount']['value'])) {
                        $billAmount = $vaData['billDetails'][0]['billAmount']['value'];
                    }
                    
                    $this->info("Payment Flag: {$paymentFlagReason}");
                    $this->info("Paid Amount: {$paidAmount}");
                    $this->info("Bill Amount: {$billAmount}");
                    
                    if (strtolower($paymentFlagReason) === 'success' && $paidAmount > 0 && $paidAmount >= $billAmount) {
                        $this->info("\n✓ Payment is SUCCESSFUL!");
                    } else {
                        $this->warn("\n⚠ Payment is still PENDING (Flag: {$paymentFlagReason})");
                    }
                }
            } else {
                $this->error("\n✗ Status check failed");
            }
            
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
        
        return 0;
    }
    
    protected function checkStatusFromDoku(DokuVirtualAccount $va): array
    {
        try {
            $config = PaymentGatewayConfig::getActive('doku');
            if (!$config) {
                throw new \Exception('DOKU configuration not found');
            }

            $dokuResponse = $va->doku_response;
            if (!$dokuResponse || !is_array($dokuResponse)) {
                throw new \Exception('DOKU response data not found in database');
            }

            $clientId = $config->client_id;
            $isProduction = $config->environment === 'production';
            
            $baseUrl = $isProduction 
                ? 'https://api.doku.com' 
                : 'https://api-sandbox.doku.com';
            
            $endpoint = '/orders/v1.0/transfer-va/status';
            $url = $baseUrl . $endpoint;
            
            $timestamp = now()->format('Y-m-d\TH:i:sP');
            
            $tokenService = new DokuService($config);
            $tokenB2B = $tokenService->getTokenB2B();
            
            $requestBody = [
                'partnerServiceId' => $dokuResponse['virtualAccountData']['partnerServiceId'] ?? trim($va->partner_service_id),
                'customerNo' => $dokuResponse['virtualAccountData']['customerNo'] ?? $va->customer_no,
                'virtualAccountNo' => $dokuResponse['virtualAccountData']['virtualAccountNo'] ?? trim($va->virtual_account_no),
                'trxId' => $dokuResponse['virtualAccountData']['trxId'] ?? $va->trx_id,
            ];
            
            $requestBodyJson = json_encode($requestBody, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            
            // Generate signature using HMAC SHA512 (symmetric signature)
            $bodyHash = hash('sha256', $requestBodyJson);
            $bodyHashHex = strtolower($bodyHash);
            $stringToSign = "POST:{$endpoint}:{$tokenB2B}:{$bodyHashHex}:{$timestamp}";
            
            $secretKey = $config->decrypted_secret_key;
            if (!$secretKey) {
                throw new \Exception('Secret key not found in configuration');
            }
            
            $signature = hash_hmac('sha512', $stringToSign, $secretKey, true);
            $signatureBase64 = base64_encode($signature);
            
            $externalId = 'CHK-' . strtoupper(uniqid());
            
            Log::channel('va')->info('=== Testing VA Status Check ===', [
                'url' => $url,
                'va_id' => $va->id,
                'request_body' => $requestBody,
                'timestamp' => $timestamp,
                'external_id' => $externalId,
            ]);
            
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
}
