<?php

namespace App\Services;

use Illuminate\Support\Str;

class DokuSignatureService
{
    /**
     * Generate DOKU SNAP signature for API request
     * 
     * @param string $clientId
     * @param string $secretKey
     * @param string $requestTarget Endpoint path (e.g., /checkout/v1/payment)
     * @param string|null $requestBody JSON body for POST requests
     * @return array ['signature' => string, 'requestId' => string, 'timestamp' => string, 'digest' => string|null, 'components' => string]
     */
    public static function generate(string $clientId, string $secretKey, string $requestTarget, ?string $requestBody = null): array
    {
        // Generate Request ID (UUID v4)
        $requestId = Str::uuid()->toString();
        
        // Generate timestamp in ISO 8601 format with timezone
        // Format: YYYY-MM-DDTHH:mm:ss+07:00
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        
        $digest = null;
        $stringToSign = null;
        
        // Generate digest and stringToSign for POST/PUT requests (when body exists)
        if ($requestBody !== null) {
            // Minify JSON (remove whitespace)
            $minifiedBody = json_encode(json_decode($requestBody), JSON_UNESCAPED_SLASHES);
            
            // SHA-256 hash of minified body
            $sha256Hash = hash('sha256', $minifiedBody);
            
            // Lowercase hex-encoded hash
            $bodyHash = strtolower($sha256Hash);
            
            // StringToSign for SNAP API
            // Format: HTTPMethod:EndpointUrl:AccessToken:BodyHash:Timestamp
            $stringToSign = 'POST:' . $requestTarget . ':' . $clientId . ':' . $bodyHash . ':' . $timestamp;
            
            // Generate HMAC-SHA512 signature
            $hmac = hash_hmac('sha512', $stringToSign, $secretKey, true);
            $signature = base64_encode($hmac);
            
            return [
                'signature' => $signature,
                'requestId' => $requestId,
                'timestamp' => $timestamp,
                'bodyHash' => $bodyHash,
                'stringToSign' => $stringToSign,
            ];
        }
        
        // For GET requests (no body)
        $stringToSign = 'GET:' . $requestTarget . ':' . $clientId . '::' . $timestamp;
        
        // Generate HMAC-SHA512 signature
        $hmac = hash_hmac('sha512', $stringToSign, $secretKey, true);
        $signature = base64_encode($hmac);
        
        return [
            'signature' => $signature,
            'requestId' => $requestId,
            'timestamp' => $timestamp,
            'bodyHash' => null,
            'stringToSign' => $stringToSign,
        ];
    }
    
    /**
     * Validate signature from DOKU response
     * 
     * @param string $clientId
     * @param string $secretKey
     * @param string $requestId
     * @param string $responseTimestamp
     * @param string $requestTarget
     * @param string|null $responseBody
     * @param string $receivedSignature
     * @return bool
     */
    public static function validate(
        string $clientId,
        string $secretKey,
        string $requestId,
        string $responseTimestamp,
        string $requestTarget,
        ?string $responseBody,
        string $receivedSignature
    ): bool {
        $digest = null;
        
        if ($responseBody !== null) {
            $digest = base64_encode(hash('sha256', $responseBody, true));
        }
        
        $components = 'Client-Id:' . $clientId . "\n" .
                     'Request-Id:' . $requestId . "\n" .
                     'Response-Timestamp:' . $responseTimestamp . "\n" .
                     'Request-Target:' . $requestTarget;
        
        if ($digest !== null) {
            $components .= "\n" . 'Digest:' . $digest;
        }
        
        $hmac = hash_hmac('sha256', $components, $secretKey, true);
        $expectedSignature = 'HMACSHA256=' . base64_encode($hmac);
        
        return hash_equals($expectedSignature, $receivedSignature);
    }
    
    /**
     * Debug signature generation - returns detailed info
     * 
     * @param string $clientId
     * @param string $secretKey
     * @param string $requestTarget
     * @param string|null $requestBody
     * @return array
     */
    public static function debug(string $clientId, string $secretKey, string $requestTarget, ?string $requestBody = null): array
    {
        $result = self::generate($clientId, $secretKey, $requestTarget, $requestBody);
        
        return [
            'client_id' => $clientId,
            'request_id' => $result['requestId'],
            'timestamp' => $result['timestamp'],
            'request_target' => $requestTarget,
            'request_body' => $requestBody,
            'digest' => $result['digest'],
            'signature_components' => $result['components'],
            'signature_components_lines' => explode("\n", $result['components']),
            'signature' => $result['signature'],
            'secret_key_length' => strlen($secretKey),
            'secret_key_preview' => substr($secretKey, 0, 10) . '...',
        ];
    }
}
