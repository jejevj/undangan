# Admin User Pricing Plan Management - Changelog

## Overview
Admin dapat mengelola paket pricing untuk setiap user, termasuk saat membuat user baru dan mengubah paket user yang sudah ada.

## Features Implemented

### 1. Assign Plan Saat Membuat User
- Admin dapat memilih paket pricing saat membuat user baru
- Dropdown menampilkan semua paket aktif
- Default ke paket Free jika tidak dipilih
- Subscription otomatis dibuat dengan status active

**File Modified:**
- `app/Http/Controllers/UserController.php` → method `store()`
- `resources/views/users/create.blade.php` → tambah dropdown pricing plan

### 2. Assign/Change Plan di User Detail
- Admin dapat mengubah paket user kapan saja
- Form di halaman detail user (show)
- Dapat set tanggal kadaluarsa (expires_at) atau unlimited
- Subscription lama otomatis di-expire
- Subscription baru dibuat dengan payment_method = 'admin_assign'

**File Modified:**
- `app/Http/Controllers/UserController.php` → method `assignPlan()`
- `resources/views/users/show.blade.php` → form assign plan

### 3. Reset Plan ke Free
- Tombol "Reset ke Free" untuk user dengan paket berbayar
- Subscription aktif di-expire
- Otomatis assign paket Free
- Payment method = 'admin_revoke'

**File Modified:**
- `app/Http/Controllers/UserController.php` → method `revokePlan()`
- `resources/views/users/show.blade.php` → tombol reset

### 4. Display Current Plan
- Halaman edit user menampilkan paket aktif saat ini
- Halaman detail user menampilkan:
  - Badge paket aktif
  - Tanggal mulai dan berakhir
  - Metode pembayaran
  - Progress bar usage undangan
  - Form untuk ubah paket

**File Modified:**
- `resources/views/users/edit.blade.php`
- `resources/views/users/show.blade.php`

## Routes Added
```php
Route::post('users/{user}/assign-plan', [UserController::class, 'assignPlan'])
    ->name('users.assign-plan')
    ->middleware('can:edit-users');

Route::post('users/{user}/revoke-plan', [UserController::class, 'revokePlan'])
    ->name('users.revoke-plan')
    ->middleware('can:edit-users');
```

## Database Changes
Tidak ada perubahan struktur database. Menggunakan tabel yang sudah ada:
- `user_subscriptions` → untuk menyimpan riwayat langganan
- `pricing_plans` → referensi paket

## Permissions Required
- `edit-users` → untuk assign dan revoke plan
- `view-users` → untuk melihat detail user dan paket aktif

## Usage Flow

### Create User dengan Paket
1. Admin buka halaman "Tambah User"
2. Isi form user (nama, email, password, role)
3. Pilih paket pricing dari dropdown (opsional, default Free)
4. Klik "Simpan"
5. User dibuat dengan subscription aktif sesuai paket yang dipilih

### Assign/Change Plan
1. Admin buka detail user
2. Di card "Paket Aktif", pilih paket baru dari dropdown
3. Set tanggal kadaluarsa (opsional, kosongkan untuk unlimited)
4. Klik "Assign Paket"
5. Subscription lama di-expire, subscription baru dibuat

### Reset ke Free
1. Admin buka detail user
2. Klik tombol "Reset ke Free" di card "Paket Aktif"
3. Konfirmasi
4. User kembali ke paket Free

## Testing Checklist
- [x] Create user dengan paket Free (default)
- [x] Create user dengan paket Basic
- [x] Create user dengan paket Pro
- [x] Assign paket Basic ke user Free
- [x] Assign paket Pro ke user Basic
- [x] Set expires_at saat assign
- [x] Reset user Pro ke Free
- [x] Lihat riwayat subscription di detail user
- [x] Verifikasi payment_method = 'admin_assign'
- [x] Verifikasi subscription lama status = 'expired'

## Notes
- Admin tidak bisa assign paket Business (visibility = 'business')
- Paket yang di-assign gratis (amount = 0)
- Subscription lama tidak dihapus, hanya status diubah ke 'expired'
- User admin tidak menampilkan card "Paket Aktif"
- Progress bar undangan menampilkan usage vs limit paket

## Files Modified
1. `app/Http/Controllers/UserController.php`
2. `resources/views/users/create.blade.php`
3. `resources/views/users/edit.blade.php`
4. `resources/views/users/show.blade.php`
5. `routes/web.php`

---
**Status:** ✅ Complete
**Date:** 28 March 2026
