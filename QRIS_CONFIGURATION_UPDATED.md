# QRIS Configuration Updated

## Configuration Details

The DOKU QRIS configuration has been updated with the correct credentials:

### Database Configuration (`payment_gateway_configs` table)
- **Provider**: doku
- **Environment**: production
- **Client ID (Brand ID)**: BRN-0204-1754870435962
- **Merchant ID**: 75143
- **Partner Service ID**: 888994
- **Base URL**: https://api.doku.com

### QRIS-Specific Settings
- **Terminal ID**: A01 (hardcoded in DokuQrisService)
- **Client ID for QRIS**: 75143 (uses merchant_id field)

## How QRIS Works

1. **Merchant ID**: Used in QRIS generation API call (75143)
2. **Terminal ID**: Fixed value "A01" for all QRIS transactions
3. **Client ID**: Brand ID (BRN-0204-1754870435962) used in API headers as X-PARTNER-ID

## Configuration Files

### DokuQrisService.php
```php
// Line 45-48
$merchantId = $this->config->merchant_id ?? $this->config->client_id;
// Terminal ID must be alphanumeric 1-16 characters
// Use fixed terminal ID: A01
$terminalId = 'A01';
```

### Environment Variable
```env
DOKU_QRIS_ENABLED=true
```

## Testing QRIS

To test QRIS generation:

1. Go to music upload checkout: http://127.0.0.1:8000/dash/music-slots/buy
2. Select quantity and proceed to checkout
3. Click on "QRIS" tab
4. Enter phone number and click "Buat QRIS"

## Expected Behavior

- If QRIS is activated by DOKU: QR code will be generated successfully
- If QRIS is NOT activated: Error 5004701 with message "QRIS belum diaktifkan untuk merchant ini"

## Troubleshooting

If you still get error 5004701:
1. Contact DOKU support to activate QRIS for Merchant ID 75143
2. Provide them with:
   - Merchant ID: 75143
   - Terminal ID: A01
   - Client ID: BRN-0204-1754870435962

## Command to Update Config

If you need to update the configuration again:
```bash
php artisan doku:update-qris-config
```

## Files Modified

1. `app/Console/Commands/UpdateDokuQrisConfig.php` - New command to update QRIS config
2. Database: `payment_gateway_configs` table updated with merchant_id and partner_service_id

## Next Steps

1. Test QRIS generation in music upload checkout
2. If error 5004701 persists, contact DOKU to activate QRIS
3. Once activated, QRIS should work for all payment types (subscription, gallery, music)
