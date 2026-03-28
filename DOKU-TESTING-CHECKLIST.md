# DOKU Payment Gateway - Testing Checklist

## 🧪 Pre-Testing Setup

### 1. Database Migration
```bash
php artisan migrate
```

**Expected**:
- [x] `payment_gateway_configs` table created
- [x] `doku_virtual_accounts` table created

### 2. Verify Configuration
```bash
# Check if DOKU config exists
php artisan tinker
>>> App\Models\PaymentGatewayConfig::where('provider', 'doku')->first()
```

**Expected**: Configuration record dengan semua credentials

### 3. Check Routes
```bash
php artisan route:list | grep -i doku
```

**Expected**:
```
POST   webhook/doku/payment-notification
GET    dash/payment-gateway
POST   dash/payment-gateway
GET    dash/payment-gateway/create
POST   dash/payment-gateway/{paymentGateway}/test-connection
```

---

## ✅ Phase 1: Configuration Testing

### Test 1.1: Access Payment Gateway Page
- [ ] Login sebagai admin
- [ ] Navigate to `/dash/payment-gateway`
- [ ] Page loads without error
- [ ] Can see list of payment gateways

### Test 1.2: Test Connection
- [ ] Click "Test Connection" button
- [ ] Expected: ✅ Success message
- [ ] Check response includes:
  - `success: true`
  - `environment: production`
  - `token_expires_in: 900 seconds`

### Test 1.3: Edit Configuration
- [ ] Click "Edit" button
- [ ] Form loads with existing data
- [ ] All fields populated correctly
- [ ] Can save without errors

---

## ✅ Phase 2: Virtual Account Testing

### Test 2.1: Create VA via Tinker
```bash
php artisan tinker
```

```php
use App\Services\DokuVirtualAccountService;
use App\Models\User;

$user = User::first();
$vaService = new DokuVirtualAccountService();

$va = $vaService->createVirtualAccount(
    user: $user,
    paymentType: 'subscription',
    amount: 100000,
    referenceId: 1,
    options: [
        'channel' => 'VIRTUAL_ACCOUNT_BANK_CIMB',
        'expired_hours' => 24,
    ]
);

echo "VA Number: " . $va->virtual_account_no . "\n";
echo "Amount: " . $va->formatted_amount . "\n";
echo "Status: " . $va->status . "\n";
echo "Expired: " . $va->expired_at . "\n";
```

**Expected**:
- [x] VA created successfully
- [x] Status = `active`
- [x] VA number generated (format: `{partner_service_id}{customer_no}`)
- [x] Expiry time = 24 hours from now

### Test 2.2: Check VA in Database
```bash
php artisan tinker
```

```php
use App\Models\DokuVirtualAccount;

$va = DokuVirtualAccount::latest()->first();
dd($va->toArray());
```

**Expected**:
- [x] Record exists
- [x] All fields populated
- [x] `doku_response` contains API response

### Test 2.3: Check VA Status
```php
$vaService = new DokuVirtualAccountService();
$status = $vaService->checkStatus($va);
dd($status);
```

**Expected**:
- [x] Response from DOKU API
- [x] Contains VA status information

---

## ✅ Phase 3: Webhook Testing

### Test 3.1: Test Signature Verification
```bash
php test_webhook_signature.php
```

**Follow prompts**:
1. Enter private key path: `KEYS/doku_private.key`
2. Enter public key path: `KEYS/doku_public.key`

**Expected**:
- [x] Signature generated
- [x] Signature verified successfully
- [x] cURL command generated

### Test 3.2: Test Webhook Endpoint (Manual)

**Option A: Using cURL**
```bash
# Get signature from test_webhook_signature.php output
curl -X POST http://localhost/undangan/public/webhook/doku/payment-notification \
  -H "Content-Type: application/json" \
  -H "X-SIGNATURE: YOUR_SIGNATURE_HERE" \
  -H "X-TIMESTAMP: 2026-03-28T10:30:00+07:00" \
  -H "X-CLIENT-KEY: BRN-0204-1754870435962" \
  -d '{
    "virtualAccountNo": "YOUR_VA_NUMBER",
    "trxId": "YOUR_TRX_ID",
    "paidAmount": {
      "value": "100000.00",
      "currency": "IDR"
    },
    "paymentFlagStatus": "00"
  }'
```

**Expected Response**:
```json
{
  "responseCode": "2002500",
  "responseMessage": "Success"
}
```

**Option B: Using Postman**
1. Create new POST request
2. URL: `http://localhost/undangan/public/webhook/doku/payment-notification`
3. Headers:
   - `Content-Type: application/json`
   - `X-SIGNATURE: [from test script]`
   - `X-TIMESTAMP: 2026-03-28T10:30:00+07:00`
   - `X-CLIENT-KEY: BRN-0204-1754870435962`
