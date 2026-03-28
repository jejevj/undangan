# Debug DOKU Signature Generation

## Tools yang Ditambahkan

### 1. DokuSignatureService
Service class untuk generate dan validate signature DOKU dengan cara yang konsisten.

**Location**: `app/Services/DokuSignatureService.php`

**Methods**:
- `generate()` - Generate signature untuk request
- `validate()` - Validate signature dari response
- `debug()` - Debug mode dengan detail lengkap

### 2. Debug Endpoint
Endpoint khusus untuk melihat detail signature generation (hanya tersedia di debug mode).

**URL**: `/dash/payment-gateway/{id}/debug-signature`

**Requirement**: `APP_DEBUG=true` di `.env`

## Cara Debug Signature

### Step 1: Pastikan Debug Mode Aktif
```env
APP_DEBUG=true
```

### Step 2: Akses Debug Endpoint
```
GET /dash/payment-gateway/1/debug-signature
```

### Step 3: Analisis Output
Output akan menampilkan:
```json
{
    "client_id": "BRN-xxxx-xxxx",
    "request_id": "uuid-v4",
    "timestamp": "2026-03-28T10:30:45Z",
    "request_target": "/checkout/v1/payment",
    "request_body": "{\"order\":{\"invoice_number\":\"TEST-1234\",\"amount\":10000}}",
    "digest": "base64_encoded_sha256",
    "signature_components": "Client-Id:xxx\nRequest-Id:xxx\n...",
    "signature_components_lines": [
        "Client-Id:BRN-xxxx-xxxx",
        "Request-Id:uuid-v4",
        "Request-Timestamp:2026-03-28T10:30:45Z",
        "Request-Target:/checkout/v1/payment",
        "Digest:base64_encoded_sha256"
    ],
    "signature": "HMACSHA256=base64_encoded_hmac",
    "secret_key_length": 32,
    "secret_key_preview": "SK-xxx..."
}
```

### Step 4: Cek Laravel Log
Setiap test connection akan log detail ke `storage/logs/laravel.log`:

```
[2026-03-28 10:30:45] local.INFO: DOKU Test Connection
{
    "client_id": "BRN-xxxx-xxxx",
    "request_id": "uuid",
    "timestamp": "2026-03-28T10:30:45Z",
    "endpoint": "/checkout/v1/payment",
    "digest": "base64_digest",
    "signature": "HMACSHA256=signature",
    "components": "Client-Id:xxx\n..."
}

[2026-03-28 10:30:46] local.INFO: DOKU Response
{
    "status": 400,
    "body": {
        "error": {
            "message": "Invalid Header Signature"
        }
    }
}
```

## Checklist Debugging

### ✅ Hal yang Harus Dicek

1. **Client ID Format**
   - Harus sesuai dengan yang di DOKU Dashboard
   - Biasanya format: `BRN-xxxx-xxxx` atau `MCH-xxxx-xxxx`
   - Tidak ada spasi di awal/akhir

2. **Secret Key Format**
   - Harus sesuai dengan yang di DOKU Dashboard
   - Biasanya format: `SK-xxxx-xxxx-xxxx`
   - Tidak ada spasi di awal/akhir
   - Cek apakah ter-encrypt dengan benar di database

3. **Timestamp Format**
   - Harus UTC timezone
   - Format: `YYYY-MM-DDTHH:mm:ssZ`
   - Contoh: `2026-03-28T10:30:45Z`
   - Tidak ada milidetik

4. **Request Target**
   - Harus sesuai dengan endpoint yang dipanggil
   - Contoh: `/checkout/v1/payment`
   - Harus dimulai dengan `/`

5. **Digest**
   - SHA-256 hash dari request body
   - Di-encode dengan base64
   - Request body harus exact JSON string (tidak ada whitespace tambahan)

6. **Signature Components**
   - Tidak ada spasi setelah colon (`:`)
   - Setiap line dipisah dengan `\n`
   - Tidak ada `\n` di akhir string
   - Urutan harus tepat: Client-Id, Request-Id, Request-Timestamp, Request-Target, Digest

## Common Issues

### Issue 1: Secret Key Ter-encrypt Double
**Symptom**: Signature selalu invalid meskipun credentials benar

**Check**:
```php
// Di database, secret_key harus ter-encrypt
// Tapi saat diambil dari model, harus sudah ter-decrypt otomatis

// Cek di PaymentGatewayConfig model:
protected $casts = [
    'secret_key' => 'encrypted',
];
```

**Solution**: Pastikan model menggunakan cast `encrypted` dengan benar

### Issue 2: Whitespace di Credentials
**Symptom**: Signature invalid

**Check**:
```php
// Pastikan tidak ada whitespace
$clientId = trim($paymentGateway->client_id);
$secretKey = trim($paymentGateway->secret_key);
```

### Issue 3: JSON Body Format
**Symptom**: Digest tidak cocok

**Check**:
```php
// Pastikan JSON encoding konsisten
$body = json_encode($data, JSON_UNESCAPED_SLASHES);

// Jangan ada whitespace tambahan
// SALAH: {"order": {"amount": 10000}}
// BENAR: {"order":{"amount":10000}}
```

### Issue 4: Timezone Issue
**Symptom**: Timestamp tidak valid

**Check**:
```php
// Harus menggunakan gmdate() untuk UTC
$timestamp = gmdate('Y-m-d\TH:i:s\Z');

// JANGAN gunakan:
// now()->format() - bisa include timezone lokal
// date() - bisa include timezone lokal
```

## Manual Verification

Untuk verify signature secara manual, gunakan tools online:

### 1. Generate SHA-256 Hash
- Tool: https://emn178.github.io/online-tools/sha256.html
- Input: Request body JSON
- Output: Hex hash
- Convert to Base64: https://base64.guru/converter/encode/hex

### 2. Generate HMAC-SHA256
- Tool: https://www.freeformatter.com/hmac-generator.html
- Algorithm: SHA-256
- Input: Signature components string
- Secret Key: Your secret key
- Output: Base64

### 3. Compare
Compare hasil manual dengan hasil dari debug endpoint.

## Next Steps

Jika masih error setelah debugging:

1. **Cek Laravel Log** - Lihat detail request dan response
2. **Cek Debug Endpoint** - Bandingkan dengan dokumentasi DOKU
3. **Contact DOKU Support** - Kirim detail signature components untuk verifikasi
4. **Test dengan Postman** - Gunakan DOKU Postman Collection untuk compare

## Dokumentasi Referensi

- [DOKU Signature Documentation](https://jokul.doku.com/docs/docs/technical-references/generate-signature/)
- [DOKU Postman Collection](https://github.com/PTNUSASATUINTIARTHA-DOKU/doku-postman-collection)

---

**Status**: Debug tools ready ✅  
**Next**: Analyze logs dan debug endpoint untuk identify issue  
**Goal**: Find exact difference between our signature and DOKU's expected signature
