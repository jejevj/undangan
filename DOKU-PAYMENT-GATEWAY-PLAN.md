# DOKU Payment Gateway Integration - Plan & Requirements

## Overview

Integrasi DOKU Payment Gateway untuk sistem pembayaran subscription/paket pricing di aplikasi undangan digital.

---

## Analisis DOKU API

### Payment Methods Supported
1. **Credit Card** - Payment page & refund
2. **Virtual Account:**
   - Mandiri VA
   - BCA VA
   - Bank Syariah Indonesia (BSI) VA
   - CIMB VA
   - Permata VA
   - BRI VA
   - BNI VA
3. **E-Wallet:**
   - OVO
   - ShopeePay
   - LinkAja
   - DANA
4. **QRIS** - QR Code payment
5. **Retail:**
   - Alfamart
   - Indomaret

### API Requirements
**Authentication:**
- Client ID
- Secret Key
- Request ID (UUID v4)
- Request Timestamp (ISO8601)
- Signature (HMACSHA256)

**Endpoints:**
- Base URL: Sandbox/Production
- Payment Code Generation
- Payment Code Update
- Payment Status Check
- Refund/Cancellation
- Webhook/Callback

---

## Database Schema

### 1. Table: `payment_gateway_configs`
Menyimpan konfigurasi DOKU API

