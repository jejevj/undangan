# 📖 Panduan Mengubah Paket Pricing User

## ⚠️ PENTING: Ubah Paket HANYA di Halaman DETAIL User!

**Halaman Edit User** = Edit nama, email, password, role saja  
**Halaman Detail User** = Ubah paket pricing ✅

---

## 🎯 Ada 3 Cara Mengubah Paket User:

---

## 1️⃣ SAAT MEMBUAT USER BARU

### Langkah-langkah:
1. Login sebagai **Admin**
2. Buka menu **Manajemen User** (di sidebar)
3. Klik tombol **"+ Tambah User"** (pojok kiri atas)
4. Isi form:
   - Nama
   - Email
   - Password
   - Konfirmasi Password
   - Role (centang checkbox)
   - **Paket Pricing** ← dropdown ini untuk pilih paket
5. Klik **"Simpan"**

### Screenshot Lokasi:
```
Dashboard → Sidebar → Manajemen User → Tambah User
```

### Field "Paket Pricing":
- Dropdown dengan pilihan: Free, Basic, Pro
- Jika tidak dipilih = otomatis Free
- Menampilkan nama paket dan harga

---

## 2️⃣ UBAH PAKET DI HALAMAN DETAIL USER ⭐ (INI YANG ANDA CARI!)

### ⚠️ BUKAN di halaman Edit! Tapi di halaman Detail/Show!

### Langkah-langkah:
1. Login sebagai **Admin**
2. Buka menu **Manajemen User**
3. Klik **icon mata (👁️)** atau **nama user** di tabel
4. Scroll ke bagian **"Paket Aktif"** (card di kolom kiri)
5. Di form "Ubah Paket":
   - Pilih paket baru dari dropdown
   - Set tanggal kadaluarsa (opsional, kosongkan = selamanya)
6. Klik tombol **"✓ Assign Paket"**

### Screenshot Lokasi:
```
Dashboard → Manajemen User → [Klik icon mata/nama user]
→ Card "Paket Aktif" (kolom kiri bawah)
```

### Fitur di Halaman Detail:
- **Badge paket aktif** (warna sesuai paket)
- **Tanggal mulai & berakhir**
- **Progress bar** usage undangan
- **Form ubah paket** dengan dropdown
- **Tombol "Reset ke Free"** (untuk user berbayar)

---

## 3️⃣ RESET PAKET KE FREE

### Langkah-langkah:
1. Login sebagai **Admin**
2. Buka menu **Manajemen User**
3. Klik **icon mata (👁️)** atau **nama user**
4. Di card **"Paket Aktif"**, klik tombol **"Reset ke Free"** (pojok kanan atas card)
5. Konfirmasi

### Kapan Digunakan:
- User tidak membayar lagi
- Trial period habis
- Downgrade manual oleh admin

---

## 📍 NAVIGASI CEPAT

### Dari Dashboard:
```
Sidebar → Manajemen User
```

### Dari Tabel User:
- **Icon Mata (👁️)** = Detail user (bisa ubah paket)
- **Icon Pensil (✏️)** = Edit user (lihat paket aktif)
- **Icon Trash (🗑️)** = Hapus user

---

## 🎨 VISUAL GUIDE

### Tabel Manajemen User:
```
┌─────────────────────────────────────────────────────────────┐
│ # │ Nama      │ Email         │ Role     │ Paket  │ Aksi   │
├─────────────────────────────────────────────────────────────┤
│ 1 │ John Doe  │ john@mail.com │ pengguna │ Basic  │ 👁️ ✏️ 🗑️ │
│ 2 │ Jane Doe  │ jane@mail.com │ pengguna │ Free   │ 👁️ ✏️ 🗑️ │
└─────────────────────────────────────────────────────────────┘
         ↑ Klik nama atau icon mata untuk detail
```

