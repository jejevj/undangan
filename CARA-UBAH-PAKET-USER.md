# 🎯 CARA UBAH PAKET USER - PANDUAN SINGKAT

## ⚠️ PENTING!

**Ubah Paket** hanya bisa di **Halaman DETAIL User**, BUKAN di halaman Edit!

```
❌ Halaman Edit   = Ubah nama, email, password, role
✅ Halaman Detail = Ubah paket pricing
```

---

## 📍 LOKASI FITUR UBAH PAKET

### Dari Tabel Manajemen User:

```
Dashboard → Manajemen User → Klik NAMA user atau icon MATA (👁️)
```

**JANGAN klik icon Pensil (✏️)** → itu halaman Edit, bukan Detail!

---

## 🔍 LANGKAH DEMI LANGKAH

### 1. Buka Halaman Detail User
- Login sebagai **Admin**
- Buka menu **"Manajemen User"** (sidebar kiri)
- Di tabel user, klik:
  - **Nama user** (kolom kedua), ATAU
  - **Icon mata (👁️)** di kolom Aksi

### 2. Cari Card "Paket Aktif"
- Setelah halaman detail terbuka
- Lihat **kolom kiri**
- Scroll ke bawah
- Cari card dengan judul **"Paket Aktif"**
- Card ini ada di bawah card "Statistik"

### 3. Ubah Paket
Di dalam card "Paket Aktif", ada form:
- **Dropdown "Ubah Paket"** → Pilih paket baru (Free/Basic/Pro)
- **Input "Berlaku Hingga"** → Set tanggal kadaluarsa (opsional)
- **Tombol "✓ Assign Paket"** → Klik untuk simpan

---

## 🎨 VISUAL GUIDE

### Tabel Manajemen User:
```
┌──────────────────────────────────────────────────────┐
│ Nama      │ Email         │ Paket  │ Aksi           │
├──────────────────────────────────────────────────────┤
│ John Doe  │ john@mail.com │ Basic  │ 👁️ ✏️ 🗑️       │
│           │               │        │ ↑  ↑  ↑        │
│           │               │        │ │  │  └─ Hapus │
│           │               │        │ │  └─ Edit     │
│           │               │        │ └─ DETAIL ✅   │
└──────────────────────────────────────────────────────┘
         ↑ Klik nama atau icon mata untuk DETAIL
```

### Halaman Detail User:
```
┌─────────────────────────────────────────────────────┐
│ DETAIL USER: John Doe                               │
├──────────────────┬──────────────────────────────────┤
│ KOLOM KIRI       │ KOLOM KANAN                      │
├──────────────────┼──────────────────────────────────┤
│ 📋 Profil        │ 📝 Daftar Undangan               │
│ - Avatar         │                                  │
│ - Nama           │                                  │
│ - Email          │                                  │
│                  │                                  │
├──────────────────┤                                  │
│ 📊 Statistik     │                                  │
│ - Total: 5       │                                  │
│ - Published: 3   │                                  │
│                  │                                  │
├──────────────────┤                                  │
│ 💎 PAKET AKTIF   │ ← INI YANG ANDA CARI!            │
│ ┌──────────────┐ │                                  │
│ │ Badge: Basic │ │                                  │
│ │ Sejak: 28 Mar│ │                                  │
│ ├──────────────┤ │                                  │
│ │ Progress Bar │ │                                  │
│ ├──────────────┤ │                                  │
│ │ FORM:        │ │                                  │
│ │ [Dropdown]   │ │                                  │
│ │ [Tanggal]    │ │                                  │
│ │ [✓ Assign]   │ │ ← FORM UBAH PAKET ADA DI SINI!  │
│ └──────────────┘ │                                  │
└──────────────────┴──────────────────────────────────┘
```

---

## 🚨 TROUBLESHOOTING

### "Saya tidak lihat card Paket Aktif"
**Penyebab:**
- User yang dibuka adalah **Admin** → Admin tidak punya paket
- Anda di halaman **Edit**, bukan **Detail**

**Solusi:**
- Pastikan buka user dengan role **"pengguna"**
- Pastikan di halaman **Detail** (URL: `/dash/users/{id}`)
- Bukan di halaman Edit (URL: `/dash/users/{id}/edit`)

### "Saya di halaman Edit, tidak ada form ubah paket"
**Jawaban:**
- Benar! Halaman Edit memang tidak ada form ubah paket
- Klik tombol **"Detail"** atau **"Buka Halaman Detail"**
- Atau kembali ke tabel user, klik nama user/icon mata

### "Dropdown paket kosong"
**Penyebab:**
- Belum ada pricing plan aktif di database

**Solusi:**
- Buka menu **"Manajemen Paket Pricing"**
- Pastikan ada paket Free, Basic, Pro
- Pastikan status paket = Aktif

---

## 📝 CONTOH KASUS

### Kasus 1: Upgrade User dari Free ke Basic
```
1. Dashboard → Manajemen User
2. Klik nama user "John Doe" (yang paketnya Free)
3. Scroll ke card "Paket Aktif"
4. Di dropdown "Ubah Paket", pilih "Basic"
5. Set "Berlaku Hingga": 2026-12-31 (opsional)
6. Klik "✓ Assign Paket"
7. ✅ Selesai! John Doe sekarang paket Basic
```

### Kasus 2: Reset User ke Free
```
1. Dashboard → Manajemen User
2. Klik nama user "Jane Doe" (yang paketnya Pro)
3. Scroll ke card "Paket Aktif"
4. Klik tombol "Reset ke Free" (pojok kanan atas card)
5. Konfirmasi
6. ✅ Selesai! Jane Doe kembali ke paket Free
```

---

## 🎯 RINGKASAN CEPAT

| Halaman | URL | Fungsi | Ubah Paket? |
|---------|-----|--------|-------------|
| **Index** | `/dash/users` | Lihat daftar user | ❌ |
| **Create** | `/dash/users/create` | Tambah user baru | ✅ (saat create) |
| **Edit** | `/dash/users/{id}/edit` | Edit nama, email, role | ❌ |
| **Detail** | `/dash/users/{id}` | Lihat detail & ubah paket | ✅ **INI!** |

---

## 💡 TIPS

1. **Bookmark halaman detail user** yang sering Anda kelola
2. **Gunakan tombol "Detail"** di halaman Edit untuk cepat ke halaman Detail
3. **Lihat riwayat subscription** di halaman Detail untuk tracking perubahan paket
4. **Set tanggal kadaluarsa** jika ingin paket otomatis expire

---

**Masih bingung?**  
Screenshot halaman Anda dan tanyakan lagi! 😊

---

**Dibuat:** 28 Maret 2026  
**Versi:** 2.0 (Diperjelas)