```sql
CREATE TABLE payment_gateway_configs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    provider VARCHAR(50) NOT NULL DEFAULT 'doku',
    environment ENUM('sandbox', 'production') NOT NULL DEFAULT 'sandbox',
    client_id VARCHAR(255) NOT NULL,
    secret_key TEXT NOT NULL,
    base_url VARCHAR(255) NOT NULL,
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 2. Table: `payment_methods`
Daftar metode pembayaran yang tersedia

```sql
CREATE TABLE payment_methods (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    type ENUM('credit_card', 'virtual_account', 'ewallet', 'qris', 'retail') NOT NULL,
    provider VARCHAR(50) NOT NULL,
    icon VARCHAR(255) NULL,
    description TEXT NULL,
    is_active BOOLEAN DEFAULT true,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

### 3. Table: `payment_transactions`
Transaksi pembayaran

```sql
CREATE TABLE payment_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    subscription_id BIGINT UNSIGNED NULL,
    invoice_number VARCHAR(100) NOT NULL UNIQUE,
    payment_method_id BIGINT UNSIGNED NOT NULL,
    
    -- Order Info
    amount DECIMAL(15,2) NOT NULL,
    admin_fee DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',
    
    -- Payment Info
    payment_code VARCHAR(100) NULL,
    payment_url TEXT NULL,
    qr_code_url TEXT NULL,
    expired_at TIMESTAMP NULL,
    
    -- Status
    status ENUM('pending', 'paid', 'expired', 'cancelled', 'refunded') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    
    -- DOKU Response
    request_id VARCHAR(100) NULL,
    response_data JSON NULL,
    
    -- Callback
    callback_data JSON NULL,
    callback_received_at TIMESTAMP NULL,
    
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subscription_id) REFERENCES user_subscriptions(id) ON DELETE SET NULL,
    FOREIGN KEY (payment_method_id) REFERENCES payment_methods(id),
    INDEX idx_invoice (invoice_number),
    INDEX idx_status (status),
    INDEX idx_user (user_id)
);
```

### 4. Table: `payment_logs`
Log semua request/response ke DOKU API

```sql
CREATE TABLE payment_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    transaction_id BIGINT UNSIGNED NULL,
    type ENUM('request', 'response', 'callback', 'error') NOT NULL,
    endpoint VARCHAR(255) NOT NULL,
    method VARCHAR(10) NOT NULL,
    headers JSON NULL,
    request_body JSON NULL,
    response_body JSON NULL,
    http_status INT NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP NULL,
    
    FOREIGN KEY (transaction_id) REFERENCES payment_transactions(id) ON DELETE CASCADE,
    INDEX idx_transaction (transaction_id),
    INDEX idx_type (type)
);
```

---

## Models

### 1. PaymentGatewayConfig
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentGatewayConfig extends Model
{
    protected $fillable = [
        'provider',
        'environment',
        'client_id',
        'secret_key',
        'base_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'secret_key',
    ];

    public function getDecryptedSecretKeyAttribute()
    {
        return decrypt($this->secret_key);
    }

    public function setSecretKeyAttribute($value)
    {
        $this->attributes['secret_key'] = encrypt($value);
    }
}
```

### 2. PaymentMethod
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'code',
        'name',
        'type',
        'provider',
        'icon',
        'description',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function transactions()
    {
        return $this->hasMany(PaymentTransaction::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('display_order');
    }
}
```

### 3. PaymentTransaction
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'subscription_id',
        'invoice_number',
        'payment_method_id',
        'amount',
        'admin_fee',
        'total_amount',
        'currency',
        'payment_code',
        'payment_url',
        'qr_code_url',
        'expired_at',
        'status',
        'paid_at',
        'request_id',
        'response_data',
        'callback_data',
        'callback_received_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'expired_at' => 'datetime',
        'paid_at' => 'datetime',
        'callback_received_at' => 'datetime',
        'response_data' => 'array',
        'callback_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subscription()
    {
        return $this->belongsTo(UserSubscription::class, 'subscription_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function logs()
    {
        return $this->hasMany(PaymentLog::class, 'transaction_id');
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isExpired()
    {
        return $this->status === 'expired' || 
               ($this->expired_at && $this->expired_at->isPast());
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
```

### 4. PaymentLog
```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentLog extends Model
{
    protected $fillable = [
        'transaction_id',
        'type',
        'endpoint',
        'method',
        'headers',
        'request_body',
        'response_body',
        'http_status',
        'error_message',
    ];

    protected $casts = [
        'headers' => 'array',
        'request_body' => 'array',
        'response_body' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(PaymentTransaction::class, 'transaction_id');
    }
}
```

---

## Services

### DokuPaymentService
```php
<?php

namespace App\Services;

use App\Models\PaymentGatewayConfig;
use App\Models\PaymentTransaction;
use App\Models\PaymentLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;

class DokuPaymentService
{
    protected $config;
    protected $baseUrl;
    protected $clientId;
    protected $secretKey;

    public function __construct()
    {
        $this->config = PaymentGatewayConfig::where('provider', 'doku')
            ->where('is_active', true)
            ->first();

        if ($this->config) {
            $this->baseUrl = $this->config->base_url;
            $this->clientId = $this->config->client_id;
            $this->secretKey = $this->config->getDecryptedSecretKeyAttribute();
        }
    }

    public function generateSignature($requestBody, $requestTarget)
    {
        $requestId = Str::uuid()->toString();
        $timestamp = now()->toIso8601String();

        // Generate digest
        $digest = base64_encode(hash('sha256', $requestBody, true));

        // Generate signature components
        $signatureComponents = "Client-Id:{$this->clientId}\n" .
                              "Request-Id:{$requestId}\n" .
                              "Request-Timestamp:{$timestamp}\n" .
                              "Request-Target:{$requestTarget}\n" .
                              "Digest:{$digest}";

        // Generate HMAC SHA256 signature
        $signature = base64_encode(hash_hmac('sha256', $signatureComponents, $this->secretKey, true));

        return [
            'request_id' => $requestId,
            'timestamp' => $timestamp,
            'signature' => "HMACSHA256={$signature}",
            'digest' => $digest,
        ];
    }

    public function createVirtualAccount($transaction, $paymentMethodCode)
    {
        // Implementation for VA creation
    }

    public function createCreditCardPayment($transaction)
    {
        // Implementation for CC payment
    }

    public function createQRISPayment($transaction)
    {
        // Implementation for QRIS
    }

    public function checkPaymentStatus($transaction)
    {
        // Implementation for status check
    }

    public function handleCallback($request)
    {
        // Implementation for webhook/callback
    }
}
```

---

## Controllers

### 1. PaymentGatewayConfigController (Admin)
- CRUD konfigurasi DOKU
- Test connection
- Switch environment (sandbox/production)

### 2. PaymentMethodController (Admin)
- CRUD metode pembayaran
- Enable/disable payment methods
- Reorder display

### 3. PaymentController (User)
- Pilih metode pembayaran
- Generate payment code
- Check payment status
- Payment history

### 4. PaymentCallbackController
- Handle DOKU webhook/callback
- Update transaction status
- Activate subscription

---

## Views (Admin Dashboard)

### 1. Payment Gateway Config
- `/dash/payment-gateway/config`
- Form input: Client ID, Secret Key, Environment
- Test connection button
- Status indicator

### 2. Payment Methods
- `/dash/payment-methods`
- List semua metode pembayaran
- Enable/disable toggle
- Icon upload
- Drag & drop reorder

### 3. Payment Transactions
- `/dash/payment-transactions`
- List semua transaksi
- Filter by status, date, user
- Export to Excel
- Detail transaction modal

### 4. Payment Logs
- `/dash/payment-logs`
- View request/response logs
- Filter by transaction, type
- Debug tool

---

## Views (User)

### 1. Checkout Page
- `/subscription/checkout/{plan}`
- Pilih metode pembayaran
- Summary order
- Terms & conditions

### 2. Payment Page
- `/payment/{invoice}`
- Payment instructions
- Payment code/VA number
- QR code (if QRIS)
- Countdown timer
- Check status button

### 3. Payment Success
- `/payment/success/{invoice}`
- Success message
- Receipt
- Download invoice
- Back to dashboard

### 4. Payment History
- `/dash/payments`
- List transaksi user
- Status badge
- Download invoice

---

## Permissions

```php
// Payment Gateway Management
'payment-gateway.view'
'payment-gateway.create'
'payment-gateway.edit'
'payment-gateway.delete'

// Payment Methods
'payment-methods.view'
'payment-methods.create'
'payment-methods.edit'
'payment-methods.delete'

// Payment Transactions
'payment-transactions.view'
'payment-transactions.export'
'payment-transactions.refund'

// Payment Logs
'payment-logs.view'
```

---

## Menu Structure (Admin)

```
Pembayaran
├── Konfigurasi Gateway
├── Metode Pembayaran
├── Transaksi
└── Log Pembayaran
```

---

## Implementation Steps

### Phase 1: Database & Models (Week 1)
1. Create migrations
2. Create models
3. Create seeders
4. Test relationships

### Phase 2: Admin Configuration (Week 1-2)
1. Payment Gateway Config CRUD
2. Payment Methods CRUD
3. Permissions & menu
4. Test connection feature

### Phase 3: DOKU Service Integration (Week 2-3)
1. DokuPaymentService class
2. Signature generation
3. API calls (VA, CC, QRIS, etc)
4. Error handling
5. Logging

### Phase 4: User Payment Flow (Week 3-4)
1. Checkout page
2. Payment method selection
3. Payment code generation
4. Payment instructions page
5. Status checking

### Phase 5: Webhook & Callback (Week 4)
1. Callback controller
2. Signature verification
3. Transaction status update
4. Subscription activation
5. Email notifications

### Phase 6: Testing & Documentation (Week 5)
1. Unit tests
2. Integration tests
3. User documentation
4. Admin documentation
5. API documentation

---

## Security Considerations

1. **Encrypt Secret Key** - Store encrypted in database
2. **Signature Verification** - Verify all callbacks
3. **HTTPS Only** - Force HTTPS in production
4. **Rate Limiting** - Prevent abuse
5. **IP Whitelist** - DOKU callback IPs only
6. **Logging** - Log all API calls
7. **Validation** - Validate all inputs
8. **CSRF Protection** - Laravel CSRF tokens

---

## Testing Checklist

### Sandbox Testing
- [ ] VA Mandiri generation
- [ ] VA BCA generation
- [ ] VA BSI generation
- [ ] Credit Card payment
- [ ] QRIS payment
- [ ] E-Wallet payment
- [ ] Callback handling
- [ ] Status checking
- [ ] Expiration handling
- [ ] Refund process

### Production Checklist
- [ ] Environment switched to production
- [ ] Production credentials configured
- [ ] HTTPS enabled
- [ ] Callback URL configured
- [ ] Email notifications working
- [ ] Error monitoring setup
- [ ] Backup strategy
- [ ] Load testing

---

## Next Actions

1. Review & approve this plan
2. Create database migrations
3. Create models
4. Setup admin configuration pages
5. Implement DOKU service
6. Test in sandbox
7. Deploy to production

---

## Estimated Timeline

- **Phase 1-2:** 1-2 weeks (Database, Models, Admin Config)
- **Phase 3:** 1-2 weeks (DOKU Integration)
- **Phase 4:** 1 week (User Flow)
- **Phase 5:** 3-5 days (Webhook)
- **Phase 6:** 3-5 days (Testing)

**Total:** 4-6 weeks for complete implementation

---

## Budget Estimate

### Development
- Backend Development: 80-100 hours
- Frontend Development: 40-60 hours
- Testing & QA: 20-30 hours
- Documentation: 10-15 hours

**Total:** 150-205 hours

### DOKU Fees
- Transaction fee: ~2-3% per transaction
- Monthly fee: Check with DOKU
- Setup fee: Check with DOKU

---

## Support & Maintenance

### Monthly Tasks
- Monitor transaction success rate
- Check error logs
- Update payment methods
- Review failed transactions
- Customer support

### Quarterly Tasks
- Security audit
- Performance optimization
- Feature updates
- Documentation updates

---

## Documentation Links

- DOKU API Docs: https://jokul.doku.com/docs
- Postman Collection: `doku-postman-collection.json`
- Laravel Payment Package: Consider using existing packages

---

**Status:** Planning Phase
**Last Updated:** 2026-03-28
**Next Review:** After approval
