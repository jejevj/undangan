# DOKU Payment Gateway - Quick Start Guide

## Overview

Panduan cepat untuk memulai integrasi DOKU Payment Gateway dengan aplikasi undangan digital.

---

## Prerequisites

### 1. DOKU Account
- Daftar di: https://jokul.doku.com/
- Dapatkan Client ID & Secret Key
- Aktifkan payment methods yang diinginkan

### 2. Server Requirements
- PHP 8.1+
- Laravel 10+
- MySQL/PostgreSQL
- HTTPS (production)
- Composer

---

## Quick Implementation (Minimal)

### Step 1: Database Setup (10 menit)

```bash
# Generate migrations
php artisan make:migration create_payment_gateway_configs_table
php artisan make:migration create_payment_methods_table
php artisan make:migration create_payment_transactions_table
php artisan make:migration create_payment_logs_table

# Run migrations
php artisan migrate
```

### Step 2: Create Models (15 menit)

```bash
php artisan make:model PaymentGatewayConfig
php artisan make:model PaymentMethod
php artisan make:model PaymentTransaction
php artisan make:model PaymentLog
```

### Step 3: Create Service (20 menit)

```bash
php artisan make:service DokuPaymentService
```

### Step 4: Create Controllers (20 menit)

```bash
# Admin
php artisan make:controller Admin/PaymentGatewayConfigController --resource
php artisan make:controller Admin/PaymentMethodController --resource
php artisan make:controller Admin/PaymentTransactionController

# User
php artisan make:controller PaymentController
php artisan make:controller PaymentCallbackController
```

### Step 5: Add Routes (10 menit)

```php
// routes/web.php

// Admin routes
Route::prefix('dash')->middleware(['auth', 'role:admin'])->group(function () {
    Route::resource('payment-gateway', PaymentGatewayConfigController::class);
    Route::resource('payment-methods', PaymentMethodController::class);
    Route::get('payment-transactions', [PaymentTransactionController::class, 'index']);
});

// User routes
Route::middleware('auth')->group(function () {
    Route::get('subscription/checkout/{plan}', [PaymentController::class, 'checkout']);
    Route::post('payment/create', [PaymentController::class, 'create']);
    Route::get('payment/{invoice}', [PaymentController::class, 'show']);
    Route::get('payment/{invoice}/status', [PaymentController::class, 'checkStatus']);
});

// Callback (no auth)
Route::post('payment/callback/doku', [PaymentCallbackController::class, 'handle']);
```

### Step 6: Create Views (30 menit)

```bash
# Admin views
resources/views/admin/payment-gateway/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

resources/views/admin/payment-methods/
├── index.blade.php
├── create.blade.php
└── edit.blade.php

# User views
resources/views/payment/
├── checkout.blade.php
├── show.blade.php
└── success.blade.php
```

### Step 7: Seed Data (10 menit)

```bash
php artisan make:seeder PaymentMethodSeeder
php artisan db:seed --class=PaymentMethodSeeder
```

### Step 8: Test (15 menit)

```bash
# Test admin config
http://127.0.0.1:8000/dash/payment-gateway

# Test payment methods
http://127.0.0.1:8000/dash/payment-methods

# Test checkout
http://127.0.0.1:8000/subscription/checkout/1
```

---

## Minimal Configuration

### 1. Payment Gateway Config (Admin)

```
Client ID: [Your DOKU Client ID]
Secret Key: [Your DOKU Secret Key]
Environment: Sandbox
Base URL: https://api-sandbox.doku.com
Status: Active
```

### 2. Payment Methods (Seed)

```php
// Minimal payment methods
[
    ['code' => 'mandiri_va', 'name' => 'Mandiri Virtual Account', 'type' => 'virtual_account'],
    ['code' => 'bca_va', 'name' => 'BCA Virtual Account', 'type' => 'virtual_account'],
    ['code' => 'bni_va', 'name' => 'BNI Virtual Account', 'type' => 'virtual_account'],
]
```

---

## Integration Flow

### User Flow
```
1. User pilih paket subscription
2. User pilih metode pembayaran
3. System generate payment code (VA/QRIS)
4. User bayar via bank/e-wallet
5. DOKU send callback
6. System update status & activate subscription
7. User receive email confirmation
```

### Admin Flow
```
1. Admin configure DOKU credentials
2. Admin enable payment methods
3. Admin monitor transactions
4. Admin handle refunds (if needed)
```

---

## Priority Features

### Must Have (Phase 1)
- [x] Payment Gateway Config (Admin)
- [x] Payment Methods Management
- [x] Virtual Account (Mandiri, BCA, BNI)
- [x] Transaction List
- [x] Callback Handler
- [x] Email Notifications

### Nice to Have (Phase 2)
- [ ] Credit Card
- [ ] QRIS
- [ ] E-Wallet (OVO, DANA, ShopeePay)
- [ ] Retail (Alfamart, Indomaret)
- [ ] Refund Management
- [ ] Payment Analytics

### Future (Phase 3)
- [ ] Recurring Payment
- [ ] Payment Link
- [ ] Invoice Generator
- [ ] Payment Reminder
- [ ] Multi-currency

---

## Testing Strategy

### Sandbox Testing
1. Use DOKU sandbox credentials
2. Test VA generation
3. Simulate payment (DOKU provides test tools)
4. Test callback
5. Verify subscription activation

### Production Testing
1. Small amount test transaction
2. Monitor first 10 transactions closely
3. Check callback reliability
4. Verify email notifications
5. Test refund process

---

## Common Issues & Solutions

### Issue 1: Signature Mismatch
**Solution:** Check timestamp format, ensure UTC+0

### Issue 2: Callback Not Received
**Solution:** 
- Check callback URL is accessible
- Verify HTTPS
- Check firewall/security rules

### Issue 3: Payment Expired
**Solution:** Set appropriate expiration time (60-1440 minutes)

### Issue 4: Duplicate Transaction
**Solution:** Use unique invoice number with timestamp

---

## Security Checklist

- [ ] Secret key encrypted in database
- [ ] HTTPS enabled in production
- [ ] Callback signature verification
- [ ] CSRF protection enabled
- [ ] Rate limiting configured
- [ ] Input validation
- [ ] SQL injection prevention
- [ ] XSS protection

---

## Monitoring

### Key Metrics
- Transaction success rate
- Average payment time
- Failed transaction rate
- Callback response time
- Payment method usage

### Alerts
- Failed callback
- High failure rate
- Unusual transaction amount
- Duplicate transactions
- API errors

---

## Support

### DOKU Support
- Email: support@doku.com
- Phone: +62 21 2953 9600
- Docs: https://jokul.doku.com/docs

### Internal Support
- Check `payment_logs` table
- Review error logs
- Test in sandbox first
- Contact DOKU support if needed

---

## Next Steps

1. ✅ Review plan: `DOKU-PAYMENT-GATEWAY-PLAN.md`
2. ⏳ Create database migrations
3. ⏳ Create models
4. ⏳ Implement admin config
5. ⏳ Implement DOKU service
6. ⏳ Create user payment flow
7. ⏳ Test in sandbox
8. ⏳ Deploy to production

---

## Estimated Time

**Minimal Implementation:** 2-3 hari
- Day 1: Database, Models, Admin Config
- Day 2: DOKU Service, User Flow
- Day 3: Testing, Bug Fixes

**Complete Implementation:** 4-6 minggu
- See `DOKU-PAYMENT-GATEWAY-PLAN.md` for details

---

**Ready to start? Let's build it!** 🚀
