# DOKU Payment Gateway - Quick Reference

## Routes

### Web Routes
```
GET    /dash/payment-gateway                              → index
GET    /dash/payment-gateway/create                       → create form
POST   /dash/payment-gateway                              → store
GET    /dash/payment-gateway/{id}/edit                    → edit form
PUT    /dash/payment-gateway/{id}                         → update
DELETE /dash/payment-gateway/{id}                         → destroy
POST   /dash/payment-gateway/{id}/test-connection         → test DOKU API
GET    /dash/payment-gateway/{id}/debug-signature         → debug (dev only)
```

## Model: PaymentGatewayConfig

### Fillable Fields
```php
'provider'          // string: 'doku'
'environment'       // enum: 'sandbox', 'production'
'client_id'         // string: Client ID dari DOKU
'secret_key'        // encrypted: Secret Key dari DOKU
'private_key'       // encrypted: RSA private key (PEM)
'public_key'        // string: RSA public key (PEM)
'doku_public_key'   // string: DOKU's public key (PEM)
'issuer'            // string: Merchant identifier
'auth_code'         // encrypted: Auth code untuk payment
'base_url'          // string: API base URL
'is_active'         // boolean: Status aktif/nonaktif
```

### Accessors (Decrypted)
```php
$config->decrypted_secret_key      // Decrypt secret_key
$config->decrypted_private_key     // Decrypt private_key
$config->decrypted_auth_code       // Decrypt auth_code
```

### Methods
```php
PaymentGatewayConfig::getActive('doku')  // Get active config
$config->isSandbox()                     // Check if sandbox
$config->isProduction()                  // Check if production
```

## Service: DokuService

### Constructor
```php
$dokuService = new DokuService($config);
```

### Available Methods

#### Connection & Token
```php
$dokuService->testConnection()                    // Test API connection
$dokuService->getTokenB2B2C($authCode)           // Get B2B2C token
$dokuService->validateTokenB2B($authorization)   // Validate B2B token
```

#### Virtual Account
```php
$dokuService->createVirtualAccount($data)        // Create VA
$dokuService->updateVirtualAccount($data)        // Update VA
$dokuService->deleteVirtualAccount($data)        // Delete VA
$dokuService->checkVirtualAccountStatus($data)   // Check VA status
```

#### Payment
```php
$dokuService->doPayment($data, $authCode, $ip)              // Do payment
$dokuService->doPaymentJumpApp($data, $deviceId, $ip)       // Payment jump app
$dokuService->checkStatus($data)                            // Check transaction status
```

#### Refund & Balance
```php
$dokuService->doRefund($data, $authCode, $ip, $deviceId)    // Refund
$dokuService->balanceInquiry($data, $authCode, $ip)         // Balance inquiry
```

#### Account Management
```php
$dokuService->accountBinding($data, $ip, $deviceId)         // Bind account
$dokuService->accountUnbinding($data, $ip)                  // Unbind account
$dokuService->cardRegistration($data)                       // Register card
$dokuService->cardUnbinding($data)                          // Unbind card
```

## Controller: PaymentGatewayConfigController

### Actions
```php
index()                                    // List all configs
create()                                   // Show create form
store(Request $request)                    // Save new config
edit(PaymentGatewayConfig $config)         // Show edit form
update(Request $request, $config)          // Update config
destroy(PaymentGatewayConfig $config)      // Delete config
testConnection(PaymentGatewayConfig $config) // Test DOKU API
debugSignature(PaymentGatewayConfig $config) // Debug signature (dev only)
```

### Validation Rules
```php
'provider'          => 'required|string|max:50',
'environment'       => 'required|in:sandbox,production',
'client_id'         => 'required|string|max:255',
'secret_key'        => 'nullable|string',
'private_key'       => 'nullable|string',
'public_key'        => 'nullable|string',
'doku_public_key'   => 'nullable|string',
'issuer'            => 'nullable|string|max:255',
'auth_code'         => 'nullable|string',
'base_url'          => 'required|url|max:255',
'is_active'         => 'boolean',
```

## Views

### Index
**Path**: `resources/views/payment-gateway/index.blade.php`
- List all configurations
- SNAP API status badge
- Test connection button
- Edit/Delete actions

### Create
**Path**: `resources/views/payment-gateway/create.blade.php`
- Basic configuration fields
- SNAP API configuration section (optional)
- Sidebar guide

### Edit
**Path**: `resources/views/payment-gateway/edit.blade.php`
- Same fields as create
- Status indicators for SNAP API fields
- Optional update (kosongkan jika tidak ingin mengubah)

## JavaScript (AJAX)

