# DOKU Complete Payment Flow - End to End Guide

## 🎯 Overview

Panduan lengkap untuk menggunakan sistem pembayaran DOKU dari awal sampai akhir, termasuk Virtual Account creation, payment notification, dan auto activation.

---

## 📋 Prerequisites

✅ DOKU configuration sudah setup (client_id, secret_key, keys, dll)  
✅ Test connection berhasil  
✅ Webhook URL sudah dikonfigurasi di DOKU Dashboard  

---

## 🔄 Complete Payment Flow

### STEP 1: User Memilih Paket/Fitur

**Contoh**: User ingin upgrade ke Premium Plan

```php
// User klik "Upgrade to Premium" di dashboard
Route: /dash/subscription/{plan}/checkout
```

---

### STEP 2: Create Virtual Account

**Controller**: `SubscriptionController@checkout` (atau controller lain)

```php
use App\Services\DokuVirtualAccountService;

public function checkout(PricingPlan $plan)
{
    $user = auth()->user();
    
    // Create Virtual Account
    $vaService = new DokuVirtualAccountService();
    
    try {
        $va = $vaService->createVirtualAccount(
            user: $user,
            paymentType: 'subscription',
            amount: $plan->price,
            referenceId: $plan->id,  // pricing_plan_id
            options: [
                'channel' => 'VIRTUAL_ACCOUNT_BANK_CIMB',  // atau bank lain
                'expired_hours' => 24,  // VA expired dalam 24 jam
            ]
        );
        
        // Redirect ke halaman payment instruction
        return view('subscription.payment', [
            'va' => $va,
            'plan' => $plan,
        ]);
        
    } catch (\Exception $e) {
        return back()->with('error', 'Gagal membuat Virtual Account: ' . $e->getMessage());
    }
}
```

**Result**:
- VA record created di database dengan status `active`
- VA created di DOKU API
- User dapat nomor VA untuk transfer

---

### STEP 3: Show Payment Instructions

**View**: `resources/views/subscription/payment.blade.php`

```blade
<div class="payment-instructions">
    <h3>Pembayaran {{ $plan->name }}</h3>
    
    <div class="va-info">
        <p><strong>Bank:</strong> {{ $va->bank_name }}</p>
        <p><strong>Nomor Virtual Account:</strong></p>
        <h2 class="va-number">{{ $va->virtual_account_no }}</h2>
        <p><strong>Jumlah:</strong> {{ $va->formatted_amount }}</p>
        <p><strong>Berlaku sampai:</strong> {{ $va->expired_at->format('d M Y H:i') }}</p>
    </div>
    
    <div class="payment-steps">
        <h4>Cara Pembayaran:</h4>
        <ol>
            <li>Buka aplikasi mobile banking atau ATM</li>
            <li>Pilih menu Transfer</li>
            <li>Pilih Bank {{ $va->bank_name }}</li>
            <li>Masukkan nomor VA: <strong>{{ $va->virtual_account_no }}</strong></li>
            <li>Masukkan jumlah: <strong>{{ $va->formatted_amount }}</strong></li>
            <li>Konfirmasi pembayaran</li>
        </ol>
    </div>
    
    <p class="text-muted">
        Pembayaran akan diverifikasi otomatis. 
        Paket akan aktif dalam 1-5 menit setelah pembayaran berhasil.
    </p>
</div>
```

---

### STEP 4: User Melakukan Transfer

User transfer ke nomor VA menggunakan:
- Mobile Banking
- Internet Banking
- ATM
- Teller Bank

**Amount**: Harus exact sesuai `$va->amount`

---

### STEP 5: DOKU Sends Webhook

Setelah payment berhasil, DOKU otomatis kirim webhook ke aplikasi:

```
POST https://yourdomain.com/webhook/doku/payment-notification

Headers:
  X-SIGNATURE: base64_encoded_signature
  X-TIMESTAMP: 2026-03-28T10:30:00+07:00
  X-CLIENT-KEY: BRN-0204-1754870435962
  Content-Type: application/json

Body:
{
  "virtualAccountNo": "123456780000000001",
  "trxId": "SUB-ABC12345-1711234567",
  "paidAmount": {
    "value": "100000.00",
    "currency": "IDR"
  },
  "paymentFlagStatus": "00"
}
```

---

### STEP 6: Webhook Processing (Automatic)

**Controller**: `DokuWebhookController@handlePaymentNotification`

**Flow**:
1. ✅ Verify signature
2. ✅ Find Virtual Account by `virtualAccountNo` or `trxId`
3. ✅ Check if already paid (skip if yes)
4. ✅ Update VA status to `paid`
5. ✅ Set `paid_at` timestamp
6. ✅ Trigger action based on `payment_type`

