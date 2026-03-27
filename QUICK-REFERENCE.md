# Quick Reference - Pricing Plans Management

## 🚀 Quick Start

### Akses Manajemen Pricing
1. Login sebagai admin
2. Sidebar → Pengaturan → Manajemen Pricing
3. Atau akses: `http://your-domain/pricing-plans`

---

## 📋 Menu Actions

| Action | Icon | Keterangan |
|---|---|---|
| Tambah Paket | ➕ Button | Buat paket pricing baru |
| Edit | ✏️ Pensil | Edit detail paket |
| Toggle | 👁️ Mata | Aktifkan/nonaktifkan paket |
| Hapus | 🗑️ Trash | Hapus paket (jika tidak ada subscriber) |

---

## 📝 Form Fields

### Informasi Dasar
- **Nama Paket** (required): Nama tampilan paket
- **Slug** (optional): Auto-generate jika kosong
- **Harga** (required): Dalam rupiah, 0 = gratis
- **Warna Badge** (required): primary, success, warning, danger, info, secondary

### Limit & Quota
- **Max Undangan** (required): Jumlah undangan yang bisa dibuat
- **Max Foto Gallery** (required): Jumlah foto per undangan
- **Max Upload Musik** (required): Jumlah musik custom yang bisa diupload

### Fitur Tambahan (Checkbox)
- **Gift Section Included**: Fitur amplop digital
- **Can Delete Music**: User bisa hapus musik yang diupload
- **Is Popular**: Tampilkan badge "Popular"
- **Is Active**: Paket aktif dan bisa dipilih user

### Daftar Fitur
- Dynamic list untuk menambah/hapus fitur display
- Satu fitur per baris
- Bisa kosong

---

## 🔐 Permission Required

| Action | Permission |
|---|---|
| Lihat daftar | `view-pricing-plans` |
| Tambah paket | `create-pricing-plans` |
| Edit paket | `edit-pricing-plans` |
| Toggle status | `edit-pricing-plans` |
| Hapus paket | `delete-pricing-plans` |

**Note**: Hanya role Admin yang memiliki semua permission ini.

---

## ⚠️ Validasi & Batasan

### Tidak Bisa Hapus Paket Jika:
- Ada user yang masih subscribe (status = active)
- Error message: "Tidak dapat menghapus paket yang masih memiliki subscription aktif"

### Solusi:
1. Gunakan **Toggle** untuk menyembunyikan paket
2. Tunggu semua subscription expired
3. Baru bisa dihapus

### Validasi Form:
- Nama: max 255 karakter
- Slug: harus unique
- Harga: min 0
- Max undangan: min 1
- Max foto: min 0
- Max musik: min 0

---

## 💡 Tips & Tricks

### Auto-Generate Slug
Kosongkan field slug, sistem akan generate otomatis dari nama paket.
- "Pro Plan" → `pro-plan`
- "Enterprise Plus" → `enterprise-plus`

### Paket Gratis
Set harga = 0, akan ditampilkan sebagai "Gratis" di UI.

### Toggle vs Delete
- **Toggle**: Sembunyikan paket sementara, data tetap ada
- **Delete**: Hapus permanen (hanya jika tidak ada subscriber)

### Badge Color Guide
- **primary** (biru): Paket standar
- **success** (hijau): Paket recommended
- **warning** (kuning): Paket popular/best value
- **danger** (merah): Paket premium/enterprise
- **info** (cyan): Paket trial/special
- **secondary** (abu): Paket basic/free

---

## 🔄 Workflow

### Membuat Paket Baru
```
1. Klik "Tambah Paket Baru"
2. Isi informasi dasar (nama, harga, badge)
3. Set limit (undangan, foto, musik)
4. Pilih fitur tambahan (checkbox)
5. Tambah daftar fitur (optional)
6. Submit
7. Paket muncul di daftar dan halaman subscription user
```

### Mengubah Paket
```
1. Klik icon pensil pada paket
2. Ubah data yang diperlukan
3. Submit
4. Perubahan langsung berlaku
5. User existing tetap menggunakan paket lama sampai expired
```

### Menonaktifkan Paket
```
1. Klik icon mata pada paket aktif
2. Status berubah jadi nonaktif
3. Paket tidak muncul di halaman subscription user
4. User yang sudah subscribe tetap bisa menggunakan
```

### Menghapus Paket
```
1. Pastikan tidak ada subscriber aktif
2. Klik icon trash
3. Confirm delete
4. Paket terhapus permanen
```

---

## 🗄️ Database Tables

### pricing_plans
```sql
id, slug, name, price, badge_color, is_popular,
max_invitations, max_gallery_photos, max_music_uploads,
gift_section_included, can_delete_music, features (JSON),
is_active, created_at, updated_at
```

### user_subscriptions
```sql
id, user_id, pricing_plan_id, order_number, amount,
status (pending/active/expired), payment_method,
starts_at, expires_at, paid_at, created_at, updated_at
```

---

## 🔗 Related Routes

| URL | Keterangan |
|---|---|
| `/pricing-plans` | Daftar paket (admin) |
| `/pricing-plans/create` | Form tambah paket |
| `/pricing-plans/{id}/edit` | Form edit paket |
| `/subscription` | Halaman pilih paket (user) |
| `/users/{id}` | Detail user (admin bisa assign paket) |

---

## 🐛 Troubleshooting

### Menu tidak muncul
```bash
php artisan cache:clear
# Logout dan login ulang
```

### Permission denied
```bash
# Cek role user
php artisan tinker
>>> auth()->user()->roles->pluck('name')

# Cek permission role
>>> auth()->user()->getAllPermissions()->pluck('name')
```

### Route not found
```bash
php artisan route:clear
php artisan route:cache
```

### View error
```bash
php artisan view:clear
php artisan view:cache
```

---

## 📞 Command Reference

```bash
# Jalankan seeder
php artisan db:seed --class=DatabaseSeeder

# Clear cache
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# List routes
php artisan route:list --path=pricing-plans

# Check permissions
php artisan tinker
>>> DB::table('permissions')->where('name', 'like', '%pricing%')->get()
```

---

## 📚 Documentation Files

- `Knowledge.md` - Dokumentasi lengkap sistem
- `CHANGELOG-PRICING-MANAGEMENT.md` - Detail implementasi
- `PRICING-TEST-RESULTS.md` - Hasil testing
- `SUMMARY-UPDATES.md` - Ringkasan semua update
- `QUICK-REFERENCE.md` - Panduan cepat (file ini)
