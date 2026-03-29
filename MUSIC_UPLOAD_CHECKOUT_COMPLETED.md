# Music Upload Slot Purchase System - COMPLETED ✅

## Overview
Implemented a complete checkout system for purchasing music upload slots at Rp 10.000 per slot. Users with Free plan (max_music_uploads = 0) or users who have reached their plan's music upload limit can purchase additional slots.

## Features Implemented

### 1. Music Upload Order System
- **Model**: `MusicUploadOrder`
  - Tracks order number, user, quantity, amount, status
  - Supports VA and QRIS payment methods
  - Auto-generates order numbers: `MUSIC-YYYYMMDD-XXXXXX`

### 2. Checkout Flow
- **Select Quantity** (`/dash/music/upload/buy`)
  - Choose 1-10 slots at Rp 10.000 per slot
  - Shows current plan and upload limits
  
- **Checkout Page** (`/dash/music/upload/checkout`)
  - Tab-based payment selection (VA & QRIS)
  - Consistent design with subscription and gallery checkout
  - Auto-disables inactive payment tabs when one is active
  - Shows order summary and payment details

### 3. Payment Integration
- **Virtual Account**
  - Supports: CIMB, Mandiri, BRI, BNI, Permata
  - Auto-generates VA number via DOKU
  - Auto-check status every 30 seconds
  
- **QRIS**
  - Generates QR code for scanning
  - Works with all e-wallets and mobile banking
  - Auto-check status every 30 seconds

### 4. Upload Logic with Slot Tracking
- **Free Slots** (from plan)
  - Basic/Pro plans have max_music_uploads limit
  - Tracks free uploads separately with `is_paid_upload = false`
  
- **Paid Slots** (purchased)
  - Tracks paid uploads with `is_paid_upload = true`
  - Calculates remaining paid slots: `purchased - used`
  
- **Upload Priority**
  1. Use free slots first (if available)
  2. Use paid slots when free slots exhausted
  3. Redirect to buy page if no slots available

### 5. Webhook Integration
- DOKU webhook activates music upload slots automatically
- Updates order status to 'paid'
- Logs activation in Laravel log

## Database Schema

### music_upload_orders
```sql
- id (bigint, PK)
- order_number (varchar) - MUSIC-YYYYMMDD-XXXXXX
- user_id (bigint, FK)
- qty (int) - Number of slots purchased
- amount (decimal) - Total amount
- price_per_slot (decimal) - Fixed at 10000
- admin_fee (decimal) - Default 0
- status (enum) - pending, paid, expired, cancelled
- payment_method (varchar) - VA channel or 'qris'
- payment_channel_id (bigint, FK, nullable)
- va_number (varchar, nullable)
- payment_url (varchar, nullable)
- qr_string (text, nullable)
- qr_url (varchar, nullable)
- paid_at (timestamp, nullable)
- expired_at (timestamp, nullable)
- created_at, updated_at
```

### music (updated)
```sql
- is_paid_upload (boolean) - True if upload used paid slot
```

## Routes

```php
// Music Upload Checkout
Route::get('music/upload/buy', [MusicUploadCheckoutController::class, 'selectQuantity'])
    ->name('music.upload.buy');
Route::get('music/upload/checkout', [MusicUploadCheckoutController::class, 'checkout'])
    ->name('music.upload.checkout');
Route::post('music-upload-orders/{order}/create-va', [MusicUploadCheckoutController::class, 'createVA'])
    ->name('music.upload.create-va');
Route::post('music-upload-orders/{order}/create-qris', [MusicUploadCheckoutController::class, 'createQris'])
    ->name('music.upload.create-qris');
Route::get('music-upload-orders/{order}/check-status', [MusicUploadCheckoutController::class, 'checkStatus'])
    ->name('music.upload.check-status');
```

## Files Created/Modified

### Created
1. `database/migrations/2026_03_29_180732_create_music_upload_orders_table.php`
2. `database/migrations/2026_03_29_181536_add_is_paid_upload_to_music_table.php`
3. `app/Models/MusicUploadOrder.php`
4. `app/Http/Controllers/MusicUploadCheckoutController.php`
5. `resources/views/music/select-quantity.blade.php`
6. `resources/views/music/checkout.blade.php`

### Modified
1. `app/Http/Controllers/MusicController.php`
   - Updated `uploadForm()` to check slot availability
   - Updated `userUpload()` to track paid vs free uploads
2. `app/Http/Controllers/Webhook/DokuWebhookController.php`
   - Implemented `activateMusicUpload()` method
3. `app/Models/Music.php`
   - Added `is_paid_upload` to fillable
4. `routes/web.php`
   - Added music upload checkout routes

## User Flow

### For Free Plan Users
1. User tries to upload music
2. System detects max_music_uploads = 0
3. Redirects to `/dash/music/upload/buy`
4. User selects quantity (1-10 slots)
5. Proceeds to checkout
6. Chooses payment method (VA or QRIS)
7. Completes payment
8. Webhook activates slots
9. User can now upload music

### For Basic/Pro Users Who Reached Limit
1. User has uploaded max_music_uploads songs (free slots)
2. Tries to upload another song
3. System checks: no free slots, no paid slots
4. Redirects to buy page
5. Purchases paid slots
6. Can upload using paid slots

### Slot Calculation Logic
```php
// Free slots
$freeUploadsUsed = Music::where('uploaded_by', $userId)
    ->where('is_paid_upload', false)
    ->count();
$remainingFreeSlots = $plan->max_music_uploads - $freeUploadsUsed;

// Paid slots
$paidSlots = MusicUploadOrder::where('user_id', $userId)
    ->where('status', 'paid')
    ->sum('qty');
$usedPaidSlots = Music::where('uploaded_by', $userId)
    ->where('is_paid_upload', true)
    ->count();
$remainingPaidSlots = $paidSlots - $usedPaidSlots;

// Total available
$totalAvailable = $remainingFreeSlots + $remainingPaidSlots;
```

## Testing Checklist

- [x] Migration runs successfully
- [x] Select quantity page displays correctly
- [x] Checkout page shows order summary
- [x] VA creation works
- [x] QRIS creation works
- [x] Tab switching works correctly
- [x] Payment status check works
- [x] Webhook activates slots
- [x] Upload form checks slot availability
- [x] Upload tracks paid vs free slots
- [x] SweetAlert notifications work (no plain alerts)

## Configuration

### Price
- Fixed at Rp 10.000 per slot
- Defined in `MusicUploadCheckoutController::selectQuantity()`

### Quantity Limits
- Minimum: 1 slot
- Maximum: 10 slots per order
- No limit on total slots a user can purchase

## Notes

1. **Checkout Only for Limited Users**: Checkout is only accessible for:
   - Users with Free plan (max_music_uploads = 0)
   - Users who have reached their plan's upload limit

2. **Slot Priority**: System uses free slots first, then paid slots

3. **Tracking**: Each music upload is marked with `is_paid_upload` flag to track which slot type was used

4. **Consistent Design**: Checkout page follows same design pattern as subscription and gallery checkout (tab-based, SweetAlert notifications)

5. **Auto-Check**: Payment status is checked automatically every 30 seconds on checkout page

## Next Steps (Optional Enhancements)

1. Email notification when slots are activated
2. Admin dashboard to view music upload orders
3. Bulk slot purchase discounts
4. Slot expiration (if needed)
5. Refund system for unused slots

---

**Status**: ✅ COMPLETED
**Date**: March 29, 2026
**Version**: 1.0
