# DOKU PHP Library - Implementation Plan

## 🎯 New Approach: Using Official DOKU Library

Kita akan menggunakan library resmi DOKU yang jauh lebih reliable dan sudah handle semua kompleksitas signature generation.

## 📦 Library Information

- **Package**: `doku/doku-php-library`
- **Repository**: https://github.com/PTNUSASATUINTIARTHA-DOKU/doku-php-library
- **Packagist**: https://packagist.org/packages/doku/doku-php-library
- **Installation**: `composer require doku/doku-php-library`

## ✅ Advantages

1. **Official Support**: Maintained by DOKU team
2. **Complete Features**: Virtual Account, Direct Debit, E-Wallet, dll
3. **Signature Handled**: Tidak perlu manual generate signature
4. **Error Handling**: Built-in error handling
5. **Documentation**: Lengkap dengan examples
6. **Updates**: Akan selalu up-to-date dengan DOKU API

## 🔄 Implementation Steps

### Step 1: Install Library
```bash
cd undangan
composer require doku/doku-php-library
```

### Step 2: Update Database Schema
Tambah field yang dibutuhkan untuk SNAP API:
- `private_key` (text, encrypted)
- `public_key` (text)
- `doku_public_key` (text)
- `issuer` (string, nullable)
- `auth_code` (string, nullable, encrypted)

### Step 3: Update Model
Update `PaymentGatewayConfig` model untuk support field baru.

### Step 4: Update CRUD Forms
Update form create/edit untuk input:
- Private Key
- Public Key  
- DOKU Public Key
- Issuer (optional)
- Auth Code (optional)

### Step 5: Create DOKU Service Wrapper
Buat service class yang wrap DOKU library untuk kemudahan penggunaan.

### Step 6: Implement Features
- Virtual Account (DGPC & MGPC)
- Check VA Status
- Payment methods
- Refund
- Balance inquiry

## 📋 Detailed Implementation

### Database Migration
```php
Schema::table('payment_gateway_configs', function (Blueprint $table) {
    $table->text('private_key')->nullable();
    $table->text('public_key')->nullable();
    $table->text('doku_public_key')->nullable();
    $table->string('issuer')->nullable();
    $table->text('auth_code')->nullable();
});
```

### Model Update
```php
protected $casts = [
    'secret_key' => 'encrypted',
    'private_key' => 'encrypted',
    'auth_code' => 'encrypted',
    'is_active' => 'boolean',
];
```

### Service Wrapper
```php
namespace App\Services;

use Doku\Snap\Snap;
use App\Models\PaymentGatewayConfig;

class DokuService
{
    protected $snap;
    protected $config;
    
    public function __construct(PaymentGatewayConfig $config)
    {
        $this->config = $config;
        $this->snap = new Snap(
            $config->private_key,
            $config->public_key,
            $config->doku_public_key,
            $config->client_id,
            $config->issuer,
            $config->environment === 'production',
            $config->secret_key,
            $config->auth_code
        );
    }
    
    public function createVirtualAccount($data)
    {
        // Implementation
    }
    
    public function checkStatus($data)
    {
        // Implementation
    }
}
```

### Test Connection (Simplified)
```php
public function testConnection(PaymentGatewayConfig $config)
{
    try {
        $doku = new DokuService($config);
        
        // Test dengan create VA sederhana
        $result = $doku->createVirtualAccount([
            'partnerServiceId' => 'TEST',
            'customerNo' => 'TEST123',
            // ... minimal data
        ]);
        
        return response()->json([
            'success' => true,
            'message' => 'Koneksi berhasil!',
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 400);
    }
}
```

## 🚀 Benefits of This Approach

### Before (Manual Implementation)
- ❌ Complex signature generation
- ❌ Multiple API versions (SNAP vs Non-SNAP)
- ❌ Manual header management
- ❌ Prone to errors
- ❌ Hard to maintain

### After (Using Library)
- ✅ Simple initialization
- ✅ All API versions supported
- ✅ Automatic signature generation
- ✅ Error handling built-in
- ✅ Easy to maintain
- ✅ Official support

## 📝 Next Steps

1. **Install Library**: `composer require doku/doku-php-library`
2. **Update Database**: Add new fields for SNAP API
3. **Update Forms**: Add input fields for keys
4. **Create Service**: Wrap DOKU library
5. **Test**: Test connection dengan library
6. **Implement Features**: Virtual Account, Payment, dll

## 🎓 Key Generation Guide

Untuk generate private/public key pair:

```bash
# Generate private key RSA
openssl genrsa -out private.key 2048

# Set passphrase (optional)
openssl pkcs8 -topk8 -inform PEM -outform PEM -in private.key -out pkcs8.key -v1 PBE-SHA1-3DES

# Generate public key
openssl rsa -in private.key -outform PEM -pubout -out public.pem
```

## 📚 Documentation References

- [DOKU PHP Library GitHub](https://github.com/PTNUSASATUINTIARTHA-DOKU/doku-php-library)
- [DOKU API Documentation](https://developers.doku.com)
- [Packagist Package](https://packagist.org/packages/doku/doku-php-library)

---

**Status**: Ready to implement with official library  
**Estimated Time**: 2-3 hours for complete implementation  
**Complexity**: Much simpler than manual implementation  
**Reliability**: High (official library)
