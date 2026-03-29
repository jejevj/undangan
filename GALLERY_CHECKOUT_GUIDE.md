# Gallery Checkout System - User-Level Gallery Pool

## Overview
Gallery checkout system memungkinkan user membeli slot foto tambahan untuk gallery pool mereka. Slot yang dibeli bersifat user-level (bukan per-undangan) dan foto dapat digunakan di semua undangan user.

## Key Features
- User-level gallery pool (bukan per-undangan)
- Foto dapat digunakan di multiple undangan
- Fixed price: Rp 5.000 per slot
- Integrated dengan DOKU payment gateway (VA & QRIS)
- Auto-check payment status setiap 10 detik
- Webhook support untuk konfirmasi otomatis

## User Flow

### 1. Select Quantity
**Route:** `GET /dash/gallery/buy`
**View:** `resources/views/gallery/select-quantity.blade.php`

User memilih jumlah slot foto yang ingin dibeli:
- Menampilkan current slots dan used slots
- Input quantity (1-50 slot)
- Harga fixed: Rp 5.000/slot
- Kalkulasi total otomatis

### 2. Checkout
**Route:** `POST /dash/gallery/checkout`
**View:** `resources/views/gallery/checkout.blade.php`

User memilih metode pembayaran:
- Virtual Account (BCA, BNI, Mandiri, Permata, CIMB, Danamon)
- QRIS
- Admin fee dihitung otomatis berdasarkan payment channel

### 3. Process Payment
**Route:** `POST /dash/gallery/process-payment`
**Controller:** `GalleryCheckoutController@processPayment`

System membuat order dan generate payment:
- Create `GalleryOrder` (user-level, no invitation_id)
- Generate VA atau QRIS via DOKU
- Store payment details (va_number, qr_url, expired_at)

### 4. Payment Page
**Route:** `GET /dash/gallery-orders/{order}/payment`
**View:** `resources/views/gallery/payment.blade.php`

User melakukan pembayaran:
- Tampilkan VA number atau QR code
- Instruksi pembayaran
- Auto-check status setiap 10 detik
- Manual check button

### 5. Payment Confirmation
**Webhook:** `POST /webhook/doku/payment-notification`
**Controller:** `DokuWebhookController@handlePaymentNotification`

DOKU mengirim notifikasi saat pembayaran berhasil:
- Validate signature
- Mark order as paid
- Add purchased slots to user's gallery slots
- Log activity

## Database Schema

### user_gallery_slots
```sql
- id
- user_id (FK to users)
- free_slots (default: 10)
- purchased_slots (default: 0)
- created_at
- updated_at
```

### invitation_gallery
```sql
- id
- user_id (FK to users) -- NEW
- invitation_id (FK to invitations, nullable) -- Can be null for unassigned photos
- path
- caption
- order
- is_paid
- created_at
- updated_at
```

### gallery_orders
```sql
- id
- order_number
- user_id (FK to users)
- qty
- amount
- price_per_photo
- admin_fee
- status (pending/paid/expired)
- payment_method
- payment_channel_id (FK to payment_channels)
- va_number (nullable)
- payment_url (nullable)
- qr_string (nullable)
- qr_url (nullable)
- paid_at (nullable)
- expired_at (nullable)
- created_at
- updated_at
```

## Models

### UserGallerySlot
```php
// Methods
- totalSlots(): int // free_slots + purchased_slots
- usedSlots(): int // count of user's photos
- remainingSlots(): int // totalSlots - usedSlots
- addPurchasedSlots(int $qty): void // increment purchased_slots
```

### User
```php
// Methods
- getGallerySlots(): UserGallerySlot
- totalGallerySlots(): int
- usedGallerySlots(): int
- remainingGallerySlots(): int
```

### InvitationGallery
```php
// Fillable
- user_id
- invitation_id (nullable)
- path
- caption
- order
- is_paid
```

### GalleryOrder
```php
// Fillable
- order_number
- user_id
- qty
- amount
- price_per_photo
- admin_fee
- status
- payment_method
- payment_channel_id
- va_number
- payment_url
- qr_string
- qr_url
- paid_at
- expired_at
```

## Gallery Management

### Upload Photo
**Route:** `POST /dash/invitations/{invitation}/gallery`
**Controller:** `GalleryController@store`

User upload foto ke gallery pool:
- Check remaining slots
- Store photo with user_id
- Optionally assign to invitation (assign_to_invitation checkbox)
- Photo masuk ke user's gallery pool

### Assign/Unassign Photo
**Route:** `POST /dash/invitations/{invitation}/gallery/{photo}/toggle`
**Controller:** `GalleryController@toggleAssignment`

User assign/unassign foto ke undangan:
- If photo.invitation_id === invitation.id → unassign (set to null)
- Else → assign (set to invitation.id)
- Photo tetap ada di gallery pool

