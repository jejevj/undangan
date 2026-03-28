# DOKU Payment Gateway - Quick Reference Card

## 🚀 Quick Start

### Create Virtual Account
```php
use App\Services\DokuVirtualAccountService;

$vaService = new DokuVirtualAccountService();
$va = $vaService->createVirtualAccount(
    user: auth()->user(),
    paymentType: 'subscription',  // subscription, gift, gallery, music_upload
    amount: 100000,
    referenceId: $pricingPlanId,
    options: [
        'channel' => 'VIRTUAL_ACCOUNT_BANK_CIMB',
        'expired_hours' => 24,
    ]
);
```

### Check VA Status
```php
$status = $vaService->checkStatus($va);
```

### Get Available Banks
```php
$banks = DokuVirtualAccountService::getAvailableChannels();
```

---

## 📋 Payment Types & Reference IDs

| Payment Type | Reference ID | Action Triggered |
|-------------|--------------|------------------|
| `subscription` | `pricing_plan_id` | Create/update UserSubscription |
| `gift` | `invitation_id` | Enable gift_enabled |
| `gallery` | `gallery_order_id` | Update order, increment gallery_limit |
| `music_upload` | `music_id` | Activate music (is_active, is_public) |

---

## 🏦 Available Banks

```php
'VIRTUAL_ACCOUNT_BANK_CIMB'     => 'CIMB Niaga'
'VIRTUAL_ACCOUNT_BANK_MANDIRI'  => 'Mandiri'
'VIRTUAL_ACCOUNT_BANK_BRI'      => 'BRI'
'VIRTUAL_ACCOUNT_BANK_BNI'      => 'BNI'
'VIRTUAL_ACCOUNT_BANK_PERMATA'  => 'Permata'
```

---

## 🔄 VA Status Flow

```
pending → active → paid
              ↓
           expired
              ↓
          cancelled
```

---

## 📊 VA Model Methods

```php
$va->isActive()      // Check if active
$va->isPaid()        // Check if paid
$va->isExpired()     // Check if expired
$va->markAsPaid()    // Mark as paid
$va->markAsExpired() // Mark as expired
$va->markAsCancelled() // Mark as cancelled
```

---

## 🔍 VA Model Scopes

```php
DokuVirtualAccount::active()->get()
DokuVirtualAccount::pending()->get()
DokuVirtualAccount::paid()->get()
DokuVirtualAccount::forUser($userId)->get()
DokuVirtualAccount::forPaymentType('subscription')->get()
```

---

## 🌐 Webhook Endpoint

```
POST /webhook/doku/payment-notification
```

**Headers Required**:
- `X-SIGNATURE`: Base64 encoded signature
- `X-TIMESTAMP`: ISO 8601 timestamp
- `X-CLIENT-KEY`: DOKU client ID
- `Content-Type`: application/json

---

## 📝 Response Codes

### Success (2xxx)
- `2002500` - Webhook processed successfully
- `2007300` - API call successful

### Client Error (4xxx)
- `4017300` - Unauthorized (invalid signature)
- `4007300` - Bad request

### Server Error (5xxx)
- `5002500` - Internal server error
- `5007300` - Server error

---

## 🔐 Configuration

### Get Active Config
```php
$config = PaymentGatewayConfig::getActive('doku');
```

### Check Environment
```php
$config->isSandbox()     // true if sandbox
$config->isProduction()  // true if production
```

### Get Decrypted Keys
```php
$config->decrypted_secret_key
$config->decrypted_private_key
$config->decrypted_auth_code
```

---

## 🧪 Testing

### Test Connection
```php
$dokuService = new DokuService($config);
$result = $dokuService->testConnection();
```

### Create Test VA
```bash
php artisan tinker
>>> $va = (new App\Services\DokuVirtualAccountService())->createVirtualAccount(
...     user: App\Models\User::first(),
...     paymentType: 'subscription',
...     amount: 100000,
...     referenceId: 1
... );
>>> echo $va->virtual_account_no;
```

### Simulate Payment
```bash
curl -X POST http://localhost/webhook/doku/payment-notification \
  -H "Content-Type: application/json" \
  -H "X-SIGNATURE: [signature]" \
  -H "X-TIMESTAMP: 2026-03-28T10:30:00+07:00" \
  -H "X-CLIENT-KEY: [client_id]" \
  -d '{"virtualAccountNo":"[va_number]","trxId":"[trx_id]"}'
```

---

## 📂 Important Files

### Controllers
- `app/Http/Controllers/DokuWebhookController.php`
- `app/Http/Controllers/PaymentGatewayConfigController.php`

### Services
- `app/Services/DokuVirtualAccountService.php`
- `app/Services/DokuService.php`

### Models
- `app/Models/DokuVirtualAccount.php`
- `app/Models/PaymentGatewayConfig.php`

### Routes
- `routes/web.php` (webhook route)

### Config
- `bootstrap/app.php` (CSRF exception)

---

## 🔧 Common Tasks

### Update VA
```php
$vaService->updateVirtualAccount($va, [
    'amount' => 150000,
    'expired_at' => now()->addHours(48),
]);
```

### Cancel VA
```php
$vaService->deleteVirtualAccount($va);
```

### Get VA by Number
```php
$va = DokuVirtualAccount::where('virtual_account_no', $vaNumber)->first();
```

### Get User's Active VAs
```php
$vas = DokuVirtualAccount::forUser($userId)->active()->get();
```

---

## 📊 Database Tables

### payment_gateway_configs
- Configuration untuk payment gateway
- Credentials encrypted

### doku_virtual_accounts
- Virtual account records
- Payment tracking
- Status management

---

## 🐛 Troubleshooting

### Signature Verification Failed
- Check DOKU public key in config
- Verify timestamp not expired
- Check client ID matches

### VA Not Found
- Check virtualAccountNo or trxId
- Verify VA exists in database
- Check VA status

### Payment Not Triggered
- Check payment_type
- Verify reference_id valid
- Review logs for errors

### Webhook Not Received
- Check webhook URL in DOKU Dashboard
- Verify HTTPS enabled
- Check firewall/security settings

---

## 📚 Documentation

- `DOKU-IMPLEMENTATION-COMPLETE.md` - Complete summary
- `DOKU-COMPLETE-PAYMENT-FLOW.md` - End-to-end flow
- `DOKU-WEBHOOK-IMPLEMENTATION.md` - Webhook details
- `DOKU-VIRTUAL-ACCOUNT-IMPLEMENTATION.md` - VA details
- `DOKU-TESTING-CHECKLIST.md` - Testing guide

---

## 🔗 Useful Links

- DOKU Dashboard: https://dashboard.doku.com
- DOKU API Docs: https://developers.doku.com
- PHP Library: https://github.com/PTNUSASATUINTIARTHA-DOKU/doku-php-library

---

## 📞 Support

### DOKU Support
- Email: support@doku.com
- Phone: +62 21 2929 2929

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

---

**Version**: 1.0.0  
**Last Updated**: 28 March 2026
