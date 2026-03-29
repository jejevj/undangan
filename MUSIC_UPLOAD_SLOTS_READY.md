# Music Upload Slots - Ready for Testing

## Status: ✅ READY

All music upload slot purchase functionality has been implemented and configured correctly.

## Configuration Summary

### QRIS Configuration
- **Merchant ID**: 75143 ✅
- **Terminal ID**: A01 ✅
- **Client ID (Brand ID)**: BRN-0204-1754870435962 ✅
- **Partner Service ID**: 888994 ✅
- **Environment**: production ✅
- **Base URL**: https://api.doku.com ✅
- **QRIS Enabled**: true ✅

### Pricing
- **Price per slot**: Rp 10.000
- **Admin fee**: Rp 0
- **Payment methods**: Virtual Account (BCA, BNI, Mandiri, Permata, CIMB) & QRIS

## How It Works

### For Users

1. **Access Music Page**: http://127.0.0.1:8000/dash/music
2. **Click "Upload Lagu" button**
3. **System checks**:
   - If user has free slots from plan → Show upload form
   - If user has purchased slots → Show upload form
   - If no slots available → Redirect to buy slots page

### Buy Slots Flow

1. **Select Quantity**: http://127.0.0.1:8000/dash/music-slots/buy
   - Choose 1-10 slots
   - See total price calculation
   
2. **Checkout**: http://127.0.0.1:8000/dash/music-slots/checkout?qty=X
   - Choose payment method (VA or QRIS)
   - Enter phone number
   - Create payment
   
3. **Payment Page**:
   - For VA: Shows bank name and VA number
   - For QRIS: Shows QR code to scan
   - Auto-check payment status every 10 seconds
   
4. **After Payment**:
   - Webhook updates order status to "paid"
   - User can upload music using purchased slots
   - Slots are tracked separately (paid vs free)

## Database Tables

### music_upload_orders
Stores all slot purchase orders with payment details:
- order_number, user_id, qty, amount
- payment_method, va_number, qr_string
- status (pending/paid/failed/expired)
- paid_at, expired_at

### music
Tracks uploaded music files:
- is_paid_upload (boolean) - tracks if upload used paid slot
- uploaded_by (user_id)

### doku_virtual_accounts
Stores VA payment records for music_upload type

### doku_qris_payments
Stores QRIS payment records for music_upload type

## Routes

All routes are under `/dash` prefix and require authentication:

```
GET  /dash/music-slots/buy                              → Select quantity page
GET  /dash/music-slots/checkout?qty=X&order_id=Y        → Checkout page
POST /dash/music-upload-orders/{order}/create-va        → Create Virtual Account
POST /dash/music-upload-orders/{order}/create-qris      → Create QRIS
GET  /dash/music-upload-orders/{order}/check-status     → Check payment status
```

## Controllers

### MusicUploadCheckoutController
- `selectQuantity()` - Show quantity selection page
- `checkout()` - Show checkout page with payment options
- `createVA()` - Generate Virtual Account
- `createQris()` - Generate QRIS QR code
- `checkStatus()` - Check payment status via API

### MusicController
- `uploadForm()` - Check slots and redirect to buy if needed
- `userUpload()` - Handle file upload and track slot usage

## Slot Tracking Logic

### Free Slots (from subscription plan)
- Defined in `subscription_plans.max_music_uploads`
- Free plan: 0 slots
- Premium plans: 5-10 slots or unlimited
- Tracked by counting `music.uploaded_by` where `is_paid_upload = false`

### Paid Slots (purchased separately)
- Purchased via `music_upload_orders`
- Price: Rp 10.000 per slot
- Tracked by:
  - Total purchased: `SUM(qty) WHERE status = 'paid'`
  - Used: `COUNT(*) WHERE is_paid_upload = true`
  - Remaining: Total - Used

### Upload Priority
1. Use free slots first (if available)
2. Use paid slots when free slots exhausted
3. Redirect to buy page if no slots available

## Webhook Integration

### DokuWebhookController
Handles payment notifications from DOKU:

