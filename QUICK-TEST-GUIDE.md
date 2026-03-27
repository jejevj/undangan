# Quick Test Guide - 28 Maret 2026

Panduan cepat untuk testing semua fitur yang baru diupdate.

---

## 🚀 Quick Setup

```bash
# 1. Run seeder
php artisan db:seed --class=DatabaseSeeder

# 2. Clear cache
php artisan cache:clear && php artisan view:clear && php artisan route:clear

# 3. Verify routes
php artisan route:list --path=pricing-plans
php artisan route:list --path=music
```

---

## ✅ Quick Test Scenarios

### Test 1: Role & Permission (2 menit)

**Login sebagai Staff:**
```
Email: staff@test.com (buat dulu jika belum ada)
Role: staff
```

1. Buka `/invitations`
2. ✅ Tombol hapus (trash) HARUS muncul

**Login sebagai Pengguna:**
```
Email: user@test.com (buat dulu jika belum ada)
Role: pengguna
```

1. Buka `/invitations`
2. ✅ Tombol hapus (trash) TIDAK boleh muncul

---

### Test 2: Music Privacy (3 menit)

**Login sebagai User A:**
```
Email: usera@test.com
```

1. Buka `/music`
2. Klik "Upload Lagu Saya"
3. Lihat alert biaya Rp 5.000
4. Upload file MP3 (title: "Lagu A")
5. Redirect ke checkout
6. Klik "Bayar Sekarang"
7. ✅ Lagu muncul dengan badge "Upload Saya"

**Login sebagai User B:**
```
Email: userb@test.com
```

1. Buka `/music`
2. ✅ "Lagu A" TIDAK boleh muncul
3. ✅ Hanya musik sistem yang muncul

---

### Test 3: Pricing Management (3 menit)

**Login sebagai Admin:**
```
Email: admin@undangan.test
Password: password
```

1. Sidebar → Pengaturan → Manajemen Pricing
2. ✅ Halaman pricing plans muncul
3. Klik "Tambah Paket Baru"
4. Isi form:
   - Nama: "Test Plan"
   - Harga: 100000
   - Max Undangan: 5
5. Submit
6. ✅ Paket tersimpan dan muncul di list

**Login sebagai User Biasa:**
```
Email: user@test.com
```

1. Cek sidebar
2. ✅ Menu "Manajemen Pricing" TIDAK muncul

---

## 🎯 Critical Checks

### Permission Check
```bash
php artisan tinker
>>> auth()->user()->can('delete-invitations')
# Staff/Admin: true
# Pengguna: false
```

### Music Access Check
```bash
php artisan tinker
>>> Music::accessibleByUser(User::find(1))->count()
# Should return: gratis + dibeli + upload sendiri
```

### Pricing Permission Check
```bash
php artisan tinker
>>> auth()->user()->can('view-pricing-plans')
# Admin: true
# Others: false
```

---

## 🐛 Common Issues

### Issue 1: Menu tidak muncul
```bash
php artisan cache:clear
# Logout dan login ulang
```

### Issue 2: Tombol hapus masih muncul
```bash
# Clear view cache
php artisan view:clear

# Verify permission
php artisan tinker
>>> User::find(1)->roles->pluck('name')
>>> User::find(1)->getAllPermissions()->pluck('name')
```

### Issue 3: Musik user lain masih muncul
```bash
# Verify controller
cat app/Http/Controllers/MusicController.php | grep accessibleByUser

# Should use: Music::accessibleByUser(auth()->user())
```

---

## ✅ Success Criteria

### Role & Permission
- [x] Staff bisa hapus undangan
- [x] Pengguna tidak bisa hapus undangan
- [x] User baru otomatis dapat role "pengguna"

### Music Privacy
- [x] Musik upload user hanya muncul untuk pemilik
- [x] Badge "Upload Saya" muncul
- [x] Musik sistem muncul untuk semua

### Pricing Management
- [x] Admin bisa akses menu pricing
- [x] Admin bisa CRUD paket
- [x] Non-admin tidak bisa akses
- [x] Permission terdaftar dengan benar

---

## 📊 Quick Verification

### Database Check
```sql
-- Check roles
SELECT * FROM roles;
-- Expected: admin, staff, pengguna

-- Check permissions
SELECT * FROM permissions WHERE name LIKE '%pricing%';
-- Expected: 4 rows

-- Check menu
SELECT * FROM menus WHERE slug = 'pricing-plans';
-- Expected: 1 row
```

### File Check
```bash
# Check new files exist
ls app/Http/Controllers/PricingPlanController.php
ls resources/views/pricing-plans/
ls database/seeders/UserRoleSeeder.php

# Check modified files
git diff app/Http/Controllers/MusicController.php
git diff app/Http/Controllers/InvitationController.php
```

---

## 🎉 All Tests Passed?

If all quick tests passed:
1. ✅ Role & permission working
2. ✅ Music privacy working
3. ✅ Pricing management working

**Next**: Proceed with detailed manual testing using:
- `TEST-RESULTS.md`
- `MUSIC-PRIVACY-TEST.md`
- `PRICING-TEST-RESULTS.md`

---

## 📝 Test Results Template

```
Date: ___________
Tester: ___________

[ ] Test 1: Role & Permission - PASS / FAIL
[ ] Test 2: Music Privacy - PASS / FAIL
[ ] Test 3: Pricing Management - PASS / FAIL

Issues Found:
1. ___________
2. ___________

Notes:
___________
```

---

**Estimated Time**: 8-10 minutes
**Difficulty**: Easy
**Prerequisites**: Database seeded, cache cleared
