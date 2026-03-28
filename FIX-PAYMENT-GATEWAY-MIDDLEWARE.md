# Fix: Payment Gateway Middleware Error

## Error
```
Call to undefined method App\Http\Controllers\PaymentGatewayConfigController::middleware()
```

## Cause
Controller menggunakan `permission:` middleware yang tidak tersedia di Laravel default.

## Solution ✅

### Changed From:
```php
$this->middleware('permission:payment-gateway.view')->only(['index', 'show']);
$this->middleware('permission:payment-gateway.create')->only(['create', 'store']);
$this->middleware('permission:payment-gateway.edit')->only(['edit', 'update']);
$this->middleware('permission:payment-gateway.delete')->only('destroy');
```

### Changed To:
```php
$this->middleware('can:payment-gateway.view')->only(['index', 'show']);
$this->middleware('can:payment-gateway.create')->only(['create', 'store']);
$this->middleware('can:payment-gateway.edit')->only(['edit', 'update']);
$this->middleware('can:payment-gateway.delete')->only('destroy');
```

## Explanation

Laravel menggunakan `can:` middleware untuk permission checking dengan Spatie Permission package, bukan `permission:`.

## Commands Run

```bash
php artisan optimize:clear
```

## Test

```bash
# Akses halaman
http://127.0.0.1:8000/dash/payment-gateway

# Should work now!
```

## Status: ✅ FIXED

Error sudah diperbaiki dan aplikasi berjalan normal.
