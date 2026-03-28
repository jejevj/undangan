# DOKU SNAP API Fields - Update Complete

## Status: ✅ SELESAI

Update form create dan edit untuk menambahkan field-field SNAP API yang diperlukan oleh DOKU library.

## Field-field Baru yang Ditambahkan

### 1. Private Key (Encrypted)
- **Type**: Textarea
- **Required**: Optional
- **Encryption**: Yes
- **Description**: Private key untuk signature SNAP API
- **Format**: PEM format (-----BEGIN PRIVATE KEY-----)

### 2. Public Key
- **Type**: Textarea
- **Required**: Optional
- **Encryption**: No
- **Description**: Public key Anda yang di-upload ke DOKU Dashboard
- **Format**: PEM format (-----BEGIN PUBLIC KEY-----)

### 3. DOKU Public Key
- **Type**: Textarea
- **Required**: Optional
- **Encryption**: No
- **Description**: Public key dari DOKU untuk verifikasi callback
- **Format**: PEM format (-----BEGIN PUBLIC KEY-----)

### 4. Issuer
- **Type**: Text Input
- **Required**: Optional
- **Encryption**: No
- **Description**: Issuer identifier untuk merchant
- **Example**: "nama-merchant"

### 5. Auth Code (Encrypted)
- **Type**: Password Input
- **Required**: Optional
- **Encryption**: Yes
- **Description**: Auth code untuk payment tertentu

## File yang Diupdate

### 1. Create Form
**File**: `undangan/resources/views/payment-gateway/create.blade.php`

**Changes**:
- ✅ Menambahkan section "SNAP API Configuration (Optional)"
- ✅ Menambahkan 5 field baru (private_key, public_key, doku_public_key, issuer, auth_code)
- ✅ Update panduan di sidebar dengan informasi SNAP API
- ✅ Menambahkan placeholder dan helper text untuk setiap field

### 2. Edit Form
**File**: `undangan/resources/views/payment-gateway/edit.blade.php`

**Changes**:
- ✅ Menambahkan section "SNAP API Configuration (Optional)"
- ✅ Menambahkan 5 field baru dengan support untuk "kosongkan jika tidak ingin mengubah"
- ✅ Menambahkan status indicator untuk setiap SNAP API field
- ✅ Update sidebar dengan SNAP API Status checklist
- ✅ Menampilkan apakah field sudah terisi atau belum

### 3. Index View
**File**: `undangan/resources/views/payment-gateway/index.blade.php`

**Changes**:
- ✅ Menambahkan kolom "SNAP API" di tabel
- ✅ Menampilkan badge "Configured" jika semua key sudah terisi
- ✅ Menampilkan badge "Not Set" jika belum dikonfigurasi

## Controller Support

**File**: `undangan/app/Http/Controllers/PaymentGatewayConfigController.php`

**Status**: ✅ Sudah support semua field baru
- Validation rules sudah include field-field baru
- Encryption handling sudah ada di Model
- Update logic sudah handle "kosongkan jika tidak ingin mengubah"

## Model Support

**File**: `undangan/app/Models/PaymentGatewayConfig.php`

**Status**: ✅ Sudah support encryption
- `private_key` - encrypted via accessor/mutator
- `auth_code` - encrypted via accessor/mutator
- `public_key` - plain text
- `doku_public_key` - plain text
- `issuer` - plain text

## DokuService Integration

**File**: `undangan/app/Services/DokuService.php`

**Status**: ✅ Sudah menggunakan field-field baru

Constructor DOKU Snap library:
```php
new Snap(
    $config->decrypted_private_key ?? '',
    $config->public_key ?? '',
    $config->doku_public_key ?? '',
    $config->client_id,
    $config->issuer ?? '',
    $config->isProduction(),
    $config->decrypted_secret_key ?? '',
    $config->decrypted_auth_code ?? ''
);
```

## UI/UX Improvements

### Create Form
1. **Section Separator**: HR dengan heading "SNAP API Configuration (Optional)"
2. **Helper Text**: Setiap field punya penjelasan singkat
3. **Placeholder**: Format contoh untuk textarea (PEM format)
4. **Sidebar Guide**: Panduan lengkap cara mendapatkan SNAP API keys

### Edit Form
1. **Status Indicators**: Checklist menampilkan field mana yang sudah terisi
2. **Optional Update**: User bisa kosongkan field jika tidak ingin mengubah
3. **Visual Feedback**: Icon check/times untuk status setiap field
4. **Security Note**: Alert info tentang enkripsi data sensitif

### Index View
1. **SNAP API Column**: Badge status konfigurasi SNAP API
2. **Tooltip**: Hover untuk info lebih detail
3. **Color Coding**: Green = configured, Gray = not set

## Testing Checklist

### Create New Config
- [ ] Bisa create config tanpa SNAP API fields (optional)
- [ ] Bisa create config dengan semua SNAP API fields
- [ ] Private key dan auth code terenkripsi di database
- [ ] Validation error jika format salah

### Edit Existing Config
- [ ] Bisa update tanpa mengubah SNAP API fields
- [ ] Bisa update hanya beberapa SNAP API fields
- [ ] Status indicator menampilkan field yang sudah terisi
- [ ] Kosongkan field tidak menghapus data existing

### Index View
- [ ] Badge "Configured" muncul jika private_key, public_key, dan doku_public_key terisi
- [ ] Badge "Not Set" muncul jika salah satu key belum terisi
- [ ] Test connection masih berfungsi

## Next Steps

1. **Test dengan Credentials Real**
   - Input credentials production yang valid
   - Test connection menggunakan DOKU library
   - Verifikasi signature generation

2. **Implement Virtual Account**
   - Create VA endpoint
   - Update VA endpoint
   - Delete VA endpoint
   - Check VA status endpoint

3. **Implement Payment Methods**
   - Direct Debit
   - E-Wallet (OVO, GoPay, Dana, dll)
   - QRIS
   - Credit Card

4. **Webhook Handler**
   - Payment notification
   - Signature verification
   - Status update

## Notes

- Semua field SNAP API bersifat optional karena bisa juga menggunakan Non-SNAP API
- Private key dan auth code dienkripsi untuk keamanan
- Public keys tidak dienkripsi karena memang bersifat public
- Form sudah responsive dan user-friendly
- Validation sudah ada di controller level

---

**Updated**: 2026-03-28
**Status**: Ready for testing with real credentials
