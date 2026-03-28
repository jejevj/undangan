# Fix DOKU Test Connection Method

## Issue

Error saat test connection:
```
Call to undefined method Doku\Snap\Snap::getTokenB2B()
```

## Root Cause

Method yang dipanggil salah. Di DOKU library v1.0.17, method yang benar adalah:
- ✅ `getB2BToken()` - Returns `TokenB2BResponseDto` object
- ❌ `getTokenB2B()` - Method ini tidak ada

## Solution

### 1. Update DokuService::testConnection()

**Before:**
```php
$token = $this->snap->getTokenB2B();  // ❌ Wrong method

if (isset($token['accessToken'])) {  // ❌ Wrong, it's object not array
    return ['success' => true, ...];
}
```

**After:**
```php
$tokenResponse = $this->snap->getB2BToken();  // ✅ Correct method

// Check if token is valid (responseCode empty means success)
if (empty($tokenResponse->responseCode) && !empty($tokenResponse->accessToken)) {
    return [
        'success' => true,
        'message' => 'Koneksi ke DOKU API berhasil! Credentials valid.',
        'environment' => $this->config->environment,
        'token_expires_in' => $tokenResponse->expiresIn . ' seconds',
    ];
}

// Token request failed
return [
    'success' => false,
    'message' => 'Gagal mendapatkan token dari DOKU API.',
    'error_code' => $tokenResponse->responseCode ?? 'unknown',
    'error_message' => $tokenResponse->responseMessage ?? 'Unknown error',
];
```

### 2. Fix Constructor Parameters

**Issue:** Constructor Snap class hanya menerima 7 parameter, tapi kita pass 8.

**Before:**
```php
$this->snap = new Snap(
    $config->decrypted_private_key ?? '',
    $config->public_key ?? '',
    $config->doku_public_key ?? '',
    $config->client_id,
    $config->issuer ?? '',
    $config->isProduction(),
    $config->decrypted_secret_key ?? '',
    $config->decrypted_auth_code ?? ''  // ❌ Parameter ke-8 tidak ada
);
```

**After:**
```php
$this->snap = new Snap(
    $config->decrypted_private_key ?? '',  // Optional for SNAP API
    $config->public_key ?? '',              // Optional for SNAP API
    $config->doku_public_key ?? '',         // Optional for SNAP API
    $config->client_id,                     // Required
    $config->issuer ?? '',                  // Optional
    $config->isProduction(),                // Required
    $config->decrypted_secret_key ?? ''     // Required
);
```

## TokenB2BResponseDto Structure

Berdasarkan source code DOKU library:

```php
class TokenB2BResponseDto
{
    public string $responseCode;      // Empty string = success
    public string $responseMessage;   // Error message if failed
    public string $accessToken;       // The B2B token
    public string $tokenType;         // Usually "Bearer"
    public int $expiresIn;           // Token expiry in seconds
    public string $additionalInfo;    // Additional info
}
```

### Success Response:
```php
TokenB2BResponseDto {
    responseCode: "",
    responseMessage: "",
    accessToken: "eyJhbGciOiJSUzI1NiJ9...",
    tokenType: "Bearer",
    expiresIn: 900,
    additionalInfo: ""
}
```

### Error Response:
```php
TokenB2BResponseDto {
    responseCode: "5007300",
    responseMessage: "Invalid credentials",
    accessToken: "",
    tokenType: "",
    expiresIn: 0,
    additionalInfo: ""
}
```

## Test Connection Logic

### Success Criteria:
1. `responseCode` is empty string
2. `accessToken` is not empty
3. `expiresIn` > 0

### Failure Criteria:
1. `responseCode` is not empty (contains error code)
2. `accessToken` is empty
3. Exception thrown

## Updated Files

1. ✅ `undangan/app/Services/DokuService.php`
   - Fixed `testConnection()` method
   - Fixed constructor parameters
   - Added proper error handling

## Testing

### Test dengan Credentials Valid:

**Expected Response:**
```json
{
    "success": true,
    "message": "Koneksi ke DOKU API berhasil! Credentials valid.",
    "environment": "production",
    "token_expires_in": "900 seconds"
}
```

### Test dengan Credentials Invalid:

**Expected Response:**
```json
{
    "success": false,
    "message": "Gagal mendapatkan token dari DOKU API.",
    "error_code": "5007300",
    "error_message": "Invalid credentials"
}
```

### Test dengan Network Error:

**Expected Response:**
```json
{
    "success": false,
    "message": "Error: Connection timeout"
}
```

## Notes

### Auth Code Field
- Auth code tidak digunakan di constructor Snap
- Auth code digunakan saat memanggil method tertentu seperti:
  - `getTokenB2B2C($authCode)`
  - `doPayment($data, $authCode, $ip)`
  - `doRefund($data, $authCode, $ip, $deviceId)`
  - `doBalanceInquiry($data, $authCode, $ip)`

### SNAP API Keys (Optional)
Untuk test connection, SNAP API keys (private_key, public_key, doku_public_key) tidak wajib. Keys ini hanya diperlukan untuk:
- Virtual Account operations
- Direct Debit operations
- Payment operations
- Signature verification

Test connection hanya butuh:
- ✅ Client ID
- ✅ Secret Key
- ✅ Base URL
- ✅ Environment

## Next Steps

1. ✅ Test connection dengan credentials production
2. ⏳ Implement Virtual Account operations
3. ⏳ Implement Payment operations
4. ⏳ Implement Webhook handler

---

**Fixed**: 2026-03-28
**Status**: ✅ Ready for testing
