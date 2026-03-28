# ✅ FINAL - Cara Ubah Paket User

## Status: SELESAI & TESTED

Fitur ubah paket user sudah selesai dan sederhana seperti yang diminta.

---

## 📍 Lokasi Fitur

**Halaman:** Edit User  
**URL:** `http://127.0.0.1:8000/dash/users/{id}/edit`

---

## 🎯 Cara Menggunakan

### Langkah 1: Buka Halaman Edit User
1. Login sebagai Admin
2. Buka menu **Manajemen User**
3. Klik **icon Pensil (✏️)** di kolom Aksi pada user yang ingin diubah
4. Atau URL langsung: `http://127.0.0.1:8000/dash/users/3/edit`

### Langkah 2: Ubah Paket
1. Scroll ke bawah ke card **"Paket Pricing"**
2. Lihat info paket aktif saat ini (badge warna)
3. Pilih paket baru dari dropdown:
   - Free - Gratis
   - Basic - Rp 49.000
   - Pro - Rp 99.000
   - Business - Rp 499.000 / bulan
4. Klik tombol **"Simpan Paket"**
5. Selesai! Halaman refresh dan paket berubah

---

## 📦 Struktur Halaman

```
┌─────────────────────────────────────────────┐
│ EDIT USER: [Nama User]                      │
├─────────────────────────────────────────────┤
│                                             │
│ Card 1: Form Edit User                      │
│ - Nama                                      │
│ - Email                                     │
│ - Password                                  │
│ - Role (checkbox)                           │
│ - Tombol: Update | Detail | Batal          │
│                                             │
├─────────────────────────────────────────────┤
│                                             │
│ Card 2: Paket Pricing                       │
│                                             │
│ Paket Aktif Saat Ini:                       │
│ [Badge: Free]                               │
│ Sejak: 28 Mar 2026 · Selamanya             │
│                                             │
│ ─────────────────────────────────────────   │
│                                             │
│ Paket Pricing:                              │
│ [Dropdown: Free/Basic/Pro/Business]         │
│ Pilih paket untuk user ini                  │
│                                             │
│ [✓ Simpan Paket]                            │
│                                             │
└─────────────────────────────────────────────┘
```

---

## ✨ Fitur

### 1. Info Paket Aktif (Read-only)
- Badge dengan warna sesuai paket
- Tanggal mulai subscription
- Tanggal berakhir (jika ada) atau "Selamanya"

### 2. Dropdown Paket
- Menampilkan semua paket aktif
- Format: `Nama Paket - Harga`
- Paket aktif saat ini ter-select otomatis

### 3. Tombol Simpan
- Langsung assign paket baru
- Subscription lama otomatis expired
- Subscription baru dibuat dengan status active
- Redirect kembali ke halaman edit dengan pesan sukses

---

## 🔧 Yang Terjadi di Backend

Saat klik "Simpan Paket":

1. **Validasi**: Cek `plan_id` valid
2. **Expire Subscription Lama**: Status → `expired`
3. **Buat Subscription Baru**:
   - user_id: ID user
   - pricing_plan_id: Paket yang dipilih
   - order_number: Auto-generate (SUB-YYYYMMDD-XXXXXX)
   - amount: 0 (gratis oleh admin)
   - status: `active`
   - payment_method: `admin_assign`
   - starts_at: Sekarang
   - expires_at: NULL (selamanya)
   - paid_at: Sekarang
4. **Redirect**: Kembali ke halaman edit
5. **Flash Message**: "Paket {nama} berhasil di-assign ke {user}"

---

## 📊 Data yang Dibutuhkan

### Controller Method: `edit()`
```php
$plans = PricingPlan::where('is_active', true)->orderBy('price')->get();
$activePlan = $user->activePlan();
$activeSub = $user->activeSubscription();
```

### View Variables:
- `$user` - User yang sedang diedit
- `$plans` - Collection pricing plans aktif
- `$activePlan` - PricingPlan aktif user saat ini
- `$activeSub` - UserSubscription aktif user saat ini

---

## 🧪 Testing

### Test 1: Ubah Free ke Basic
```
1. Buka edit user dengan paket Free
2. Pilih "Basic - Rp 49.000" dari dropdown
3. Klik "Simpan Paket"
4. ✅ Badge berubah jadi "Basic"
5. ✅ Tanggal mulai = hari ini
6. ✅ Subscription lama status = expired
```

### Test 2: Ubah Basic ke Pro
```
1. Buka edit user dengan paket Basic
2. Pilih "Pro - Rp 99.000" dari dropdown
3. Klik "Simpan Paket"
4. ✅ Badge berubah jadi "Pro"
5. ✅ Subscription Basic expired
6. ✅ Subscription Pro active
```

### Test 3: Downgrade Pro ke Free
```
1. Buka edit user dengan paket Pro
2. Pilih "Free - Gratis" dari dropdown
3. Klik "Simpan Paket"
4. ✅ Badge berubah jadi "Free"
5. ✅ Subscription Pro expired
```

---

## 🚨 Troubleshooting

### "Card Paket Pricing kosong"
**Penyebab:** Tidak ada pricing plan aktif di database

**Solusi:**
```bash
php artisan db:seed --class=PricingPlanSeeder
```

### "Dropdown tidak muncul"
**Penyebab:** Browser cache belum refresh

**Solusi:**
1. Hard refresh: Ctrl+F5 atau Ctrl+Shift+R
2. Clear Laravel cache: `php artisan view:clear`
3. Buka di Incognito mode

### "Error saat simpan"
**Penyebab:** Route atau permission tidak ada

**Solusi:**
1. Cek route: `php artisan route:list | grep assign-plan`
2. Cek permission: User admin harus punya `edit-users`

---

## 📝 Perbandingan dengan Tambah User

### Tambah User Baru:
```
Form:
- Nama, Email, Password
- Role (checkbox)
- Paket Pricing (dropdown) ← SAMA
- Tombol: Simpan
```

### Edit User Existing:
```
Form 1 (Edit Data):
- Nama, Email, Password
- Role (checkbox)
- Tombol: Update

Form 2 (Ubah Paket):
- Info paket aktif
- Paket Pricing (dropdown) ← SAMA
- Tombol: Simpan Paket
```

**Konsisten:** Sama-sama menggunakan dropdown sederhana!

---

## ✅ Checklist Fitur

Setelah refresh halaman edit user, pastikan:
- [x] Card "Form Edit User" muncul
- [x] Card "Paket Pricing" muncul di bawahnya
- [x] Info paket aktif tampil dengan badge warna
- [x] Dropdown berisi 4 paket (Free, Basic, Pro, Business)
- [x] Paket aktif ter-select di dropdown
- [x] Tombol "Simpan Paket" berwarna hijau
- [x] Klik simpan → redirect ke edit dengan pesan sukses
- [x] Badge paket berubah sesuai pilihan

---

## 🎉 Kesimpulan

Fitur ubah paket user sudah selesai dengan implementasi yang sederhana:
- ✅ Hanya dropdown paket (sama seperti tambah user)
- ✅ Tidak ada input tanggal kadaluarsa
- ✅ Tidak ada tombol reset terpisah
- ✅ Langsung simpan dan selesai

Admin tinggal pilih paket dari dropdown dan klik simpan!

---

**Dibuat:** 28 Maret 2026  
**Status:** ✅ FINAL & TESTED  
**Versi:** 3.0 (Simplified)
