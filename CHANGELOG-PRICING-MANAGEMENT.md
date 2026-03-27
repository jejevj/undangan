# Changelog - Manajemen Pricing Plans

## Tanggal: 28 Maret 2026

### Fitur Baru: Manajemen Pricing Plans untuk Admin

Admin sekarang dapat mengelola paket pricing/langganan melalui panel admin.

---

## Perubahan yang Dilakukan

### 1. Controller Baru: PricingPlanController
- **File**: `app/Http/Controllers/PricingPlanController.php`
- **Fitur**:
  - `index()` - Menampilkan daftar semua paket pricing
  - `create()` - Form tambah paket baru
  - `store()` - Simpan paket baru
  - `edit()` - Form edit paket
  - `update()` - Update paket
  - `destroy()` - Hapus paket (dengan validasi subscription aktif)
  - `toggle()` - Aktifkan/nonaktifkan paket

### 2. View Manajemen Pricing
- **File**: `resources/views/pricing-plans/index.blade.php`
  - Tabel daftar paket dengan informasi lengkap
  - Tombol aksi: Edit, Toggle, Hapus
  - Badge status aktif/nonaktif
  - Counter jumlah subscribers per paket

- **File**: `resources/views/pricing-plans/create.blade.php`
  - Form lengkap untuk membuat paket baru
  - Input: nama, slug, harga, badge color
  - Limit: max undangan, foto, musik
  - Checkbox fitur: gift section, delete music, popular, active
  - Dynamic features list dengan JavaScript

- **File**: `resources/views/pricing-plans/edit.blade.php`
  - Form edit dengan data existing
  - Sama seperti create form tapi pre-filled

### 3. Permission Baru
Ditambahkan 4 permission baru untuk pricing plans:
- `view-pricing-plans` - Melihat daftar paket
- `create-pricing-plans` - Membuat paket baru
- `edit-pricing-plans` - Edit dan toggle paket
- `delete-pricing-plans` - Hapus paket

### 4. Menu Baru di Sidebar
- **Lokasi**: Pengaturan > Manajemen Pricing
- **URL**: `/pricing-plans`
- **Permission**: `view-pricing-plans`
- **Order**: 3 (setelah Manajemen Musik)

### 5. Route Baru
```php
Route::resource('pricing-plans', PricingPlanController::class)
    ->middleware(['can:view-pricing-plans', ...]);
Route::patch('pricing-plans/{pricingPlan}/toggle', ...)
    ->middleware('can:edit-pricing-plans');
```

### 6. Update DatabaseSeeder
- Menambahkan permission pricing plans ke array permissions
- Menambahkan menu "Manajemen Pricing" ke sub-menu Pengaturan
- Admin role otomatis mendapat semua permission pricing plans

### 7. Update Knowledge.md
- Dokumentasi model PricingPlan
- Dokumentasi model UserSubscription
- Daftar permission pricing plans
- Route dan method yang tersedia

---

## Fitur Manajemen Pricing

### Daftar Paket
Menampilkan semua paket dengan informasi:
- Nama paket dan badge popular
- Slug (identifier)
- Harga (formatted)
- Limit: undangan, foto, musik
- Fitur gift section
- Jumlah subscribers
- Status aktif/nonaktif
- Tombol aksi

### Tambah/Edit Paket
Form lengkap dengan field:
- **Informasi Dasar**: Nama, slug, harga, warna badge
- **Limit**: Max undangan, foto gallery, upload musik
- **Fitur Tambahan**:
  - Gift section included (checkbox)
  - Can delete music (checkbox)
  - Is popular (checkbox)
  - Is active (checkbox)
- **Daftar Fitur**: Dynamic list untuk menambah/hapus fitur display

### Toggle Aktif/Nonaktif
- Nonaktifkan paket tanpa menghapus dari database
- Paket nonaktif tidak muncul di halaman subscription user
- Subscription aktif tetap berjalan meskipun paket dinonaktifkan

### Hapus Paket
- Validasi: tidak bisa hapus jika ada subscription aktif
- Mencegah data inconsistency
- Error message jika ada subscription aktif

---