### Delete Photo
**Route:** `DELETE /dash/invitations/{invitation}/gallery/{photo}`
**Controller:** `GalleryController@destroy`

User hapus foto dari gallery pool:
- Delete file from storage
- Delete record from database
- Photo hilang dari semua undangan yang menggunakannya

## Payment Integration

### DOKU Virtual Account
```php
$vaRecord = $this->vaService->createVirtualAccount(
    auth()->user(),
    'gallery_order',
    $total,
    $order->id,
    ['channel' => $paymentChannel->code]
);
```

### DOKU QRIS
```php
$qrisRecord = $this->qrisService->createQris(
    auth()->user(),
    'gallery_order',
    $total,
    $order->id
);
```

### Webhook Handler
```php
protected function activateGalleryUpgrade($payment)
{
    $order = GalleryOrder::find($payment->reference_id);
    $order->update(['status' => 'paid', 'paid_at' => now()]);
    
    // Add slots to user
    $userSlots = $order->user->getGallerySlots();
    $userSlots->addPurchasedSlots($order->qty);
}
```

## Routes

```php
// Gallery Checkout (User-Level)
Route::get('gallery/buy', [GalleryCheckoutController::class, 'selectQuantity'])
    ->name('gallery.select-quantity');
Route::post('gallery/checkout', [GalleryCheckoutController::class, 'checkout'])
    ->name('gallery.checkout');
Route::post('gallery/process-payment', [GalleryCheckoutController::class, 'processPayment'])
    ->name('gallery.process-payment');
Route::get('gallery-orders/{order}/payment', [GalleryCheckoutController::class, 'payment'])
    ->name('gallery.payment');
Route::get('gallery-orders/{order}/check-status', [GalleryCheckoutController::class, 'checkStatus'])
    ->name('gallery.check-status');

// Gallery Management (Per Invitation)
Route::get('invitations/{invitation}/gallery', [GalleryController::class, 'index'])
    ->name('invitations.gallery.index');
Route::post('invitations/{invitation}/gallery', [GalleryController::class, 'store'])
    ->name('invitations.gallery.store');
Route::delete('invitations/{invitation}/gallery/{photo}', [GalleryController::class, 'destroy'])
    ->name('invitations.gallery.destroy');
Route::post('invitations/{invitation}/gallery/{photo}/toggle', [GalleryController::class, 'toggleAssignment'])
    ->name('invitations.gallery.toggle');
```

## Testing

### Test Upload
1. Login sebagai user
2. Buka `/dash/invitations/{id}/gallery`
3. Upload foto (akan masuk ke gallery pool)
4. Check: foto muncul di "Gallery Pool Saya"
5. Check: foto juga muncul di "Foto di Undangan Ini" (jika assign_to_invitation checked)

### Test Assign/Unassign
1. Upload foto tanpa assign
2. Foto muncul di "Gallery Pool Saya" tapi tidak di "Foto di Undangan Ini"
3. Click tombol + (assign)
4. Foto muncul di kedua section
5. Click tombol - (unassign)
6. Foto hilang dari "Foto di Undangan Ini" tapi tetap di "Gallery Pool Saya"

### Test Buy Slots
1. Upload foto sampai slot habis
2. Click "Beli Slot Foto"
3. Pilih quantity
4. Pilih payment method
5. Lakukan pembayaran (VA atau QRIS)
6. Wait for auto-check atau click "Cek Status"
7. Setelah paid, redirect ke dashboard
8. Check: purchased_slots bertambah di user_gallery_slots

### Test Webhook
```bash
# Simulate DOKU webhook
curl -X POST http://localhost:8000/webhook/doku/payment-notification \
  -H "Content-Type: application/json" \
  -H "X-SIGNATURE: {signature}" \
  -H "X-TIMESTAMP: {timestamp}" \
  -d '{
    "virtualAccountNo": "...",
    "paidAmount": {"value": "15000"},
    "transactionDate": "...",
    "trxId": "..."
  }'
```

## Notes

1. **User-Level Gallery Pool**: Foto tidak terikat ke undangan tertentu, bisa digunakan di semua undangan user
2. **Fixed Price**: Harga per slot fixed Rp 5.000 (tidak tergantung template)
3. **Free Slots**: Setiap user dapat 10 free slots saat pertama kali
4. **Assign/Unassign**: User bisa assign/unassign foto ke undangan kapan saja
5. **Delete**: Menghapus foto akan menghapusnya dari semua undangan yang menggunakannya
6. **Payment**: Terintegrasi dengan DOKU VA dan QRIS
7. **Auto-Check**: Status pembayaran dicek otomatis setiap 10 detik
8. **Webhook**: DOKU akan mengirim notifikasi saat pembayaran berhasil

## Migration Commands

```bash
# Create user_gallery_slots table
php artisan migrate

# Update invitation_gallery table (add user_id, make invitation_id nullable)
php artisan migrate

# Update gallery_orders table (remove invitation_id)
php artisan migrate
```
