# Quick Test - Registration System

## Persiapan Database

Sebelum test registrasi, pastikan role dan plan sudah ada:

```bash
# 1. Seed role "pengguna"
php artisan db:seed --class=UserRoleSeeder

# 2. Seed pricing plans (termasuk free plan)
php artisan db:seed --class=PricingPlanSeeder
```

## Test Registrasi

1. Akses halaman: `http://127.0.0.1:8000/register`
2. Klik tab "Daftar"
3. Isi form:
   - Nama: Test User
   - Email: testuser@example.com
   - Password: password123
   - Konfirmasi Password: password123
4. Klik "Daftar Sekarang"

## Expected Result

✅ User berhasil dibuat
✅ Auto-login
✅ Redirect ke dashboard
✅ Pesan sukses: "Registrasi berhasil! Selamat datang di [Site Name]"

## Verifikasi Database

```sql
-- Cek user baru
SELECT * FROM users WHERE email = 'testuser@example.com';

-- Cek role user (harus "pengguna")
SELECT u.name, u.email, r.name as role_name
FROM users u
JOIN model_has_roles mhr ON u.id = mhr.model_id
JOIN roles r ON mhr.role_id = r.id
WHERE u.email = 'testuser@example.com';

-- Cek subscription user (harus free plan)
SELECT us.*, pp.name as plan_name, pp.price
FROM user_subscriptions us
JOIN pricing_plans pp ON us.pricing_plan_id = pp.id
JOIN users u ON us.user_id = u.id
WHERE u.email = 'testuser@example.com';
```

## Test Login

1. Logout dari dashboard
2. Akses: `http://127.0.0.1:8000/login`
3. Login dengan:
   - Email: testuser@example.com
   - Password: password123
4. Klik "Masuk"

✅ Berhasil login dan masuk ke dashboard

## Troubleshooting

### Error: Role "pengguna" not found
**Solusi**: Jalankan `php artisan db:seed --class=UserRoleSeeder`

### Error: Free plan not found
**Solusi**: Jalankan `php artisan db:seed --class=PricingPlanSeeder`

### Error: Logo tidak muncul
**Solusi**: Set logo di General Config (Dashboard → Konfigurasi Umum)

### Tab tidak berfungsi
**Solusi**: Clear cache browser atau hard refresh (Ctrl+F5)
