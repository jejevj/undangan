# DOKU SNAP API - Signature Fix

## 🎯 ROOT CAUSE FOUND!

Ternyata kamu menggunakan **DOKU SNAP API**, bukan Non-SNAP API!

SNAP API menggunakan format signature yang **BERBEDA TOTAL** dari Non-SNAP API.

## ❌ Yang Salah Sebelumnya

### Non-SNAP API Format (Yang Saya Implementasi Sebelumnya)
```
Headers:
- Client-Id
- Request-Id  
- Request-Timestamp
- Signature: HMACSHA256=xxx
- Digest

Signature Components:
Client-Id:xxx
Request-Id:xxx
Request-Timestamp:xxx
Request-Target:xxx
Digest:xxx

Algorithm: HMAC-SHA256
```

## ✅ Yang Benar untuk SNAP API

### SNAP API Format (Yang Baru Diimplementasi)
```
Headers:
- X-CLIENT-KEY (bukan Client-Id)
- X-TIMESTAMP (bukan Request-Timestamp)
- X-SIGNATURE (bukan Signature)

StringToSign Format:
HTTPMethod:EndpointUrl:AccessToken:BodyHash:Timestamp

Example:
POST:/checkout/v1/payment:BRN-xxx:3274fab8dac896837b106a16da2a974e7e65142dcecb4b768ef0294102838977:2024-03-26T16:01:41+07:00

Algorithm: HMAC-SHA512 (bukan SHA256!)
```

## 🔄 Perubahan yang Dilakukan

### 1. Timestamp Format
**Before**: `2026-03-28T10:41:35Z` (UTC)  
**After**: `2026-03-28T16:01:41+07:00` (dengan timezone)

### 2. Body Hash
**Before**: Base64(SHA256(body))  
**After**: Lowercase(HexEncode(SHA256(minify(body))))

### 3. StringToSign
**Before**: Multi-line components dengan newline  
**After**: Single line dengan colon separator

### 4. HMAC Algorithm
**Before**: HMAC-SHA256  
**After**: HMAC-SHA512

### 5. Headers
**Before**:
```
Client-Id: xxx
Request-Id: xxx
Request-Timestamp: xxx
Signature: HMACSHA256=xxx
```

**After**:
```
X-CLIENT-KEY: xxx
X-TIMESTAMP: xxx
X-SIGNATURE: xxx
```

## 📝 New Implementation

### DokuSignatureService::generate()
```php
// Minify JSON
$minifiedBody = json_encode(json_decode($requestBody), JSON_UNESCAPED_SLASHES);

// SHA-256 hash (hex, lowercase)
$bodyHash = strtolower(hash('sha256', $minifiedBody));

// StringToSign
$stringToSign = 'POST:' . $requestTarget . ':' . $clientId . ':' . $bodyHash . ':' . $timestamp;

// HMAC-SHA512
$hmac = hash_hmac('sha512', $stringToSign, $secretKey, true);
$signature = base64_encode($hmac);
```

### HTTP Headers
```php
Http::withHeaders([
    'X-CLIENT-KEY' => $clientId,
    'X-TIMESTAMP' => $timestamp,
    'X-SIGNATURE' => $signature,
    'Content-Type' => 'application/json',
])
```

## 🧪 Test Now

Sekarang test connection lagi dengan credentials yang sama. Dengan format SNAP API yang benar, seharusnya berhasil!

### Expected Result
✅ "Koneksi berhasil! Credentials valid"

atau jika test data ditolak:

✅ "Koneksi berhasil! Credentials valid (test request ditolak karena data test)"

## 📚 Reference

- [DOKU SNAP Symmetric Signature](https://developers.doku.com/get-started-with-doku-api/signature-component/snap/symmetric-signature)
- StringToSign Format: `HTTPMethod:EndpointUrl:AccessToken:Lowercase(HexEncode(SHA256(minify(RequestBody)))):TimeStamp`
- Algorithm: HMAC-SHA512
- Headers: X-CLIENT-KEY, X-TIMESTAMP, X-SIGNATURE

## ✨ Summary

- ❌ **Before**: Menggunakan Non-SNAP API format (salah)
- ✅ **After**: Menggunakan SNAP API format (benar)
- 🔧 **Algorithm**: HMAC-SHA512 (bukan SHA256)
- 📋 **Headers**: X-CLIENT-KEY, X-TIMESTAMP, X-SIGNATURE
- 🚀 **Status**: Ready to test dengan format yang benar!

---

**Next**: Test connection dengan credentials production yang sama  
**Expected**: Success! ✅