4. Body (raw JSON):
```json
{
  "virtualAccountNo": "YOUR_VA_NUMBER",
  "trxId": "YOUR_TRX_ID",
  "paidAmount": {
    "value": "100000.00",
    "currency": "IDR"
  },
  "paymentFlagStatus": "00"
}
```

### Test 3.3: Check Logs
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

### Test 3.4: Verify Status Update
```bash
php artisan tinker
```

```php
use App\Models\DokuVirtualAccount;

$va = DokuVirtualAccount::where('virtual_account_no', 'YOUR_VA_NUMBER')->first();
echo "Status: " . $va->status . "\n";
echo "Paid At: " . $va->paid_at . "\n";
```

**Expected**:
- [x] Status = `paid`
- [x] `paid_at` timestamp set

---

## ✅ Phase 4: Auto Activation Testing

### Test 4.1: Subscription Activation
```bash
php artisan tinker
```

```php
use App\Models\User;
use App\Models\UserSubscription;
use App\Services\DokuVirtualAccountService;
use App\Models\PricingPlan;

$user = User::first();
$plan = PricingPlan::first();
$vaService = new DokuVirtualAccountService();

// Create VA
$va = $vaService->createVirtualAccount(
    user: $user,
    paymentType: 'subscription',
    amount: $plan->price,
    referenceId: $plan->id
);

// Simulate webhook (mark as paid)
$va->markAsPaid();

// Manually trigger activation
$controller = new App\Http\Controllers\DokuWebhookController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('triggerSubscriptionActivation');
$method->setAccessible(true);
$method->invoke($controller, $va);

// Check subscription
$subscription = UserSubscription::where('user_id', $user->id)->first();
dd($subscription->toArray());
```

**Expected**:
- [x] UserSubscription created/updated
- [x] `pricing_plan_id` = plan ID
- [x] `start_date` = now
- [x] `end_date` = now + plan duration
- [x] `is_active` = true

### Test 4.2: Gift Feature Activation
```php
use App\Models\Invitation;

$invitation = Invitation::first();

// Create VA
$va = $vaService->createVirtualAccount(
    user: $user,
    paymentType: 'gift',
    amount: 50000,
    referenceId: $invitation->id
);

// Mark as paid and trigger
$va->markAsPaid();
$method = $reflection->getMethod('triggerGiftActivation');
$method->setAccessible(true);
$method->invoke($controller, $va);

// Check invitation
$invitation->refresh();
echo "Gift Enabled: " . ($invitation->gift_enabled ? 'Yes' : 'No') . "\n";
```

**Expected**:
- [x] `gift_enabled` = true

### Test 4.3: Gallery Slots Activation
```php
use App\Models\GalleryOrder;

$invitation = Invitation::first();

// Create gallery order
$order = GalleryOrder::create([
    'invitation_id' => $invitation->id,
    'user_id' => $user->id,
    'quantity' => 10,
    'price_per_slot' => 5000,
    'total_price' => 50000,
    'status' => 'pending',
]);

// Create VA
$va = $vaService->createVirtualAccount(
    user: $user,
    paymentType: 'gallery',
    amount: 50000,
    referenceId: $order->id
);

// Mark as paid and trigger
$va->markAsPaid();
$method = $reflection->getMethod('triggerGalleryActivation');
$method->setAccessible(true);
$method->invoke($controller, $va);

// Check order and invitation
$order->refresh();
$invitation->refresh();
echo "Order Status: " . $order->status . "\n";
echo "Gallery Limit: " . $invitation->gallery_limit . "\n";
```

**Expected**:
- [x] Order status = `paid`
- [x] Gallery limit increased by quantity

### Test 4.4: Music Activation
```php
use App\Models\Music;

$music = Music::first();

// Create VA
$va = $vaService->createVirtualAccount(
    user: $user,
    paymentType: 'music_upload',
    amount: 10000,
    referenceId: $music->id
);

// Mark as paid and trigger
$va->markAsPaid();
$method = $reflection->getMethod('triggerMusicActivation');
$method->setAccessible(true);
$method->invoke($controller, $va);

// Check music
$music->refresh();
echo "Is Active: " . ($music->is_active ? 'Yes' : 'No') . "\n";
echo "Is Public: " . ($music->is_public ? 'Yes' : 'No') . "\n";
```

**Expected**:
- [x] `is_active` = true
- [x] `is_public` = true

---

## ✅ Phase 5: Error Handling Testing

