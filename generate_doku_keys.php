<?php
/**
 * DOKU RSA Key Generator
 * 
 * Script untuk generate RSA key pair untuk DOKU SNAP API
 * 
 * Usage:
 *   php generate_doku_keys.php
 * 
 * Output:
 *   - doku_private.key (Private Key - RAHASIA!)
 *   - doku_public.key (Public Key - Upload ke DOKU)
 */

echo "===========================================\n";
echo "  DOKU RSA Key Generator\n";
echo "===========================================\n\n";

// Check if OpenSSL extension is loaded
if (!extension_loaded('openssl')) {
    die("ERROR: OpenSSL extension is not loaded!\n");
}

// Configuration
$config = [
    "private_key_bits" => 2048,
    "private_key_type" => OPENSSL_KEYTYPE_RSA,
];

echo "Generating RSA key pair (2048 bit)...\n";

// Generate private key
$privateKey = openssl_pkey_new($config);

if (!$privateKey) {
    die("ERROR: Failed to generate private key!\n");
}

// Extract private key
$success = openssl_pkey_export($privateKey, $privateKeyPEM);

if (!$success) {
    die("ERROR: Failed to export private key!\n");
}

// Extract public key
$publicKeyDetails = openssl_pkey_get_details($privateKey);
$publicKeyPEM = $publicKeyDetails["key"];

// Save to files
$privateKeyFile = 'doku_private.key';
$publicKeyFile = 'doku_public.key';

file_put_contents($privateKeyFile, $privateKeyPEM);
file_put_contents($publicKeyFile, $publicKeyPEM);

echo "✓ Keys generated successfully!\n\n";

echo "===========================================\n";
echo "  FILES CREATED\n";
echo "===========================================\n";
echo "1. {$privateKeyFile} (PRIVATE - Keep Secret!)\n";
echo "2. {$publicKeyFile} (PUBLIC - Upload to DOKU)\n\n";

echo "===========================================\n";
echo "  PRIVATE KEY (Keep this SECRET!)\n";
echo "===========================================\n";
echo $privateKeyPEM . "\n";

echo "===========================================\n";
echo "  PUBLIC KEY (Upload to DOKU Dashboard)\n";
echo "===========================================\n";
echo $publicKeyPEM . "\n";

echo "===========================================\n";
echo "  NEXT STEPS\n";
echo "===========================================\n";
echo "1. BACKUP private key ke tempat aman\n";
echo "2. LOGIN ke DOKU Jokul Dashboard\n";
echo "3. UPLOAD public key ke DOKU\n";
echo "4. DOWNLOAD DOKU public key\n";
echo "5. INPUT semua keys ke aplikasi\n\n";

echo "===========================================\n";
echo "  SECURITY WARNING\n";
echo "===========================================\n";
echo "⚠️  NEVER share your private key!\n";
echo "⚠️  NEVER commit private key to Git!\n";
echo "⚠️  NEVER upload private key to public server!\n";
echo "⚠️  DELETE this script after use if needed!\n\n";

echo "Keys saved to:\n";
echo "  - " . realpath($privateKeyFile) . "\n";
echo "  - " . realpath($publicKeyFile) . "\n\n";

echo "Done!\n";
