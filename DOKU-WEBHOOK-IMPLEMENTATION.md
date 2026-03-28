# DOKU Webhook Implementation - Complete Guide

## ✅ Implementation Status: COMPLETE

Webhook handler dengan signature verification, auto status update, dan trigger actions telah berhasil diimplementasikan.

---

## 📋 Fitur yang Diimplementasikan

### 1. Webhook Endpoint
- **URL**: `POST /webhook/doku/payment-notification`
- **CSRF Protection**: Disabled untuk webhook route
- **Logging**: Semua request dan response dicatat di log

### 2. Signature Verification
- Verifikasi signature menggunakan DOKU Public Key
- Format signature: `HTTP_METHOD:RELATIVE_PATH:ACCESS_TOKEN:REQUEST_BODY:TIMESTAMP`
- Menggunakan OpenSSL dengan algoritma SHA256
- Validasi Client ID dari header

### 3. Auto Status Update
- Update status Virtual Account dari `active` → `paid`
- Update `paid_at` timestamp
- Simpan payment notification di `doku_response`

### 4. Trigger Actions
Otomatis trigger action berdasarkan `payment_type`:

#### a. Subscription (`payment_type: subscription`)
- Aktivasi atau perpanjang subscription user
- Create/update `UserSubscription`
- Set `start_date` dan `end_date` berdasarkan pricing plan
- `reference_id` = `pricing_plan_id`

#### b. Gift Feature (`payment_type: gift`)
- Aktivasi fitur gift/bank account di undangan
- Update `gift_enabled = true` di invitation
- `reference_id` = `invitation_id`

#### c. Gallery Slots (`payment_type: gallery`)
- Tambah slot gallery ke undangan
- Update status `GalleryOrder` menjadi `paid`
- Increment `gallery_limit` di invitation
- `reference_id` = `gallery_order_id`

#### d. Music Upload (`payment_type: music_upload`)
- Aktivasi musik yang diupload user
- Update `is_active = true` dan `is_public = true`
- `reference_id` = `music_id`

---

## 📁 File yang Dibuat/Dimodifikasi

### 1. Controller
**File**: `app/Http/Controllers/DokuWebhookController.php`

**Methods**:
- `handlePaymentNotification()` - Main webhook endpoint
- `verifySignature()` - Verify DOKU signature
- `processPayment()` - Update VA status
- `triggerPaymentAction()` - Route to specific action
- `triggerSubscriptionActivation()` - Activate subscription
- `triggerGiftActivation()` - Activate gift feature
- `triggerGalleryActivation()` - Add gallery slots
- `triggerMusicActivation()` - Activate music

### 2. Routes
**File**: `routes/web.php`

```php
use App\Http\Controllers\DokuWebhookController;

Route::post('/webhook/doku/payment-notification', [DokuWebhookController::class, 'handlePaymentNotification'])
    ->name('webhook.doku.payment-notification');
```

### 3. Middleware Configuration
**File**: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->validateCsrfTokens(except: [
        'webhook/doku/*',
    ]);
})
```

**File**: `app/Http/Middleware/VerifyCsrfToken.php` (created)

```php
protected $except = [
    'webhook/doku/*',
];
```

---

## 🔐 Signature Verification Flow

### 1. Extract Headers
```php
$signature = $request->header('X-SIGNATURE');
$timestamp = $request->header('X-TIMESTAMP');
$clientId = $request->header('X-CLIENT-KEY');
```

### 2. Build String to Sign
```php
$method = 'POST';
$path = 'webhook/doku/payment-notification';
$body = $request->getContent();
$stringToSign = "{$method}:{$path}::{$body}:{$timestamp}";
```

### 3. Verify with DOKU Public Key
```php
$dokuPublicKey = PaymentGatewayConfig::getActive('doku')->doku_public_key;
$signatureDecoded = base64_decode($signature);
$verified = openssl_verify($stringToSign, $signatureDecoded, $publicKeyResource, OPENSSL_ALGO_SHA256);
```

---

## 📊 Webhook Request Format

### Headers
```
X-SIGNATURE: base64_encoded_signature
X-TIMESTAMP: 2026-03-28T10:30:00+07:00
X-CLIENT-KEY: BRN-0204-1754870435962
Content-Type: application/json
```

### Body Example
```json
{
  "partnerServiceId": "12345678",
  "customerNo": "0000000001",
  "virtualAccountNo": "123456780000000001",
  "virtualAccountName": "John Doe",
  "trxId": "SUB-ABC12345-1711234567",
  "paymentRequestId": "REQ-20260328-001",
  "paidAmount": {
    "value": "100000.00",
    "currency": "IDR"
  },
  "virtualAccountTrxType": "C",
  "flagAdvise": "N",
  "paymentFlagStatus": "00",
  "billDetails": [],
  "freeTexts": [],
  "additionalInfo": {
    "channel": "VIRTUAL_ACCOUNT_BANK_CIMB"
  }
}
```

---

## 🔄 Payment Processing Flow

### 1. Receive Webhook
```
DOKU → POST /webhook/doku/payment-notification
```

### 2. Verify Signature
```
✓ Extract headers (X-SIGNATURE, X-TIMESTAMP, X-CLIENT-KEY)
✓ Build string to sign
✓ Verify with DOKU public key
✓ Validate client ID
```

### 3. Find Virtual Account
```
✓ Find by virtualAccountNo or trxId
✓ Check if already paid (skip if yes)
```

### 4. Update Status
```
✓ Mark VA as paid
✓ Set paid_at timestamp
✓ Save payment notification
```

### 5. Trigger Action
```
✓ Route based on payment_type
✓ Execute specific action
✓ Log result
```

### 6. Return Response
```
✓ Success: 2002500
✓ Unauthorized: 4017300
✓ Server Error: 5002500
```

---

## 🧪 Testing Webhook

### 1. Setup Webhook URL di DOKU Dashboard
```
URL: https://yourdomain.com/webhook/doku/payment-notification
Method: POST
```

### 2. Test dengan cURL
```bash
curl -X POST https://yourdomain.com/webhook/doku/payment-notification \
  -H "Content-Type: application/json" \
  -H "X-SIGNATURE: base64_signature" \
  -H "X-TIMESTAMP: 2026-03-28T10:30:00+07:00" \
  -H "X-CLIENT-KEY: BRN-0204-1754870435962" \
  -d '{
    "virtualAccountNo": "123456780000000001",
    "trxId": "SUB-ABC12345-1711234567",
    "paidAmount": {
      "value": "100000.00",
      "currency": "IDR"
    }
  }'