## Validasi & Keamanan

### Validasi Form
- Nama paket: required, max 255 karakter
- Slug: unique, auto-generate jika kosong
- Harga: required, integer, min 0
- Max invitations: required, integer, min 1
- Max gallery photos: required, integer, min 0
- Max music uploads: required, integer, min 0
- Features: array of strings

### Proteksi Permission
Semua route dilindungi dengan middleware `can:`:
- Index: `can:view-pricing-plans`
- Create/Store: `can:create-pricing-plans`
- Edit/Update: `can:edit-pricing-plans`
- Destroy: `can:delete-pricing-plans`
- Toggle: `can:edit-pricing-plans`

### Proteksi Hapus
Tidak bisa menghapus paket yang memiliki subscription aktif:
```php
if ($pricingPlan->subscriptions()->where('status', 'active')->exists()) {
    return redirect()->back()->with('error', 'Tidak dapat menghapus...');
}
```

---

## Cara Menggunakan

### 1. Jalankan Seeder
```bash
php artisan db:seed --class=DatabaseSeeder
```

Ini akan:
- Membuat permission pricing plans
- Menambahkan menu "Manajemen Pricing"
- Assign permission ke role admin

### 2. Akses Menu
- Login sebagai admin
- Buka menu "Pengaturan > Manajemen Pricing"
- Atau akses langsung: `/pricing-plans`

### 3. Kelola Paket
- **Tambah**: Klik tombol "Tambah Paket Baru"
- **Edit**: Klik icon pensil pada paket
- **Toggle**: Klik icon mata untuk aktif/nonaktif
- **Hapus**: Klik icon trash (hanya jika tidak ada subscriber aktif)

---

## Testing Checklist

- [x] Route pricing-plans terdaftar
- [x] Permission pricing-plans terbuat
- [x] Menu "Manajemen Pricing" muncul di sidebar admin
- [x] Controller tidak ada syntax error
- [x] View blade compiled successfully
- [ ] Bisa akses halaman index pricing plans
- [ ] Bisa tambah paket baru
- [ ] Bisa edit paket existing
- [ ] Bisa toggle aktif/nonaktif
- [ ] Tidak bisa hapus paket dengan subscription aktif
- [ ] Bisa hapus paket tanpa subscription aktif
- [ ] Menu tidak muncul untuk user non-admin

---

## Struktur File Baru

```
app/Http/Controllers/
└── PricingPlanController.php

resources/views/pricing-plans/
├── index.blade.php
├── create.blade.php
└── edit.blade.php
```

---

## Permission Matrix

| Role | View | Create | Edit | Delete |
|---|---|---|---|---|
| Admin | ✓ | ✓ | ✓ | ✓ |
| Staff | ✗ | ✗ | ✗ | ✗ |
| Pengguna | ✗ | ✗ | ✗ | ✗ |

Hanya admin yang dapat mengelola pricing plans.

---

## Catatan Penting

1. **Slug Auto-Generate**: Jika slug kosong, otomatis di-generate dari nama paket
2. **Harga 0 = Gratis**: Paket dengan harga 0 akan ditampilkan sebagai "Gratis"
3. **Features Array**: Disimpan sebagai JSON array, bisa kosong
4. **Subscription Aktif**: Paket tidak bisa dihapus jika ada user yang masih subscribe
5. **Toggle vs Delete**: Gunakan toggle untuk menyembunyikan paket sementara, delete untuk permanen

---

## Integrasi dengan Fitur Lain

### User Model
User memiliki method untuk cek limit berdasarkan active plan:
- `activePlan()` - Get paket aktif user
- `canCreateInvitation()` - Cek apakah masih bisa buat undangan
- `remainingInvitations()` - Hitung sisa kuota undangan

### Subscription Flow
1. User pilih paket di `/subscription`
2. Checkout dan simulasi pembayaran
3. Subscription aktif, paket lama expired
4. User dapat fitur sesuai paket baru

### Admin Assign Plan
Admin bisa assign paket ke user tanpa pembayaran:
- Via halaman detail user
- Bypass payment flow
- Langsung aktif dengan `payment_method: 'admin_assign'`
