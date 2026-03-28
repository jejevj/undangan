# 📋 Ringkasan: Cara Ubah Paket User

## ✅ Lokasi Form Ubah Paket

**Halaman:** Edit User  
**URL:** `http://127.0.0.1:8000/dash/users/{id}/edit`

## 🎯 Cara Akses

1. Login sebagai **Admin**
2. Buka menu **Manajemen User**
3. Klik **icon Pensil (✏️)** di kolom Aksi
4. Scroll ke bawah

## 📦 Struktur Halaman Edit User

```
┌─────────────────────────────────────────┐
│ Card 1: Form Edit User                  │
│ - Nama                                  │
│ - Email                                 │
│ - Password Baru                         │
│ - Role (checkbox)                       │
│ - Tombol: Update | Detail | Batal      │
└─────────────────────────────────────────┘

┌─────────────────────────────────────────┐
│ Card 2: Paket Pricing                   │ ← INI YANG ANDA CARI!
│                                         │
│ Paket Aktif: [Badge Free]              │
│ Sejak: 28 Mar 2026 · Selamanya         │
│                                         │
│ ─────────────────────────────────────  │
│                                         │
│ Form Ubah Paket:                        │
│ ┌─────────────────────────────────────┐ │
│ │ Ubah Paket                          │ │
│ │ [Dropdown: Free/Basic/Pro]          │ │
│ │                                     │ │
│ │ Berlaku Hingga (opsional)           │ │
│ │ [Input Date]                        │ │
│ │                                     │ │
│ │ [Tombol: ✓ Assign Paket]           │ │
│ └─────────────────────────────────────┘ │
│                                         │
│ [Tombol: Reset ke Free] (jika bukan Free)│
└─────────────────────────────────────────┘
```

## 🔧 Fitur di Card "Paket Pricing"

1. **Info Paket Aktif** (bagian atas)
   - Badge paket saat ini
   - Tanggal mulai dan berakhir

2. **Form Ubah Paket** (bagian tengah)
   - Dropdown pilih paket baru
   - Input tanggal kadaluarsa (opsional)
   - Tombol "Assign Paket"

3. **Tombol Reset ke Free** (header kanan)
   - Hanya muncul jika paket bukan Free
   - Tidak muncul untuk user Admin

## 📝 Langkah Mengubah Paket

### Scenario 1: Upgrade Free ke Basic
```
1. Buka: http://127.0.0.1:8000/dash/users/3/edit
2. Scroll ke card "Paket Pricing"
3. Di dropdown "Ubah Paket", pilih: Basic
4. Di "Berlaku Hingga", set: 2026-12-31 (atau kosongkan)
5. Klik tombol "✓ Assign Paket"
6. ✅ Halaman refresh, paket berubah ke Basic
```

### Scenario 2: Reset ke Free
```
1. Buka: http://127.0.0.1:8000/dash/users/3/edit
2. Scroll ke card "Paket Pricing"
3. Klik tombol "Reset ke Free" (di header kanan)
4. Konfirmasi
5. ✅ Halaman refresh, paket kembali ke Free
```

## 🚨 Troubleshooting

### "Saya tidak lihat card Paket Pricing"
**Solusi:**
- Refresh dengan Ctrl+F5 (hard refresh)
- Atau jalankan: `php artisan view:clear`

### "Dropdown paket kosong"
**Solusi:**
- Jalankan seeder: `php artisan db:seed --class=PricingPlanSeeder`

### "Tombol Assign Paket tidak berfungsi"
**Solusi:**
- Cek console browser (F12) untuk error
- Pastikan sudah pilih paket dari dropdown

## ✅ Checklist

Setelah refresh halaman edit user, Anda harus melihat:
- [ ] Card "Form Edit User" (nama, email, password, role)
- [ ] Card "Paket Pricing" dengan dropdown paket
- [ ] Dropdown berisi: Free, Basic, Pro
- [ ] Input tanggal "Berlaku Hingga"
- [ ] Tombol hijau "✓ Assign Paket"
- [ ] Tombol "Reset ke Free" (jika paket bukan Free)

## 📸 Screenshot Lokasi

```
URL: http://127.0.0.1:8000/dash/users/3/edit

┌─────────────────────────────────────────────────┐
│ EDIT USER: [Nama User]                          │
├─────────────────────────────────────────────────┤
│                                                 │
│ [Card: Form Edit User]                          │
│ - Nama, Email, Password, Role                   │
│ - Tombol: Update | Detail | Batal              │
│                                                 │
├─────────────────────────────────────────────────┤
│                                                 │
│ [Card: Paket Pricing] ← SCROLL KE SINI!        │
│                                                 │
│ Paket Aktif: Free                               │
│ Sejak: 28 Mar 2026                              │
│                                                 │
│ ───────────────────────────────────────────     │
│                                                 │
│ Ubah Paket:                                     │
│ [▼ Free (Gratis)        ]                       │
│ [  Basic (Rp 50.000)    ]                       │
│ [  Pro (Rp 100.000)     ]                       │
│                                                 │
│ Berlaku Hingga:                                 │
│ [📅 ____-__-__          ]                       │
│                                                 │
│ [✓ Assign Paket]                                │
│                                                 │
└─────────────────────────────────────────────────┘
```

## 🎉 Kesimpulan

Form ubah paket sekarang ada di halaman **Edit User**, sama seperti saat tambah user baru. Tidak perlu ke halaman Detail lagi!

---
**Dibuat:** 28 Maret 2026  
**Status:** ✅ Final & Tested
