<?php

/**
 * DOKU Webhook Signature Test Script
 * 
 * Script ini untuk test signature verification secara manual
 * sebelum test dengan DOKU sandbox.
 * 
 * Usage:
 * php test_webhook_signature.php
 */

// Sample data
$method = 'POST';
$path = 'webhook/doku/payment-notification';
$timestamp = '2026-03-28T10:30:00+07:00';
$body = json_encode([
    'virtualAccountNo' => '123456780000000001',
    'trxId' => 'SUB-ABC12345-1711234567',
    'paidAmount' => [
        'value' => '100000.00',
        'currency' => 'IDR'
    ],
    'paymentFlagStatus' => '00'
]);

// Build string to sign
$stringToSign = "{$method}:{$path}::{$body}:{$timestamp}";

echo "=== DOKU Webhook Signature Test ===\n\n";
echo "1. String to Sign:\n";
echo $stringToSign . "\n\n";

// Load your private key (untuk generate signature)
echo "2. Generate Signature:\n";
echo "Masukkan path ke private key Anda: ";
$privateKeyPath = trim(fgets(STDIN));

if (!file_exists($privateKeyPath)) {
    die("Error: Private key file not found!\n");
}

$privateKey = file_get_contents($privateKeyPath);
$privateKeyResource = openssl_pkey_get_private($privateKey);

if (!$privateKeyResource) {
    die("Error: Invalid private key format!\n");
}

// Sign the string
$signature = '';
openssl_sign($stringToSign, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256);
openssl_free_key($privateKeyResource);

$signatureBase64 = base64_encode($signature);
echo "Signature (Base64): " . $signatureBase64 . "\n\n";

// Verify with public key
echo "3. Verify Signature:\n";
echo "Masukkan path ke public key Anda: ";
$publicKeyPath = trim(fgets(STDIN));

if (!file_exists($publicKeyPath)) {
    die("Error: Public key file not found!\n");
}

$publicKey = file_get_contents($publicKeyPath);
$publicKeyResource = openssl_pkey_get_public($publicKey);

if (!$publicKeyResource) {
    die("Error: Invalid public key format!\n");
}

// Verify signature
$verified = openssl_verify($stringToSign, $signature, $publicKeyResource, OPENSSL_ALGO_SHA256);
openssl_free_key($publicKeyResource);

if ($verified === 1) {
    echo "✅ Signature VALID!\n\n";
} else {
    echo "❌ Signature INVALID!\n\n";
}

// Generate cURL command for testing
echo "4. Test cURL Command:\n";
echo "curl -X POST http://localhost/webhook/doku/payment-notification \\\n";
echo "  -H \"Content-Type: application/json\" \\\n";
echo "  -H \"X-SIGNATURE: {$signatureBase64}\" \\\n";
echo "  -H \"X-TIMESTAMP: {$timestamp}\" \\\n";
echo "  -H \"X-CLIENT-KEY: YOUR_CLIENT_ID\" \\\n";
echo "  -d '{$body}'\n\n";

echo "=== Test Complete ===\n";