---

### STEP 7: Auto Activation (Automatic)

Berdasarkan `payment_type`, sistem otomatis trigger action:

#### A. Subscription Payment
```php
// triggerSubscriptionActivation()
UserSubscription::updateOrCreate(
    ['user_id' => $user->id],
    [
        'pricing_plan_id' => $pricingPlan->id,
        'start_date' => now(),
        'end_date' => now()->addDays($pricingPlan->duration_days),
        'is_active' => true,
    ]
);
```

**Result**: User subscription aktif, dapat akses fitur premium

#### B. Gift Feature Payment
```php
// triggerGiftActivation()
$invitation->update(['gift_enabled' => true]);
```

**Result**: Fitur gift/bank account aktif di undangan

#### C. Gallery Slots Payment
```php
// triggerGalleryActivation()
$galleryOrder->update(['status' => 'paid', 'paid_at' => now()]);
$invitation->increment('gallery_limit', $galleryOrder->quantity);
```

**Result**: Slot gallery bertambah di undangan

#### D. Music Upload Payment
```php
// triggerMusicActivation()
$music->update(['is_active' => true, 'is_public' => true]);
```

**Result**: Musik aktif dan bisa digunakan

---

### STEP 8: User Notification (Optional)

Setelah payment berhasil, kirim notifikasi ke user:

```php
// Di DokuWebhookController setelah trigger action
use Illuminate\Support\Facades\Mail;

Mail::to($user->email)->send(new PaymentSuccessEmail($va, $user));
```

---

## 💡 Implementation Examples

### Example 1: Subscription Payment

```php
// SubscriptionController.php
public function checkout(PricingPlan $plan)
{
    $vaService = new DokuVirtualAccountService();
    
    $va = $vaService->createVirtualAccount(
        user: auth()->user(),
        paymentType: 'subscription',
        amount: $plan->price,
        referenceId: $plan->id,
        options: [
            'channel' => 'VIRTUAL_ACCOUNT_BANK_MANDIRI',
            'expired_hours' => 24,
        ]
    );
    
    return view('subscription.payment', compact('va', 'plan'));
}
```

### Example 2: Gift Feature Payment

```php
// GiftController.php
public function buyFeature(Invitation $invitation)
{
    $vaService = new DokuVirtualAccountService();
    
    $giftPrice = GeneralConfig::get('gift_feature_price', 50000);
    
    $va = $vaService->createVirtualAccount(
        user: auth()->user(),
        paymentType: 'gift',
        amount: $giftPrice,
        referenceId: $invitation->id,
        options: [
            'channel' => 'VIRTUAL_ACCOUNT_BANK_BRI',
            'expired_hours' => 48,
        ]
    );
    
    return view('gift.payment', compact('va', 'invitation'));
}
```

### Example 3: Gallery Slots Payment

```php
// GalleryController.php
public function buySlots(Request $request, Invitation $invitation)
{
    $quantity = $request->input('quantity', 10);
    $pricePerSlot = GeneralConfig::get('gallery_slot_price', 5000);
    $totalPrice = $quantity * $pricePerSlot;
    
    // Create gallery order
    $order = GalleryOrder::create([
        'invitation_id' => $invitation->id,
        'user_id' => auth()->id(),
        'quantity' => $quantity,
        'price_per_slot' => $pricePerSlot,
        'total_price' => $totalPrice,
        'status' => 'pending',
    ]);
    
    // Create VA
    $vaService = new DokuVirtualAccountService();
    $va = $vaService->createVirtualAccount(
        user: auth()->user(),
        paymentType: 'gallery',
        amount: $totalPrice,
        referenceId: $order->id,
        options: [
            'channel' => 'VIRTUAL_ACCOUNT_BANK_BNI',
            'expired_hours' => 24,
        ]
    );
    
    return view('gallery.payment', compact('va', 'order'));
}
```

### Example 4: Music Upload Payment

```php
// MusicController.php
public function uploadCheckout(MusicUploadOrder $order)
{
    $vaService = new DokuVirtualAccountService();
    
    $va = $vaService->createVirtualAccount(
        user: auth()->user(),
        paymentType: 'music_upload',
        amount: $order->upload_fee,
        referenceId: $order->music_id,
        options: [
            'channel' => 'VIRTUAL_ACCOUNT_BANK_PERMATA',
            'expired_hours' => 24,
        ]
    );
    
    return view('music.payment', compact('va', 'order'));
}
```

---

## 🔍 Check Payment Status

### Manual Check (Optional)

Jika ingin manual check status VA:

