# DOKU Signature Generation - Fixed

## Perubahan Berdasarkan Dokumentasi Resmi DOKU

Setelah mempelajari dokumentasi resmi DOKU di https://jokul.doku.com/docs/docs/technical-references/generate-signature/, saya telah memperbaiki implementasi signature generation.

## Masalah Sebelumnya

1. ❌ Timestamp format salah (menggunakan `now()->format()` yang bisa include timezone lokal)
2. ❌ Signature components menggunakan `implode()` yang menambahkan `\n` di akhir
3. ❌ JSON encoding tidak konsisten

## Perbaikan yang Dilakukan

### 1. Timestamp Format yang Benar

**Sebelum:**
```php
$timestamp = now()->format('Y-m-d\TH:i:s\Z');
```

**Sesudah:**
```php
$timestamp = gmdate('Y-m-d\TH:i:s\Z');
```

**Alasan**: 
- Harus menggunakan UTC timezone (GMT)
- Format: `YYYY-MM-DDTHH:mm:ssZ` (tanpa milidetik)
- Contoh: `2020-08-11T08:45:42Z`

### 2. Signature Components yang Benar

**Sebelum:**
```php
$signatureComponents = implode("\n", [
    'Client-Id:' . $paymentGateway->client_id,
    'Request-Id:' . $requestId,
    'Request-Timestamp:' . $timestamp,
    'Request-Target:' . $endpoint,
    'Digest:' . $digest,
]);
```

**Sesudah:**
```php
$signatureComponents = 
    'Client-Id:' . $paymentGateway->client_id . "\n" .
    'Request-Id:' . $requestId . "\n" .
    'Request-Timestamp:' . $timestamp . "\n" .
    'Request-Target:' . $endpoint . "\n" .
    'Digest:' . $digest;
```

**Alasan**:
- Tidak ada spasi setelah colon (`:`)
- Tidak ada `\n` di akhir string
- Format yang tepat sesuai dokumentasi DOKU

### 3. JSON Encoding yang Konsisten

**Sebelum:**
```php
$testBody = json_encode([
    'order' => [
        'invoice_number' => 'TEST-' . time(),
        'amount' => 10000,
    ]
]);
```

**Sesudah:**
```php
$testBody = json_encode([
    'order' => [
        'invoice_number' => 'TEST-' . time(),
        'amount' => 10000,
    ]
], JSON_UNESCAPED_SLASHES);
```

**Alasan**: Memastikan format JSON konsisten tanpa escaped slashes

## Algoritma Signature DOKU (Sesuai Dokumentasi)

### Step 1: Generate Digest
```php
// SHA256 hash dari request body, lalu base64 encode
$digest = base64_encode(hash('sha256', $requestBody, true));
```

### Step 2: Prepare Signature Components
```
Client-Id:<client_id>
Request-Id:<request_id>
Request-Timestamp:<timestamp>
Request-Target:<endpoint_path>
Digest:<digest>
```

**PENTING**: 
- Tidak ada spasi setelah colon
- Setiap component dipisah dengan `\n`
- Tidak ada `\n` di akhir string

### Step 3: Generate HMAC-SHA256
```php
$hmac = hash_hmac('sha256', $signatureComponents, $secretKey, true);
$signature = 'HMACSHA256=' . base64_encode($hmac);
```

## Contoh Lengkap

### Input
```
Client ID: MCH-0001-10791114622547
Secret Key: your-secret-key
Request ID: cc682442-6c22-493e-8121-b9ef6b3fa728
Timestamp: 2020-08-11T08:45:42Z
Endpoint: /doku-virtual-account/v2/payment-code
Body: {"order":{"invoice_number":"INV-20210124-0001","amount":15000}}
```

### Step 1: Generate Digest
```
Digest: 5WIYK2TJg6iiZ0d5v4IXSR0EkYEkYOezJIma3Ufli5s=
```

### Step 2: Signature Components (Raw String)
```
Client-Id:MCH-0001-10791114622547
Request-Id:cc682442-6c22-493e-8121-b9ef6b3fa728
Request-Timestamp:2020-08-11T08:45:42Z
Request-Target:/doku-virtual-account/v2/payment-code
Digest:5WIYK2TJg6iiZ0d5v4IXSR0EkYEkYOezJIma3Ufli5s=
```

### Step 3: Generate Signature
```
Signature: HMACSHA256=OvIRJs/jH8BIcGsktr4d8nnYtxY6E0Uzdm9d1GVgv5s=
```

## Testing dengan Credentials Valid

Sekarang dengan perbaikan ini, test connection dengan credentials production yang valid seharusnya berhasil.

### Expected Results

#### ✅ Credentials Valid
```json
{
    "success": true,
    "message": "Koneksi ke DOKU API berhasil! Credentials valid.",
    "environment": "production",
    "status_code": 200
}
```

atau jika test data ditolak:

```json
{
    "success": true,
    "message": "Koneksi berhasil! Credentials valid (test request ditolak karena data test).",
    "environment": "production",
    "note": "API merespon dengan benar, credentials terverifikasi."
}
```

#### ❌ Credentials Invalid
```json
{
    "success": false,
    "message": "Credentials tidak valid! Client ID atau Secret Key salah.",
    "status_code": 400,
    "error": "Invalid Header Signature"
}
```

## Referensi

- [DOKU Signature Documentation](https://jokul.doku.com/docs/docs/technical-references/generate-signature/)
- [DOKU Postman Collection](https://github.com/PTNUSASATUINTIARTHA-DOKU/doku-postman-collection)

## Summary

✅ Signature generation sudah diperbaiki sesuai dokumentasi resmi DOKU  
✅ Timestamp menggunakan UTC timezone yang benar  
✅ Signature components format yang tepat  
✅ JSON encoding konsisten  
🚀 Siap untuk test dengan credentials production yang valid

---

**Status**: Signature Fixed ✅  
**Ready for**: Testing dengan DOKU production credentials  
**Next**: Verify test connection berhasil dengan credentials valid