```

### 3. Check Logs
```bash
tail -f storage/logs/laravel.log
```

**Expected Log Entries**:
```
[2026-03-28 10:30:00] local.INFO: DOKU Webhook Received
[2026-03-28 10:30:00] local.INFO: DOKU Signature Verification
[2026-03-28 10:30:00] local.INFO: DOKU Webhook: Signature verified successfully
[2026-03-28 10:30:00] local.INFO: DOKU Webhook: Payment processed successfully
[2026-03-28 10:30:00] local.INFO: DOKU Webhook: Subscription activated
```

---

## 📝 Response Codes

### Success Responses (2xxx)
- `2002500` - Success (webhook processed)
- `2007300` - Successful (token/API call)

### Client Error Responses (4xxx)
- `4017300` - Unauthorized (invalid signature)
- `4007300` - Bad Request

### Server Error Responses (5xxx)
- `5002500` - Internal Server Error
- `5007300` - Server Error

---

## 🔍 Troubleshooting

### 1. Signature Verification Failed
**Problem**: `4017300 - Unauthorized. Invalid signature`

**Solutions**:
- Pastikan DOKU Public Key benar di database
- Check format public key (harus ada header/footer)
- Verify timestamp tidak expired (max 5 menit)
- Check client ID match dengan config

### 2. Virtual Account Not Found
**Problem**: VA tidak ditemukan di database

**Solutions**:
- Check `virtualAccountNo` atau `trxId` di request
- Verify VA sudah dibuat sebelumnya
- Check status VA (harus `active`)

### 3. Payment Already Processed
**Problem**: Payment sudah diproses sebelumnya

**Solutions**:
- Check status VA (jika `paid`, skip)
- Ini normal behavior (idempotent)
- DOKU bisa kirim webhook multiple times

### 4. Trigger Action Failed
**Problem**: Action tidak ter-trigger

**Solutions**:
- Check `payment_type` di VA
- Verify `reference_id` valid
- Check related model exists (PricingPlan, Invitation, etc.)
- Review logs untuk error detail

---

## 🚀 Next Steps

### 1. Configure Webhook URL di DOKU Dashboard
- Login ke DOKU Dashboard
- Go to Settings → Webhook
- Set URL: `https://yourdomain.com/webhook/doku/payment-notification`
- Save configuration

### 2. Test Payment Flow
- Create Virtual Account
- Simulate payment di DOKU sandbox
- Check webhook received
- Verify status updated
- Confirm action triggered

### 3. Monitor Production
- Setup log monitoring
- Create alerts for failed webhooks
- Monitor payment success rate
- Track trigger action success

---

## 📚 Related Documentation

- `DOKU-VIRTUAL-ACCOUNT-IMPLEMENTATION.md` - VA creation guide
- `DOKU-FINAL-SETUP-GUIDE.md` - Initial setup guide
- `CARA-KONFIGURASI-DOKU-SNAP.md` - SNAP API configuration

---

## ✅ Implementation Checklist

- [x] Create DokuWebhookController
- [x] Implement handlePaymentNotification()
- [x] Implement verifySignature()
- [x] Implement processPayment()
- [x] Implement triggerSubscriptionActivation()
- [x] Implement triggerGiftActivation()
- [x] Implement triggerGalleryActivation()
- [x] Implement triggerMusicActivation()
- [x] Add webhook route
- [x] Disable CSRF for webhook
- [x] Add comprehensive logging
- [x] Add error handling
- [x] Create documentation

---

**Status**: ✅ READY FOR TESTING
**Date**: 28 March 2026
**Version**: 1.0.0