```php
public function activateMusicUpload($order)
{
    // Mark order as paid
    $order->update(['status' => 'paid', 'paid_at' => now()]);
    
    // Slots are automatically available for user
    // No need to create separate records
}
```

## Testing Checklist

### ✅ Configuration
- [x] QRIS merchant ID set to 75143
- [x] Terminal ID set to A01
- [x] Routes registered correctly
- [x] Database tables created
- [x] Webhook handler implemented

### 🧪 Manual Testing Required

1. **Free Plan User**:
   - Login as free plan user
   - Click "Upload Lagu" → Should redirect to buy slots
   - Buy 1 slot → Complete payment
   - Upload music → Should work
   - Try upload again → Should redirect to buy more slots

2. **Premium Plan User**:
   - Login as premium user
   - Click "Upload Lagu" → Should show upload form
   - Upload music → Should use free slot
   - After exhausting free slots → Should use paid slots
   - After exhausting all slots → Should redirect to buy

3. **Payment Methods**:
   - Test VA payment (BCA, BNI, Mandiri, etc.)
   - Test QRIS payment (if activated by DOKU)
   - Verify auto-check status works
   - Verify webhook updates order status

## QRIS Status

⚠️ **QRIS may show error 5004701** if not activated by DOKU.

**Error message**: "QRIS belum diaktifkan untuk merchant ini. Silakan hubungi DOKU untuk aktivasi QRIS atau gunakan metode pembayaran lain (Virtual Account / E-Wallet)."

**Solution**: Contact DOKU support to activate QRIS for:
- Merchant ID: 75143
- Terminal ID: A01
- Client ID: BRN-0204-1754870435962

**Workaround**: Use Virtual Account payment method instead.

## Commands

### Update QRIS Configuration
```bash
php artisan doku:update-qris-config
```

### Check Database
```bash
# Check music upload orders
php artisan tinker --execute="echo \App\Models\MusicUploadOrder::count() . ' orders';"

# Check user's paid slots
php artisan tinker --execute="
\$user = \App\Models\User::find(1);
\$paid = \App\Models\MusicUploadOrder::where('user_id', \$user->id)->where('status', 'paid')->sum('qty');
\$used = \App\Models\Music::where('uploaded_by', \$user->id)->where('is_paid_upload', true)->count();
echo 'Paid: ' . \$paid . ', Used: ' . \$used . ', Remaining: ' . (\$paid - \$used);
"
```

## Files Modified/Created

### New Files
1. `app/Console/Commands/UpdateDokuQrisConfig.php` - Command to update QRIS config
2. `QRIS_CONFIGURATION_UPDATED.md` - QRIS configuration documentation
3. `MUSIC_UPLOAD_SLOTS_READY.md` - This file

### Modified Files
1. `storage/doku-config-backup.json` - Contains correct QRIS credentials
2. `.env` - DOKU_QRIS_ENABLED=true
3. Database: `payment_gateway_configs` table updated

### Existing Files (Already Implemented)
1. `app/Http/Controllers/MusicUploadCheckoutController.php`
2. `app/Http/Controllers/MusicController.php`
3. `app/Models/MusicUploadOrder.php`
4. `app/Services/DokuQrisService.php`
5. `resources/views/music/select-quantity.blade.php`
6. `resources/views/music/checkout.blade.php`
7. `routes/web.php`

## Next Steps

1. ✅ QRIS configuration updated
2. 🧪 Test music upload slot purchase flow
3. 🧪 Test payment with Virtual Account
4. 🧪 Test QRIS (if activated by DOKU)
5. 🧪 Verify webhook updates order status
6. 🧪 Verify slot tracking works correctly

## Support

If you encounter issues:
1. Check logs: `storage/logs/laravel.log` and `storage/logs/va-*.log`
2. Verify QRIS config: `php artisan tinker --execute="echo json_encode(\App\Models\PaymentGatewayConfig::first()->toArray(), JSON_PRETTY_PRINT);"`
3. Check routes: `php artisan route:list --name=music.slots`
4. Contact DOKU support for QRIS activation issues