### Test 5.1: Invalid Signature
```bash
curl -X POST http://localhost/undangan/public/webhook/doku/payment-notification \
  -H "Content-Type: application/json" \
  -H "X-SIGNATURE: invalid_signature" \
  -H "X-TIMESTAMP: 2026-03-28T10:30:00+07:00" \
  -H "X-CLIENT-KEY: BRN-0204-1754870435962" \
  -d '{"test": "data"}'
```

**Expected**:
- [x] Response: `4017300 - Unauthorized`
- [x] Log: "Invalid signature"

### Test 5.2: Missing Headers
```bash
curl -X POST http://localhost/undangan/public/webhook/doku/payment-notification \
  -H "Content-Type: application/json" \
  -d '{"test": "data"}'
```

**Expected**:
- [x] Response: `4017300 - Unauthorized`
- [x] Log: "Missing signature headers"

### Test 5.3: VA Not Found
```bash
# Use valid signature but non-existent VA
curl -X POST http://localhost/undangan/public/webhook/doku/payment-notification \
  -H "Content-Type: application/json" \
  -H "X-SIGNATURE: [valid signature]" \
  -H "X-TIMESTAMP: 2026-03-28T10:30:00+07:00" \
  -H "X-CLIENT-KEY: BRN-0204-1754870435962" \
  -d '{
    "virtualAccountNo": "999999999999999999",
    "trxId": "INVALID-TRX-ID"
  }'
```

**Expected**:
- [x] Response: `5002500 - Internal Server Error`
- [x] Log: "Virtual Account not found"

### Test 5.4: Already Paid
```bash
# Send webhook twice for same VA
# First request: Success
# Second request: Should skip (already paid)
```

**Expected**:
- [x] First: Status updated, action triggered
- [x] Second: Skipped, log "Payment already processed"

---

## ✅ Phase 6: Integration Testing

### Test 6.1: Full Subscription Flow
1. [ ] User login
2. [ ] Navigate to subscription page
3. [ ] Select pricing plan
4. [ ] Click "Upgrade"
5. [ ] VA created and displayed
6. [ ] Copy VA number
7. [ ] Simulate payment (DOKU sandbox or manual webhook)
8. [ ] Check subscription activated
9. [ ] User can access premium features

### Test 6.2: Full Gift Feature Flow
1. [ ] User login
2. [ ] Navigate to invitation
3. [ ] Click "Buy Gift Feature"
4. [ ] VA created and displayed
5. [ ] Simulate payment
6. [ ] Check gift feature enabled
7. [ ] Can add bank accounts

### Test 6.3: Full Gallery Flow
1. [ ] User login
2. [ ] Navigate to invitation gallery
3. [ ] Click "Buy More Slots"
4. [ ] Enter quantity
5. [ ] VA created and displayed
6. [ ] Simulate payment
7. [ ] Check gallery limit increased
8. [ ] Can upload more photos

### Test 6.4: Full Music Flow
1. [ ] User login
2. [ ] Navigate to music upload
3. [ ] Upload music file
4. [ ] VA created for upload fee
5. [ ] Simulate payment
6. [ ] Check music activated
7. [ ] Music available in library

---

## 📊 Test Results Summary

### Configuration
- [ ] Test connection: PASS
- [ ] Edit configuration: PASS
- [ ] Credentials encrypted: PASS

### Virtual Account
- [ ] Create VA: PASS
- [ ] VA in database: PASS
- [ ] Check status: PASS

### Webhook
- [ ] Signature verification: PASS
- [ ] Status update: PASS
- [ ] Logging: PASS

### Auto Activation
- [ ] Subscription: PASS
- [ ] Gift: PASS
- [ ] Gallery: PASS
- [ ] Music: PASS

### Error Handling
- [ ] Invalid signature: PASS
- [ ] Missing headers: PASS
- [ ] VA not found: PASS
- [ ] Already paid: PASS

### Integration
- [ ] Subscription flow: PASS
- [ ] Gift flow: PASS
- [ ] Gallery flow: PASS
- [ ] Music flow: PASS

---

## 🐛 Issues Found

| # | Issue | Severity | Status | Notes |
|---|-------|----------|--------|-------|
| 1 | | | | |
| 2 | | | | |

---

## ✅ Sign-off

**Tested By**: _______________  
**Date**: _______________  
**Status**: [ ] PASS / [ ] FAIL  
**Notes**: _______________

---

**Next Steps After Testing**:
1. Fix any issues found
2. Deploy to staging
3. Test with DOKU sandbox
4. Configure webhook URL in DOKU Dashboard
5. Deploy to production
6. Monitor logs and payments
