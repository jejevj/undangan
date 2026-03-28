# DOKU Virtual Account - Implementation Complete

## Status: ✅ SELESAI

Implementasi Virtual Account untuk payment system menggunakan DOKU SNAP API.

## Database Structure

### Table: `doku_virtual_accounts`

```sql
- id (bigint)
- user_id (foreign key to users)
- partner_service_id (string) - From DOKU config
- customer_no (string) - User ID padded
- virtual_account_no (string, unique) - Full VA number
- virtual_account_name (string) - User name
- virtual_account_email (string)
- virtual_account_phone (string)
- trx_id (string, unique) - Invoice number
- amount (decimal) - Payment amount
- currency (string) - Default IDR
- payment_type (enum) - subscription, gift, gallery, music_upload
- reference_id (bigint) - ID of related order
- channel (string) - Bank channel
- trx_type (enum) - C (Closed) or O (Open Amount)
- reusable (boolean)
- min_amount, max_amount (decimal)
- expired_at (timestamp)
- status (enum) - pending, active, paid, expired, cancelled
- paid_at (timestamp)
- doku_response (json)
- doku_reference_no (string)
- timestamps
```

## Models

### DokuVirtualAccount Model

**Location**: `app/Models/DokuVirtualAccount.php`

**Key Methods**:
- `isActive()` - Check if VA is active
- `isPaid()` - Check if VA is paid
- `isExpired()` - Check if VA is expired
- `markAsPaid()` - Mark VA as paid
- `markAsExpired()` - Mark VA as expired
- `markAsCancelled()` - Cancel VA
- `getFormattedAmountAttribute()` - Get formatted amount (Rp xxx)
- `getBankNameAttribute()` - Get bank name from channel

**Scopes**:
- `active()` - Get active VAs
- `pending()` - Get pending VAs
- `paid()` - Get paid VAs
- `forUser($userId)` - Get VAs for specific user
- `forPaymentType($type)` - Get VAs for specific payment type

**Relationships**:
- `user()` - BelongsTo User

## Services

### DokuVirtualAccountService

**Location**: `app/Services/DokuVirtualAccountService.php`

#### Main Methods:

##### 1. Create Virtual Account
```php
public function createVirtualAccount(
    User $user,
    string $paymentType,  // subscription, gift, gallery, music_upload
    float $amount,
    ?int $referenceId = null,
    array $options = []
): DokuVirtualAccount
```

**Options**:
- `partner_service_id` - Custom partner service ID
- `channel` - Bank channel (default: VIRTUAL_ACCOUNT_BANK_CIMB)
- `expired_hours` - Expiry in hours (default: 24)
- `trx_type` - C or O (default: C)
- `reusable` - Boolean (default: false)
- `min_amount` - For open amount
- `max_amount` - For open amount

**Example**:
```php
$vaService = new DokuVirtualAccountService();

$va = $vaService->createVirtualAccount(
    user: $user,
    paymentType: 'subscription',
    amount: 100000,
    referenceId: $subscription->id,
    options: [
        'channel' => 'VIRTUAL_ACCOUNT_BANK_CIMB',
        'expired_hours' => 48,
    ]
);
```

##### 2. Check Status
```php
public function checkStatus(DokuVirtualAccount $va): array
```

##### 3. Update Virtual Account
```php
public function updateVirtualAccount(DokuVirtualAccount $va, array $data): array
```

##### 4. Delete/Cancel Virtual Account
```php
public function deleteVirtualAccount(DokuVirtualAccount $va): array
```

##### 5. Get Available Channels
```php
public static function getAvailableChannels(): array
```

Returns:
```php
[
    'VIRTUAL_ACCOUNT_BANK_CIMB' => 'CIMB Niaga',
    'VIRTUAL_ACCOUNT_BANK_MANDIRI' => 'Mandiri',
    'VIRTUAL_ACCOUNT_BANK_BRI' => 'BRI',
    'VIRTUAL_ACCOUNT_BANK_BNI' => 'BNI',
    'VIRTUAL_ACCOUNT_BANK_PERMATA' => 'Permata',
]
```

## Usage Examples

### Example 1: Create VA for Subscription Payment

```php
use App\Services\DokuVirtualAccountService;

// In your subscription controller
public function createPayment(Request $request)
{
    $user = auth()->user();
    $pricingPlan = PricingPlan::find($request->pricing_plan_id);
    
    $vaService = new DokuVirtualAccountService();
    
    try {
        $va = $vaService->createVirtualAccount(
            user: $user,
            paymentType: 'subscription',
            amount: $pricingPlan->price,
            referenceId: null, // Will be set after subscription created
            options: [
                'channel' => $request->bank_channel ?? 'VIRTUAL_ACCOUNT_BANK_CIMB',
                'expired_hours' => 24,
            ]
        );
        
        return response()->json([
            'success' => true,
            'va_number' => $va->virtual_account_no,
            'amount' => $va->formatted_amount,
            'bank' => $va->bank_name,
            'expired_at' => $va->expired_at->format('d M Y H:i'),
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
```

