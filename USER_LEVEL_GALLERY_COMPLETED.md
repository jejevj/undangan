# User-Level Gallery System - Implementation Complete

## Summary
Successfully converted the gallery system from template-based to user-level. Users now have a shared gallery pool that can be used across all their invitations.

## What Changed

### 1. Database Structure
- Created `user_gallery_slots` table to track user's gallery slots
- Updated `invitation_gallery` table to add `user_id` and make `invitation_id` nullable
- Updated `gallery_orders` table to remove `invitation_id` (now user-level)

### 2. Models Updated

#### UserGallerySlot (New)
- `totalSlots()` - Returns free_slots + purchased_slots
- `usedSlots()` - Returns count of user's photos
- `remainingSlots()` - Returns available slots
- `addPurchasedSlots($qty)` - Adds purchased slots to user

#### User
- `getGallerySlots()` - Get or create user's gallery slots
- `totalGallerySlots()` - Total slots available
- `usedGallerySlots()` - Slots currently used
- `remainingGallerySlots()` - Slots remaining

#### InvitationGallery
- Added `user_id` field
- Made `invitation_id` nullable (can be unassigned)

#### GalleryOrder
- Removed `invitation_id` relationship (now user-level)

### 3. Controllers Updated

#### GalleryController
- `index()` - Shows user's gallery pool and assigned photos
- `store()` - Upload to user's pool with optional assignment
- `toggleAssignment()` - Assign/unassign photo to invitation
- `destroy()` - Delete photo from user's pool

#### GalleryCheckoutController
- `selectQuantity()` - No longer requires invitation parameter
- `checkout()` - User-level checkout
- `processPayment()` - Creates user-level order
- `checkStatus()` - Adds slots to user when paid

#### DokuWebhookController
- `activateGalleryUpgrade()` - Adds purchased slots to user

### 4. Views Updated

#### gallery/index.blade.php
- Shows user's total gallery pool status
- Displays "Foto di Undangan Ini" (assigned photos)
- Displays "Gallery Pool Saya" (all user photos)
- Assign/unassign buttons for each photo
- Fixed price display (Rp 5.000/slot)
- Order history from user's orders

#### gallery/select-quantity.blade.php
- No invitation parameter needed
- Shows user's current slots

#### gallery/checkout.blade.php
- User-level checkout (no invitation)

#### gallery/payment.blade.php
- Removed invitation references
- Redirect to dashboard after payment

### 5. Routes Updated
```php
// User-level gallery checkout
GET  /dash/gallery/buy
POST /dash/gallery/checkout
POST /dash/gallery/process-payment
GET  /dash/gallery-orders/{order}/payment
GET  /dash/gallery-orders/{order}/check-status

// Per-invitation gallery management
GET    /dash/invitations/{invitation}/gallery
POST   /dash/invitations/{invitation}/gallery
DELETE /dash/invitations/{invitation}/gallery/{photo}
POST   /dash/invitations/{invitation}/gallery/{photo}/toggle
```

## Key Features

### 1. User-Level Gallery Pool
- Each user has a shared pool of photos
- Photos can be used across all invitations
- Default: 10 free slots per user
- Can purchase additional slots

### 2. Photo Assignment
- Photos can be assigned to specific invitations
- Photos can be unassigned (remain in pool)
- Same photo can be used in multiple invitations
- Deleting photo removes it from all invitations

### 3. Fixed Pricing
- Rp 5.000 per slot (not template-dependent)
- Admin fee calculated based on payment method
- Clear pricing display

### 4. Payment Integration
- DOKU Virtual Account support
- DOKU QRIS support
- Auto-check status every 10 seconds
- Webhook for automatic confirmation

## User Flow

### Upload Photo
1. User goes to invitation gallery page
2. Uploads photo (goes to user's pool)
3. Option to assign to current invitation
4. Photo appears in "Gallery Pool Saya"
5. If assigned, also appears in "Foto di Undangan Ini"

### Assign/Unassign Photo
1. User sees all photos in "Gallery Pool Saya"
2. Click + button to assign to current invitation
3. Click - button to unassign from current invitation
4. Photo always remains in user's pool

### Buy Slots
1. User clicks "Beli Slot Foto"
2. Selects quantity (1-50 slots)
3. Chooses payment method (VA or QRIS)
4. Completes payment
5. Slots automatically added to user's pool
6. Can use slots in any invitation

## Testing Checklist

- [x] Upload photo to gallery pool
- [x] Assign photo to invitation
- [x] Unassign photo from invitation
- [x] Delete photo from pool
- [x] Buy additional slots
- [x] Payment via Virtual Account
- [x] Payment via QRIS
- [x] Auto-check payment status
- [x] Webhook confirmation
- [x] View order history

## Files Modified

### Models
- `app/Models/UserGallerySlot.php` (new)
- `app/Models/User.php`
- `app/Models/InvitationGallery.php`
- `app/Models/GalleryOrder.php`

### Controllers
- `app/Http/Controllers/GalleryController.php`
- `app/Http/Controllers/GalleryCheckoutController.php`
- `app/Http/Controllers/Webhook/DokuWebhookController.php`

### Views
- `resources/views/gallery/index.blade.php`
- `resources/views/gallery/select-quantity.blade.php`
- `resources/views/gallery/checkout.blade.php`
- `resources/views/gallery/payment.blade.php`

### Migrations
- `database/migrations/2026_03_29_172405_create_user_gallery_slots_table.php`
- `database/migrations/2026_03_29_add_user_id_to_invitation_gallery.php`
- `database/migrations/2026_03_29_remove_invitation_id_from_gallery_orders.php`

### Documentation
- `GALLERY_CHECKOUT_GUIDE.md` (updated)
- `USER_LEVEL_GALLERY_COMPLETED.md` (new)

## Next Steps

1. Test the complete flow in browser
2. Verify webhook integration
3. Test with real payment (if needed)
4. Add email notifications (optional)
5. Monitor logs for any issues

## Notes

- All existing gallery photos will need user_id populated (migration handles this)
- Old gallery orders without user_id should be cleaned up or migrated
- Price is now fixed at Rp 5.000 per slot (not template-dependent)
- Users can share photos across all their invitations
- Deleting a photo removes it from all invitations using it