### Test Connection
```javascript
$.ajax({
    url: `/dash/payment-gateway/${configId}/test-connection`,
    method: 'POST',
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
        alert('✅ ' + response.message);
    },
    error: function(xhr) {
        const message = xhr.responseJSON?.message || 'Gagal test koneksi';
        alert('❌ ' + message);
    }
});
```

## Database

### Table: payment_gateway_configs
```sql
id                  BIGINT UNSIGNED PRIMARY KEY
provider            VARCHAR(50)
environment         ENUM('sandbox', 'production')
client_id           VARCHAR(255)
secret_key          TEXT (encrypted)
private_key         TEXT (encrypted, nullable)
public_key          TEXT (nullable)
doku_public_key     TEXT (nullable)
issuer              VARCHAR(255) (nullable)
auth_code           TEXT (encrypted, nullable)
base_url            VARCHAR(255)
is_active           BOOLEAN DEFAULT 0
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

### Indexes
```sql
INDEX idx_provider (provider)
INDEX idx_is_active (is_active)
INDEX idx_provider_active (provider, is_active)
```

## Permissions

### Required Permissions
```
payment-gateway.index      // View list
payment-gateway.create     // Create new config
payment-gateway.edit       // Edit config
payment-gateway.delete     // Delete config
```

### Menu
```
Payment Gateway
├── Konfigurasi Payment Gateway (index)
├── Tambah Konfigurasi (create)
└── Test Connection (AJAX)
```

## Environment Variables

### Required in .env
```env
APP_KEY=base64:...                    # For encryption
APP_URL=https://yourdomain.com        # Base URL
```

### DOKU Credentials (stored in database)
```
CLIENT_ID           # From DOKU Dashboard
SECRET_KEY          # From DOKU Dashboard
PRIVATE_KEY         # Your RSA private key
PUBLIC_KEY          # Your RSA public key
DOKU_PUBLIC_KEY     # DOKU's public key
```

## API Endpoints (DOKU)

### Sandbox
```
Base URL: https://api-sandbox.doku.com
```

### Production
```
Base URL: https://api.doku.com
```

### Common Endpoints
```
POST   /checkout/v1/payment              # Create payment
POST   /orders/v1/virtual-accounts       # Create VA
PUT    /orders/v1/virtual-accounts       # Update VA
DELETE /orders/v1/virtual-accounts       # Delete VA
GET    /orders/v1/status                 # Check status
POST   /orders/v1/refund                 # Refund
```

## Testing

### Manual Test
1. Login ke dashboard admin
2. Menu **Payment Gateway** > **Tambah Konfigurasi**
3. Isi credentials (sandbox untuk testing)
4. Klik **Simpan**
5. Klik **Test Connection**
6. Verify response sukses

### Unit Test (TODO)
```php
// Test create config
$this->post('/dash/payment-gateway', $data)
    ->assertRedirect()
    ->assertSessionHas('success');

// Test encryption
$config = PaymentGatewayConfig::first();
$this->assertNotEquals($data['secret_key'], $config->secret_key);
$this->assertEquals($data['secret_key'], $config->decrypted_secret_key);

// Test DOKU service
$service = new DokuService($config);
$result = $service->testConnection();
$this->assertTrue($result['success']);
```

## Common Issues

### Issue: "Invalid Signature"
**Solution**: 
- Check private key format (PEM)
- Verify public key uploaded to DOKU
- Ensure no extra spaces/newlines

### Issue: "Client ID required"
**Solution**: 
- Verify client_id is filled
- Check database value not null

### Issue: "Token B2B failed"
**Solution**: 
- Check internet connection
- Verify base URL correct
- Ensure credentials valid

### Issue: "Encryption error"
**Solution**: 
- Check APP_KEY in .env
- Run `php artisan key:generate` if needed
- Clear config cache: `php artisan config:clear`

## Useful Commands

```bash
# Clear caches
php artisan view:clear
php artisan config:clear
php artisan cache:clear

# Check routes
php artisan route:list --name=payment-gateway

# Run migrations
php artisan migrate

# Seed permissions
php artisan db:seed --class=PaymentGatewayPermissionSeeder
php artisan db:seed --class=PaymentGatewayMenuSeeder

# Generate RSA keys
openssl genrsa -out private.key 2048
openssl rsa -in private.key -pubout -out public.key
```

## Documentation Files

```
DOKU-LIBRARY-IMPLEMENTATION-PLAN.md      # Implementation plan
DOKU-SNAP-API-FIELDS-UPDATE.md           # Field update details
CARA-KONFIGURASI-DOKU-SNAP.md            # User guide
DOKU-PHASE2-FORM-UPDATE-COMPLETE.md      # Phase 2 summary
DOKU-QUICK-REFERENCE.md                  # This file
```

---

**Updated**: 2026-03-28
**Version**: 1.0
**Status**: Ready for use