### Example 2: Check VA Status

```php
public function checkPaymentStatus($vaId)
{
    $va = DokuVirtualAccount::findOrFail($vaId);
    $vaService = new DokuVirtualAccountService();
    
    try {
        $status = $vaService->checkStatus($va);
        
        return response()->json([
            'success' => true,
            'status' => $va->status,
            'paid' => $va->isPaid(),
            'doku_status' => $status,
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
```

### Example 3: Cancel VA

```php
public function cancelPayment($vaId)
{
    $va = DokuVirtualAccount::findOrFail($vaId);
    
    // Check if user owns this VA
    if ($va->user_id !== auth()->id()) {
        abort(403);
    }
    
    // Check if can be cancelled
    if ($va->isPaid()) {
        return response()->json([
            'success' => false,
            'message' => 'Cannot cancel paid VA',
        ], 400);
    }
    
    $vaService = new DokuVirtualAccountService();
    
    try {
        $response = $vaService->deleteVirtualAccount($va);
        
        return response()->json([
            'success' => true,
            'message' => 'VA cancelled successfully',
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}
```

## Payment Flow

### 1. User Selects Payment Method
```
User → Select Pricing Plan → Choose Bank → Create VA
```

### 2. VA Created
```
System → Create VA in Database → Call DOKU API → Return VA Number
```

### 3. User Makes Payment
```
User → Transfer to VA Number → Bank → DOKU → Webhook to Our System
```

### 4. Payment Confirmed
```
Webhook → Verify Signature → Update VA Status → Activate Subscription
```

## Integration Points

### 1. Subscription Payment
- Create VA when user selects pricing plan
- Update subscription status when payment confirmed

### 2. Gift Payment
- Create VA when user buys gift feature
- Activate gift feature when payment confirmed

### 3. Gallery Payment
- Create VA when user buys additional gallery slots
- Add gallery slots when payment confirmed

### 4. Music Upload Payment
- Create VA when user uploads custom music
- Activate music when payment confirmed

## Next Steps

### Phase 3.1: Webhook Handler (PRIORITY)
- [ ] Create webhook endpoint
- [ ] Verify DOKU signature
- [ ] Update VA status
- [ ] Trigger payment confirmation actions

### Phase 3.2: Payment UI
- [ ] Create payment selection page
- [ ] Show VA details page
- [ ] Payment status check page
- [ ] Payment history page

### Phase 3.3: Integration with Existing Features
- [ ] Integrate with subscription system
- [ ] Integrate with gift system
- [ ] Integrate with gallery system
- [ ] Integrate with music upload system

### Phase 3.4: Admin Panel
- [ ] VA management page
- [ ] Payment monitoring
- [ ] Manual payment confirmation
- [ ] Refund handling

## Testing

### Test Create VA
```php
// In tinker or test
$user = User::first();
$vaService = new App\Services\DokuVirtualAccountService();

$va = $vaService->createVirtualAccount(
    user: $user,
    paymentType: 'subscription',
    amount: 100000,
    referenceId: null,
    options: ['expired_hours' => 24]
);

dd($va);
```

### Test Check Status
```php
$va = App\Models\DokuVirtualAccount::first();
$vaService = new App\Services\DokuVirtualAccountService();

$status = $vaService->checkStatus($va);
dd($status);
```

## Error Handling

All methods throw exceptions on error. Wrap in try-catch:

```php
try {
    $va = $vaService->createVirtualAccount(...);
} catch (\Exception $e) {
    // Handle error
    Log::error('VA Creation Failed', [
        'user_id' => $user->id,
        'error' => $e->getMessage(),
    ]);
    
    // Return error response
    return response()->json([
        'success' => false,
        'message' => 'Failed to create payment. Please try again.',
    ], 500);
}
```

## Security

1. **VA Number Uniqueness**: Enforced by database unique constraint
2. **Transaction ID Uniqueness**: Enforced by database unique constraint
3. **User Ownership**: Always check `$va->user_id === auth()->id()`
4. **Status Validation**: Check VA status before operations
5. **Webhook Signature**: Verify DOKU signature (Phase 3.1)

## Files Created

1. ✅ `database/migrations/2026_03_28_150412_create_doku_virtual_accounts_table.php`
2. ✅ `app/Models/DokuVirtualAccount.php`
3. ✅ `app/Services/DokuVirtualAccountService.php`
4. ✅ `DOKU-VIRTUAL-ACCOUNT-IMPLEMENTATION.md` (this file)

---

**Completed**: 2026-03-28
**Status**: ✅ Ready for webhook implementation
**Next**: Implement webhook handler for payment notifications
