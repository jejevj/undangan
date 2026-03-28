# Test Ubah Paket User

## Situasi Saat Ini
- Login sebagai: **Admin**
- Target user: **User ID 3** (pengguna biasa, paket Free)
- URL: `http://127.0.0.1:8000/dash/users/3`

## Yang Sudah Dilakukan
1. ✅ Card "Paket Aktif" sekarang muncul untuk SEMUA user (termasuk admin dan pengguna biasa)
2. ✅ Form "Ubah Paket" ada di dalam card tersebut
3. ✅ Routes `users.assign-plan` sudah terdaftar
4. ✅ Method `UserController::assignPlan()` sudah ada
5. ✅ Method `UserController::show()` mengirim data `$plans`, `$activePlan`, `$activeSub`

## Struktur Halaman Detail User

### Kolom Kiri (col-lg-4):
1. Card "Profil" - Avatar, nama, email, role, tombol Edit
2. Card "Statistik" - Total undangan, published, draft, tanggal bergabung
3. **Card "Paket Aktif"** ← INI YANG ANDA CARI!
   - Header: "Paket Aktif" + badge "Admin" (jika admin)
   - Tombol "Reset ke Free" (jika bukan Free dan bukan admin)
   - Body:
     - Alert info (jika admin)
     - Badge paket aktif (Free/Basic/Pro)
     - Info tanggal mulai/berakhir
     - Progress bar usage (jika bukan admin)
     - **FORM UBAH PAKET:**
       - Dropdown "Ubah Paket"
       - Input "Berlaku Hingga"
       - Tombol "✓ Assign Paket"

### Kolom Kanan (col-lg-8):
1. Card "Undangan" - Tabel daftar undangan user
2. Card "Riwayat Langganan" - Tabel riwayat subscription

## Cara Mengubah Paket

### Langkah-langkah:
1. Buka `http://127.0.0.1:8000/dash/users/3`
2. Scroll ke bawah di **kolom kiri**
3. Cari card dengan header **"Paket Aktif"**
4. Di dalam card, ada form dengan:
   - **Dropdown "Ubah Paket"** → Pilih Basic atau Pro
   - **Input "Berlaku Hingga"** → Set tanggal atau kosongkan
   - **Tombol "✓ Assign Paket"** → Klik untuk simpan
5. Setelah klik, halaman akan refresh dan paket berubah

## Troubleshooting

### Jika Card "Paket Aktif" Tidak Muncul:
**Kemungkinan 1:** File view belum ter-update
- Solusi: Refresh browser dengan Ctrl+F5 (hard refresh)
- Atau clear cache: `php artisan view:clear`

**Kemungkinan 2:** Ada error di view
- Solusi: Cek error log di browser console (F12)
- Atau cek Laravel log: `storage/logs/laravel.log`

### Jika Dropdown Paket Kosong:
**Kemungkinan:** Tidak ada pricing plan aktif di database
- Solusi: Jalankan seeder:
  ```bash
  php artisan db:seed --class=PricingPlanSeeder
  ```

### Jika Form Tidak Berfungsi:
**Kemungkinan:** CSRF token expired atau route tidak ditemukan
- Solusi: Refresh halaman
- Cek route: `php artisan route:list | grep assign-plan`

## Verifikasi

### Cek Route:
```bash
php artisan route:list | grep "users.*assign"
```

Harus muncul:
```
POST  dash/users/{user}/assign-plan  users.assign-plan
POST  dash/users/{user}/revoke-plan  users.revoke-plan
```

### Cek Pricing Plans:
```bash
php artisan tinker
```
Lalu ketik:
```php
\App\Models\PricingPlan::where('is_active', true)->get(['id', 'name', 'slug']);
```

Harus muncul minimal 3 paket: Free, Basic, Pro

### Cek User:
```bash
php artisan tinker
```
Lalu ketik:
```php
$user = \App\Models\User::find(3);
echo $user->name . ' - Role: ' . $user->roles->pluck('name')->join(', ');
echo ' - Paket: ' . $user->activePlan()->name;
```

## Screenshot Lokasi

```
┌─────────────────────────────────────────────────────────┐
│ DETAIL USER: [Nama User]                               │
├──────────────────┬──────────────────────────────────────┤
│ KOLOM KIRI       │ KOLOM KANAN                          │
│                  │                                      │
│ [Card Profil]    │ [Card Undangan]                      │
│                  │                                      │
│ [Card Statistik] │                                      │
│                  │                                      │
│ ┌──────────────┐ │ [Card Riwayat Langganan]             │
│ │ PAKET AKTIF  │ │                                      │
│ │              │ │                                      │
│ │ Badge: Free  │ │                                      │
│ │ Progress Bar │ │                                      │
│ │              │ │                                      │
│ │ FORM:        │ │                                      │
│ │ [Dropdown]   │ │ ← FORM UBAH PAKET ADA DI SINI!      │
│ │ [Tanggal]    │ │                                      │
│ │ [✓ Assign]   │ │                                      │
│ └──────────────┘ │                                      │
└──────────────────┴──────────────────────────────────────┘
```

## Next Steps

1. **Refresh halaman** `http://127.0.0.1:8000/dash/users/3` dengan Ctrl+F5
2. **Scroll ke bawah** di kolom kiri
3. **Cari card "Paket Aktif"**
4. **Gunakan form** di dalam card tersebut

Jika masih tidak muncul, screenshot halaman Anda dan kirim ke saya!

---
**Dibuat:** 28 Maret 2026
