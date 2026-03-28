<?php

namespace App\Services;

use Doku\Snap\Snap;
use App\Models\PaymentGatewayConfig;

class DokuService
{
    protected $snap;
    protected $config;
    
    public function __construct(PaymentGatewayConfig $config)
    {
        $this->config = $config;
        
        // Initialize DOKU Snap with configuration
        // Based on README: new Snap(privateKey, publicKey, dokuPublicKey, clientId, issuer, isProduction, secretKey, authCode)
        // Note: authCode is optional parameter (8th parameter)
        $this->snap = new Snap(
            $config->decrypted_private_key ?? '',  // privateKey - Required for signature
            $config->public_key ?? '',              // publicKey - Required
            $config->doku_public_key ?? '',         // dokuPublicKey - Required
            $config->client_id,                     // clientId - Required
            $config->issuer ?? '',                  // issuer - Optional
            $config->isProduction(),                // isProduction - Required (boolean)
            $config->decrypted_secret_key ?? '',    // secretKey - Required
            $config->decrypted_auth_code ?? ''      // authCode - Optional (8th parameter)
        );
    }
    
    /**
     * Get Snap instance
     */
    public function getSnap()
    {
        return $this->snap;
    }
    
    /**
     * Test connection to DOKU API
     */
    public function testConnection()
    {
        try {
            // Simple test: validate token B2B
            // This will test if credentials are valid
            $tokenResponse = $this->snap->getB2BToken();
            
            // DOKU Success Response Codes start with "2" (2007300 = Successful)
            // Error Response Codes start with "4" or "5" (4017300 = Unauthorized, 5007300 = Server Error)
            $responseCode = $tokenResponse->responseCode ?? '';
            
            // Check if successful (code starts with "2" or empty, and has accessToken)
            if ((empty($responseCode) || str_starts_with($responseCode, '2')) && !empty($tokenResponse->accessToken)) {
                return [
                    'success' => true,
                    'message' => 'Koneksi ke DOKU API berhasil! Credentials valid.',
                    'environment' => $this->config->environment,
                    'token_expires_in' => $tokenResponse->expiresIn . ' seconds',
                    'response_code' => $responseCode,
                ];
            }
            
            // Token request failed
            return [
                'success' => false,
                'message' => 'Gagal mendapatkan token dari DOKU API.',
                'error_code' => $responseCode ?: 'unknown',
                'error_message' => $tokenResponse->responseMessage ?? 'Unknown error',
            ];
            
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * Create Virtual Account
     */
    public function createVirtualAccount($data)
    {
        return $this->snap->createVa($data);
    }
    
    /**
     * Update Virtual Account
     */
    public function updateVirtualAccount($data)
    {
        return $this->snap->updateVa($data);
    }
    
    /**
     * Delete Virtual Account
     */
    public function deleteVirtualAccount($data)
    {
        return $this->snap->deletePaymentCode($data);
    }
    
    /**
     * Check Virtual Account Status
     */
    public function checkVirtualAccountStatus($data)
    {
        return $this->snap->checkStatusVa($data);
    }
    
    /**
     * Do Payment
     */
    public function doPayment($data, $authCode, $ipAddress)
    {
        return $this->snap->doPayment($data, $authCode, $ipAddress);
    }
    
    /**
     * Do Payment Jump App
     */
    public function doPaymentJumpApp($data, $deviceId, $ipAddress)
    {
        return $this->snap->doPaymentJumpApp($data, $deviceId, $ipAddress);
    }
    
    /**
     * Check Transaction Status
     */
    public function checkStatus($data)
    {
        return $this->snap->doCheckStatus($data);
    }
    
    /**
     * Do Refund
     */
    public function doRefund($data, $authCode, $ipAddress, $deviceId)
    {
        return $this->snap->doRefund($data, $authCode, $ipAddress, $deviceId);
    }
    
    /**
     * Balance Inquiry
     */
    public function balanceInquiry($data, $authCode, $ipAddress)
    {
        return $this->snap->doBalanceInquiry($data, $authCode, $ipAddress);
    }
    
    /**
     * Account Binding
     */
    public function accountBinding($data, $ipAddress, $deviceId)
    {
        return $this->snap->doAccountBinding($data, $ipAddress, $deviceId);
    }
    
    /**
     * Account Unbinding
     */
    public function accountUnbinding($data, $ipAddress)
    {
        return $this->snap->doAccountUnbinding($data, $ipAddress);
    }
    
    /**
     * Card Registration
     */
    public function cardRegistration($data)
    {
        return $this->snap->doCardRegistration($data);
    }
    
    /**
     * Card Unbinding
     */
    public function cardUnbinding($data)
    {
        return $this->snap->doCardUnbinding($data);
    }
    
    /**
     * Get Token B2B2C
     */
    public function getTokenB2B2C($authCode)
    {
        return $this->snap->getTokenB2B2C($authCode);
    }
    
    /**
     * Validate Token B2B
     */
    public function validateTokenB2B($authorization)
    {
        return $this->snap->validateTokenB2B($authorization);
    }
}