```php
use App\Services\DokuVirtualAccountService;

$vaService = new DokuVirtualAccountService();
$status = $vaService->checkStatus($va);

// Response dari DOKU
[
    'responseCode' => '2002500',
    'responseMessage' => 'Success',
    'virtualAccountData' => [
        'paymentFlagStatus' => '00',  // 00 = Paid
        'paidAmount' => [
            'value' => '100000.00',
            'currency' => 'IDR'
        ]
    ]
]
```

---

## 📊 Available Banks

```php
DokuVirtualAccountService::getAvailableChannels()

// Returns:
[
    'VIRTUAL_ACCOUNT_BANK_CIMB' => 'CIMB Niaga',
    'VIRTUAL_ACCOUNT_BANK_MANDIRI' => 'Mandiri',
    'VIRTUAL_ACCOUNT_BANK_BRI' => 'BRI',
    'VIRTUAL_ACCOUNT_BANK_BNI' => 'BNI',
    'VIRTUAL_ACCOUNT_BANK_PERMATA' => 'Permata',
]
```

---

## 🔐 Security Features

### 1. Signature Verification
- Semua webhook request diverifikasi signature-nya
- Menggunakan DOKU Public Key
- Invalid signature = rejected (401)

### 2. Idempotency
- Payment yang sudah diproses tidak akan diproses lagi
- Check `isPaid()` sebelum update

### 3. Transaction Logging
- Semua webhook request dicatat di log
- Include headers, body, dan result
- Mudah untuk debugging

### 4. Database Transaction
- Payment processing menggunakan DB transaction
- Rollback jika ada error
- Data consistency terjaga

---

## 📝 Database Schema

### doku_virtual_accounts
```sql
- id
- user_id (FK to users)
- partner_service_id
- customer_no
- virtual_account_no (unique)
- virtual_account_name
- virtual_account_email
- virtual_account_phone
- trx_id (unique)
- amount
- currency
- payment_type (subscription, gift, gallery, music_upload)
- reference_id (pricing_plan_id, invitation_id, gallery_order_id, music_id)
- channel (bank)
- trx_type (C = Closed, O = Open)
- reusable (boolean)
- min_amount
- max_amount
- expired_at
- status (pending, active, paid, expired, cancelled)
- paid_at
- doku_response (json)
- doku_reference_no
- created_at
- updated_at
```

---

## 🧪 Testing Checklist

### 1. Create VA
- [ ] VA created successfully
- [ ] VA number generated correctly
- [ ] Status = `active`
- [ ] Expiry time correct

### 2. Payment Simulation
- [ ] Simulate payment di DOKU sandbox
- [ ] Webhook received
- [ ] Signature verified
- [ ] Status updated to `paid`

### 3. Auto Activation
- [ ] Subscription activated
- [ ] Gift feature enabled
- [ ] Gallery slots added
- [ ] Music activated

### 4. Error Handling
- [ ] Invalid signature rejected
- [ ] VA not found handled
- [ ] Already paid handled
- [ ] Trigger action error logged

---

## 🚀 Production Deployment

### 1. Configure Webhook URL
```
DOKU Dashboard → Settings → Webhook
URL: https://yourdomain.com/webhook/doku/payment-notification
```

### 2. Environment Variables
```env
APP_URL=https://yourdomain.com
DOKU_ENVIRONMENT=production
```

### 3. SSL Certificate
- Webhook URL harus HTTPS
- Valid SSL certificate required

### 4. Monitoring
- Setup log monitoring
- Create alerts for failed webhooks
- Monitor payment success rate

---

## 📚 Related Files

### Controllers
- `app/Http/Controllers/DokuWebhookController.php`
- `app/Http/Controllers/SubscriptionController.php`
- `app/Http/Controllers/GiftController.php`
- `app/Http/Controllers/GalleryController.php`
- `app/Http/Controllers/MusicController.php`

### Services
- `app/Services/DokuVirtualAccountService.php`
- `app/Services/DokuService.php`

### Models
- `app/Models/DokuVirtualAccount.php`
- `app/Models/PaymentGatewayConfig.php`
- `app/Models/UserSubscription.php`
- `app/Models/GalleryOrder.php`

### Routes
- `routes/web.php` (webhook route)

### Config
- `bootstrap/app.php` (CSRF exception)

---

## ✅ Summary

**Complete Flow**:
1. User pilih paket/fitur → Create VA
2. User transfer ke VA → DOKU terima payment
3. DOKU kirim webhook → Verify signature
4. Update VA status → Trigger action
5. Feature activated → User dapat akses

**Fully Automated**: Tidak perlu manual approval atau activation!

---

**Status**: ✅ PRODUCTION READY  
**Date**: 28 March 2026  
**Version**: 1.0.0
