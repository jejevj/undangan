# Test Payment Gateway Configuration

## Status: READY TO TEST ✅

Middleware error telah diperbaiki. Sekarang siap untuk ditest.

## Perubahan yang Dilakukan

### 1. Fixed Middleware Error
- **Problem**: `Call to undefined method middleware()` di PaymentGatewayConfigController
- **Root Cause**: Base Controller class di Laravel 11 kosong, tidak punya method middleware()
- **Solution**: Pindahkan middleware dari constructor ke route definition

### 2. Files Updated
- `app/Http/Controllers/PaymentGatewayConfigController.php` - Hapus constructor middleware
- `routes/web.php` - Tambah middleware langsung di route resource

## Cara Test

### 1. Login sebagai Admin
```
URL: https://undanganberpesta.ourtestcloud.my.id/login
Email: admin@example.com
Password: password
```

### 2. Akses Menu Payment Gateway
- Buka sidebar menu
- Cari menu "Pembayaran" → "Konfigurasi Gateway"
- Atau langsung ke: `/dash/payment-gateway`

### 3. Test CRUD Operations

#### A. View List (Index)
- Harus bisa melihat daftar konfigurasi payment gateway
- Tabel kosong jika belum ada data
- Ada tombol "Tambah Konfigurasi"

#### B. Create New Config
1. Klik "Tambah Konfigurasi"
2. Isi form:
   - Provider: DOKU
   - Environment: Sandbox
   - Client ID: (dari DOKU dashboard)
   - Secret Key: (dari DOKU dashboard)
   - Base URL: https://api-sandbox.doku.com
   - Is Active: ✓
3. Klik "Simpan"
4. Harus redirect ke index dengan success message

#### C. Edit Config
1. Klik tombol "Edit" pada salah satu config
2. Ubah data (misal: ganti environment)
3. Secret Key bisa dikosongkan (tidak akan diupdate)
4. Klik "Update"
5. Harus redirect ke index dengan success message

#### D. Test Connection
1. Di halaman index, klik tombol "Test Connection"
2. Sistem akan melakukan test dengan:
   - Generate signature DOKU yang valid
   - Kirim test request ke DOKU API
   - Validasi response dari server
3. Hasil yang mungkin:
   - ✅ **Success**: "Koneksi berhasil! Credentials valid"
     - Status 200/201: API merespon dengan benar
     - Status 400 dengan error INVALID_REQUEST: Credentials valid, tapi test data ditolak (ini normal)
   - ❌ **Failed - Invalid Credentials**: "Credentials tidak valid!"
     - Status 401/403: Client ID atau Secret Key salah
   - ❌ **Failed - Connection Error**: "Tidak dapat terhubung ke server"
     - Base URL salah atau server tidak dapat dijangkau
   - ❌ **Failed - Other Error**: Error lainnya dengan detail message

**PENTING**: Test connection sekarang menggunakan signature authentication yang sebenarnya, jadi:
- Client ID dan Secret Key harus valid
- Base URL harus benar
- Tidak bisa lagi menggunakan credentials sembarangan

#### E. Delete Config
1. Klik tombol "Hapus"
2. Konfirmasi delete
3. Config terhapus dengan success message

### 4. Test Permissions

#### Test dengan User Non-Admin
1. Logout dari admin
2. Login sebagai user biasa
3. Coba akses `/dash/payment-gateway`
4. Harus dapat error 403 Forbidden atau redirect

#### Test dengan Role yang Tidak Punya Permission
- User tanpa permission `payment-gateway.view` tidak bisa akses
- User tanpa permission `payment-gateway.create` tidak bisa create
- User tanpa permission `payment-gateway.edit` tidak bisa edit
- User tanpa permission `payment-gateway.delete` tidak bisa delete

## Expected Results

### ✅ Success Indicators
- Tidak ada error middleware
- Semua halaman CRUD bisa diakses oleh admin
- Form validation bekerja
- Data tersimpan ke database dengan secret_key terenkripsi
- Test connection bisa dijalankan
- Permissions bekerja dengan benar

### ❌ Potential Issues
- Error 403 jika user tidak punya permission (ini normal)
- Error 500 jika ada masalah database
- Test connection gagal jika credentials salah (ini normal)

## Database Check

### Cek Data di Database
```sql
-- Lihat semua config
SELECT id, provider, environment, client_id, is_active, created_at 
FROM payment_gateway_configs;

-- Secret key harus terenkripsi
SELECT id, provider, secret_key 
FROM payment_gateway_configs;
-- secret_key harus berupa string panjang terenkripsi, bukan plaintext
```

### Cek Permissions
```sql
-- Lihat permissions payment gateway
SELECT * FROM permissions 
WHERE name LIKE 'payment-gateway%';

-- Cek role admin punya permissions
SELECT r.name as role, p.name as permission
FROM roles r
JOIN role_has_permissions rhp ON r.id = rhp.role_id
JOIN permissions p ON rhp.permission_id = p.id
WHERE p.name LIKE 'payment-gateway%';
```

## Next Steps After Testing

Jika semua test berhasil:
1. ✅ Phase 1 Complete: Payment Gateway Configuration
2. 🚀 Lanjut ke Phase 2: Payment Methods Management
3. 📝 Implementasi payment methods (VA, QRIS, E-Wallet, dll)

## Troubleshooting

### Error: "Call to undefined method middleware()"
- Sudah diperbaiki dengan memindahkan middleware ke routes

### Error: "Permission denied" atau 403
- Pastikan user login sebagai admin
- Cek permissions sudah di-seed dengan benar
- Run: `php artisan db:seed --class=PaymentGatewayPermissionSeeder`

### Error: "Table payment_gateway_configs doesn't exist"
- Run migration: `php artisan migrate`

### Test Connection Selalu Gagal
- Normal jika credentials belum benar
- Cek Client ID dan Secret Key dari DOKU dashboard
- Pastikan Base URL benar (sandbox vs production)
- Error 401/403 = Credentials salah
- Connection error = Base URL salah atau server tidak dapat dijangkau

### Test Connection Selalu Berhasil Padahal Credentials Salah
- Sudah diperbaiki! Sekarang menggunakan signature authentication yang sebenarnya
- Sistem akan generate signature HMAC-SHA256 seperti yang dibutuhkan DOKU
- Credentials salah akan menghasilkan error 401/403

## DOKU Credentials untuk Testing

### Sandbox Environment
- Base URL: `https://api-sandbox.doku.com`
- Client ID: (dapatkan dari DOKU Sandbox Dashboard)
- Secret Key: (dapatkan dari DOKU Sandbox Dashboard)
- Docs: https://developers.doku.com

### Production Environment
- Base URL: `https://api.doku.com`
- Client ID: (dapatkan dari DOKU Production Dashboard)
- Secret Key: (dapatkan dari DOKU Production Dashboard)

---

**Status**: Middleware error fixed ✅  
**Ready for**: Manual testing di browser  
**Next Phase**: Payment Methods Management (Phase 2)
