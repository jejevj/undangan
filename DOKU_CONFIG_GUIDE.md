# DOKU Payment Gateway Configuration Guide

## Overview

Sistem ini menggunakan DOKU sebagai payment gateway dengan support untuk:
- Virtual Account (VA) - BRI, BNI, Mandiri, CIMB, Permata
- E-Wallet - DANA, ShopeePay
- QRIS - Universal QR payment

## Configuration Fields

| Field | Description | Example |
|-------|-------------|---------|
| `provider` | Payment gateway provider | `doku` |
| `environment` | Environment mode | `production` or `sandbox` |
| `client_id` | Brand ID from DOKU | `BRN-0204-1754870435962` |
| `merchant_id` | Merchant ID for QRIS | `75143` |
| `partner_service_id` | Partner Service ID for VA (8 chars with spaces) | `  888994` |
| `secret_key` | Secret key (encrypted) | Auto-encrypted by Laravel |
| `private_key` | Private key (encrypted) | Auto-encrypted by Laravel |
| `public_key` | Your public key | PEM format |
| `doku_public_key` | DOKU's public key | PEM format |
| `base_url` | API base URL | `https://api.doku.com` |
| `is_active` | Active status | `true` or `false` |

## Important Notes

### Partner Service ID Format
- Must be exactly 8 characters
- Left-padded with spaces if needed
- Example: `  888994` (2 spaces + 6 digits)

### Merchant ID vs Client ID
- `client_id`: Brand ID (used for authentication)
- `merchant_id`: Merchant ID (used specifically for QRIS)
- These are different values from DOKU

### Terminal ID
- Fixed value: `A01`
- Used for QRIS transactions
- Must be alphanumeric, 1-16 characters

## Setup Methods

### Method 1: Using Seeder (Recommended for Fresh Install)

1. Run the seeder:
```bash
php artisan db:seed --class=PaymentGatewayConfigSeeder
```

2. Set encrypted keys manually:
```bash
php artisan tinker
```

```php
$config = \App\Models\PaymentGatewayConfig::where('provider', 'doku')->first();
$config->secret_key = 'YOUR_SECRET_KEY_HERE';
$config->private_key = 'YOUR_PRIVATE_KEY_HERE';
$config->save();
```

### Method 2: Export/Import (Recommended for Migration)

#### Export from existing system:
```bash
php artisan doku:export-config --output=doku-config-backup.json
```

This will create a JSON file with all configuration including encrypted keys.

#### Import to new system:
```bash
php artisan doku:import-config doku-config-backup.json
```

**Important:** The export file contains encrypted keys. Make sure:
- Both systems use the same `APP_KEY` in `.env`
- Keep the export file secure (add to `.gitignore`)

### Method 3: Manual Database Insert

```sql
INSERT INTO payment_gateway_configs (
    provider, environment, client_id, merchant_id, 
    partner_service_id, secret_key, private_key, 
    public_key, doku_public_key, base_url, is_active
) VALUES (
    'doku', 'production', 'BRN-0204-1754870435962', '75143',
    '  888994', 'ENCRYPTED_SECRET', 'ENCRYPTED_PRIVATE_KEY',
    'PUBLIC_KEY_PEM', 'DOKU_PUBLIC_KEY_PEM', 
    'https://api.doku.com', 1
);
```

## Verification

After setup, verify the configuration:

```bash
php artisan tinker
```

```php
$config = \App\Models\PaymentGatewayConfig::getActive('doku');
echo "Provider: " . $config->provider . "\n";
echo "Environment: " . $config->environment . "\n";
echo "Client ID: " . $config->client_id . "\n";
echo "Merchant ID: " . $config->merchant_id . "\n";
echo "Partner Service ID: '" . $config->partner_service_id . "'\n";
echo "Secret Key (decrypted): " . $config->decrypted_secret_key . "\n";
echo "Active: " . ($config->is_active ? 'Yes' : 'No') . "\n";
```

## Testing

### Test QRIS Generation:
```bash
php artisan test:qris-generate 1 1000
```

### Test VA Status Check:
```bash
php artisan test:va-status-check 1
```

## Webhook Configuration

Configure webhook URL in DOKU dashboard:
- URL: `https://yourdomain.com/webhook/doku/payment-notification`
- Method: POST
- This webhook handles VA, E-Wallet, and QRIS notifications

## Troubleshooting

### Issue: "QRIS belum diaktifkan"
- Ensure QRIS is activated in DOKU dashboard
- Verify `merchant_id` is correct (not Brand ID)

### Issue: "Invalid signature"
- Check `secret_key` is correctly set
- Verify `APP_KEY` is the same if importing config

### Issue: "VA number format error"
- Ensure `partner_service_id` is exactly 8 characters
- Check for leading spaces: `  888994` (2 spaces)

### Issue: "Terminal ID invalid"
- Terminal ID must be alphanumeric
- Current value: `A01` (fixed)

## Security Best Practices

1. Never commit `doku-config-backup.json` to git
2. Add to `.gitignore`:
   ```
   doku-config-backup.json
   storage/doku-config-backup.json
   ```
3. Rotate keys periodically
4. Use environment variables for sensitive data in production
5. Keep `APP_KEY` secure and consistent across environments

## Support

For DOKU-related issues:
- Documentation: https://docs.doku.com
- Support: support@doku.com

For application issues:
- Check logs: `storage/logs/va.log`
- Check Laravel logs: `storage/logs/laravel.log`