### Halaman Detail User (Show):
```
┌─────────────────────┐  ┌──────────────────────────────────┐
│   KOLOM KIRI        │  │   KOLOM KANAN                    │
├─────────────────────┤  ├──────────────────────────────────┤
│ 📋 Profil           │  │ 📝 Daftar Undangan               │
│ - Avatar            │  │ - Tabel undangan user            │
│ - Nama              │  │                                  │
│ - Email             │  ├──────────────────────────────────┤
│ - Badge role        │  │ 📜 Riwayat Langganan             │
│                     │  │ - Tabel subscription history     │
├─────────────────────┤  └──────────────────────────────────┘
│ 📊 Statistik        │
│ - Total undangan    │
│ - Published         │
│ - Draft             │
│                     │
├─────────────────────┤
│ 💎 PAKET AKTIF      │  ← INI YANG ANDA CARI!
│ ┌─────────────────┐ │
│ │ Badge: Basic    │ │
│ │ Sejak: 28 Mar   │ │
│ │ via: Admin      │ │
│ ├─────────────────┤ │
│ │ Progress Bar    │ │
│ │ Undangan: 2/10  │ │
│ ├─────────────────┤ │
│ │ FORM UBAH PAKET │ │
│ │ [Dropdown]      │ │
│ │ [Tanggal]       │ │
│ │ [Assign Paket]  │ │
│ └─────────────────┘ │
└─────────────────────┘
```

---

## ⚙️ DETAIL FORM "UBAH PAKET"

### Field 1: Dropdown Paket
```
┌─────────────────────────────────────┐
│ Ubah Paket                          │
│ ┌─────────────────────────────────┐ │
│ │ Free (Gratis)                   ▼│ │
│ │ Basic (Rp 50.000)                │ │
│ │ Pro (Rp 100.000)                 │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

### Field 2: Tanggal Kadaluarsa (Opsional)
```
┌─────────────────────────────────────┐
│ Berlaku Hingga (opsional)           │
│ ┌─────────────────────────────────┐ │
│ │ [📅 2026-04-28]                  │ │
│ └─────────────────────────────────┘ │
│ Kosongkan = selamanya               │
└─────────────────────────────────────┘
```

### Tombol Submit:
```
┌─────────────────────────────────────┐
│ [✓ Assign Paket]                    │
└─────────────────────────────────────┘
```

---

## 🔍 TIPS MENCARI FITUR INI

### Jika Tidak Ketemu:
1. ✅ Pastikan login sebagai **Admin** (bukan user biasa)
2. ✅ Buka menu **"Manajemen User"** di sidebar kiri
3. ✅ Klik **nama user** atau **icon mata** di tabel
4. ✅ Scroll ke bawah, cari card **"Paket Aktif"**
5. ✅ Form ada di dalam card tersebut

### Jika Card "Paket Aktif" Tidak Muncul:
- User yang dibuka adalah **Admin** → Admin tidak punya paket
- Coba buka user dengan role **"pengguna"**

---

## 📝 CONTOH PENGGUNAAN

### Scenario 1: User Baru Langsung Pro
```
1. Tambah User
2. Isi nama: "Premium User"
3. Isi email: "premium@mail.com"
4. Pilih paket: "Pro (Rp 100.000)"
5. Simpan
✅ User dibuat dengan paket Pro aktif
```

### Scenario 2: Upgrade Free ke Basic
```
1. Buka detail user (yang paketnya Free)
2. Di card "Paket Aktif", pilih "Basic"
3. Set expires_at: 2026-12-31
4. Klik "Assign Paket"
✅ User upgrade ke Basic hingga 31 Des 2026
```

### Scenario 3: Reset ke Free
```
1. Buka detail user (yang paketnya Pro)
2. Klik tombol "Reset ke Free"
3. Konfirmasi
✅ User kembali ke paket Free
```

---

## 🚨 TROUBLESHOOTING

### "Saya tidak lihat menu Manajemen User"
→ Anda bukan Admin. Minta akses dari super admin.

### "Card Paket Aktif tidak ada"
→ User yang dibuka adalah Admin. Coba user lain.

### "Dropdown paket kosong"
→ Belum ada pricing plan aktif. Buat di menu "Manajemen Paket Pricing".

### "Tombol Assign Paket tidak berfungsi"
→ Cek console browser (F12) untuk error. Pastikan form valid.

---

## 📞 BUTUH BANTUAN?

Jika masih bingung, screenshot halaman Anda dan tanyakan:
- "Saya di halaman mana sekarang?"
- "Apa yang ingin saya lakukan?"

---

**Dibuat:** 28 Maret 2026  
**Versi:** 1.0
