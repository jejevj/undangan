# Knowledge Base — Sistem Undangan Digital

Dokumen ini menjelaskan seluruh model yang tersedia dalam aplikasi, beserta entitas, relasi, dan kegunaannya.

---

## Daftar Model

| Model | Tabel | Keterangan |
|---|---|---|
| `User` | `users` | Pengguna aplikasi |
| `Menu` | `menus` | Navigasi sidebar dinamis |
| `Template` | `templates` | Template desain undangan |
| `TemplateField` | `template_fields` | Definisi field per template |
| `Invitation` | `invitations` | Data undangan milik user |
| `InvitationData` | `invitation_data` | Nilai field per undangan (EAV) |
| `PricingPlan` | `pricing_plans` | Paket langganan/pricing |
| `UserSubscription` | `user_subscriptions` | Subscription user ke pricing plan |
| `Role` *(Spatie)* | `roles` | Role pengguna |
| `Permission` *(Spatie)* | `permissions` | Hak akses per fitur |

---

## 1. User

**File:** `app/Models/User.php`
**Tabel:** `users`

Mewakili pengguna yang login ke sistem. Menggunakan `HasRoles` dari Spatie Permission untuk manajemen role dan permission.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `name` | string | Nama lengkap pengguna |
| `email` | string (unique) | Email untuk login |
| `password` | string (hashed) | Password terenkripsi |
| `email_verified_at` | timestamp, nullable | Waktu verifikasi email |
| `remember_token` | string, nullable | Token "ingat saya" |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `roles` | BelongsToMany | `Role` | Role yang dimiliki user (via Spatie) |
| `permissions` | BelongsToMany | `Permission` | Permission langsung (via Spatie) |
| `invitations` | HasMany | `Invitation` | Undangan yang dibuat user |

### Traits

- `HasFactory` — factory untuk testing/seeding
- `Notifiable` — notifikasi Laravel
- `HasRoles` — role & permission dari Spatie Laravel Permission

---

## 2. Menu

**File:** `app/Models/Menu.php`
**Tabel:** `menus`

Menyimpan struktur navigasi sidebar secara dinamis. Mendukung hierarki dua level (parent → children). Visibilitas menu dikontrol oleh `permission_name` yang dicek terhadap permission user yang sedang login.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `name` | string | Label menu yang ditampilkan |
| `slug` | string (unique) | Identifier unik menu |
| `url` | string, nullable | Path URL tujuan |
| `icon` | string, nullable | Class icon (flaticon/fa) |
| `parent_id` | bigint FK, nullable | ID parent menu (null = menu utama) |
| `order` | integer | Urutan tampil di sidebar |
| `is_active` | boolean | Aktif/nonaktif |
| `permission_name` | string, nullable | Permission yang dibutuhkan untuk melihat menu ini |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `children` | HasMany | `Menu` | Sub-menu (ordered by `order`) |
| `parent` | BelongsTo | `Menu` | Menu induk |

### Method Statis

| Method | Return | Keterangan |
|---|---|---|
| `getMenuTree()` | `Collection` | Ambil semua menu aktif beserta children-nya, diurutkan by `order` |

### Logika Visibilitas Sidebar

- Menu **tanpa children**: tampil jika `permission_name` null, atau user memiliki permission tersebut.
- Menu **dengan children**: tampil jika minimal satu child-nya bisa diakses oleh user.
- Child menu: tampil jika `permission_name` null, atau user memiliki permission tersebut.

---

## 3. Template

**File:** `app/Models/Template.php`
**Tabel:** `templates`

Mendefinisikan desain/tema undangan yang tersedia. Setiap template memiliki blade view tersendiri dan sekumpulan field yang harus diisi oleh user.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `name` | string | Nama template, contoh: `Premium White 1` |
| `slug` | string (unique) | Identifier URL-friendly, contoh: `premium-white-1` |
| `thumbnail` | string, nullable | Path gambar preview template (storage) |
| `blade_view` | string | Nama blade view yang dirender, contoh: `invitation-templates.premium-white-1` |
| `description` | text, nullable | Deskripsi singkat template |
| `is_active` | boolean | Aktif/nonaktif (hanya template aktif yang bisa dipilih user) |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `fields` | HasMany | `TemplateField` | Definisi field template, ordered by `order` |
| `invitations` | HasMany | `Invitation` | Undangan yang menggunakan template ini |

### Method

| Method | Return | Keterangan |
|---|---|---|
| `fieldsByGroup()` | `Collection` | Field dikelompokkan berdasarkan nilai `group` |

---

## 4. TemplateField

**File:** `app/Models/TemplateField.php`
**Tabel:** `template_fields`

Mendefinisikan setiap field input yang harus diisi user saat membuat undangan dengan template tertentu. Bersifat fleksibel — admin bisa menambah/menghapus field tanpa mengubah kode.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `template_id` | bigint (FK) | Referensi ke `templates.id` |
| `key` | string | Identifier unik field dalam template, contoh: `groom_name` |
| `label` | string | Label yang ditampilkan di form, contoh: `Nama Mempelai Pria` |
| `type` | enum | Tipe input: `text`, `textarea`, `date`, `time`, `datetime`, `image`, `url`, `number`, `select` |
| `options` | text (JSON), nullable | Pilihan untuk tipe `select`, disimpan sebagai array JSON |
| `required` | boolean | Apakah field wajib diisi |
| `placeholder` | string, nullable | Placeholder input |
| `default_value` | text, nullable | Nilai default field |
| `group` | string, nullable | Pengelompokan field di form, contoh: `mempelai`, `acara`, `tambahan` |
| `order` | integer | Urutan tampil di form |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

**Unique constraint:** `(template_id, key)` — satu key tidak boleh duplikat dalam satu template.

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `template` | BelongsTo | `Template` | Template pemilik field ini |

### Tipe Field yang Tersedia

| Tipe | Keterangan |
|---|---|
| `text` | Input teks satu baris |
| `textarea` | Input teks multi baris |
| `date` | Tanggal (date picker) |
| `time` | Waktu (time picker) |
| `datetime` | Tanggal dan waktu |
| `image` | Upload gambar, disimpan ke storage |
| `url` | Input URL |
| `number` | Input angka |
| `select` | Dropdown, pilihan dari kolom `options` |

### Field Default Template "Premium White 1"

| Key | Label | Type | Group | Required |
|---|---|---|---|---|
| `groom_name` | Nama Mempelai Pria | text | mempelai | ✓ |
| `groom_nickname` | Nama Panggilan Pria | text | mempelai | |
| `groom_photo` | Foto Mempelai Pria | image | mempelai | |
| `groom_father` | Nama Ayah Mempelai Pria | text | mempelai | |
| `groom_mother` | Nama Ibu Mempelai Pria | text | mempelai | |
| `bride_name` | Nama Mempelai Wanita | text | mempelai | ✓ |
| `bride_nickname` | Nama Panggilan Wanita | text | mempelai | |
| `bride_photo` | Foto Mempelai Wanita | image | mempelai | |
| `bride_father` | Nama Ayah Mempelai Wanita | text | mempelai | |
| `bride_mother` | Nama Ibu Mempelai Wanita | text | mempelai | |
| `akad_date` | Tanggal Akad | date | acara | ✓ |
| `akad_time` | Waktu Akad | time | acara | ✓ |
| `akad_venue` | Tempat Akad | text | acara | ✓ |
| `akad_address` | Alamat Akad | textarea | acara | |
| `reception_date` | Tanggal Resepsi | date | acara | ✓ |
| `reception_time` | Waktu Resepsi | time | acara | ✓ |
| `reception_venue` | Tempat Resepsi | text | acara | ✓ |
| `reception_address` | Alamat Resepsi | textarea | acara | |
| `maps_url` | Link Google Maps | url | tambahan | |
| `love_story` | Cerita Cinta | textarea | tambahan | |
| `cover_photo` | Foto Cover | image | tambahan | |

---

## 5. Invitation

**File:** `app/Models/Invitation.php`
**Tabel:** `invitations`

Mewakili satu undangan digital yang dibuat oleh user. Setiap undangan terikat ke satu template dan memiliki slug unik sebagai URL publik.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `user_id` | bigint (FK) | Pemilik undangan, referensi ke `users.id` |
| `template_id` | bigint (FK) | Template yang digunakan, referensi ke `templates.id` |
| `slug` | string (unique) | UUID sebagai URL publik, contoh: `/inv/{slug}` |
| `title` | string, nullable | Judul undangan, contoh: `Pernikahan Budi & Ani` |
| `status` | enum | Status: `draft`, `published`, `expired` |
| `published_at` | timestamp, nullable | Waktu dipublikasikan |
| `expired_at` | timestamp, nullable | Waktu kadaluarsa undangan |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Status Undangan

| Status | Keterangan |
|---|---|
| `draft` | Belum dipublikasikan, hanya bisa diakses pemilik via preview |
| `published` | Aktif dan bisa diakses publik via URL `/inv/{slug}` |
| `expired` | Sudah kadaluarsa, tidak bisa diakses publik |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `user` | BelongsTo | `User` | Pemilik undangan |
| `template` | BelongsTo | `Template` | Template yang digunakan |
| `data` | HasMany | `InvitationData` | Nilai semua field undangan |

### Method

| Method | Return | Keterangan |
|---|---|---|
| `getDataMap()` | `array` | Semua data field sebagai `['key' => 'value']`, contoh: `['groom_name' => 'Budi']` |
| `getValue(string $key, $default)` | `mixed` | Ambil nilai satu field berdasarkan key |
| `isPublished()` | `bool` | Cek apakah status `published` |

---

## 6. InvitationData

**File:** `app/Models/InvitationData.php`
**Tabel:** `invitation_data`

Menyimpan nilai setiap field untuk satu undangan. Menggunakan pola **EAV (Entity-Attribute-Value)** agar fleksibel terhadap perubahan field template tanpa perlu mengubah skema database.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `invitation_id` | bigint (FK) | Referensi ke `invitations.id` |
| `template_field_id` | bigint (FK) | Referensi ke `template_fields.id` |
| `value` | longText, nullable | Nilai field (teks, path file, URL, dll) |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

**Unique constraint:** `(invitation_id, template_field_id)` — satu field hanya boleh punya satu nilai per undangan.

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `invitation` | BelongsTo | `Invitation` | Undangan pemilik data ini |
| `templateField` | BelongsTo | `TemplateField` | Definisi field yang nilainya disimpan |

---

## 7. PricingPlan

**File:** `app/Models/PricingPlan.php`
**Tabel:** `pricing_plans`

Mendefinisikan paket langganan yang tersedia untuk user. Setiap paket memiliki limit dan fitur yang berbeda.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `slug` | string (unique) | Identifier URL-friendly |
| `name` | string | Nama paket, contoh: `Free`, `Basic`, `Pro` |
| `price` | integer | Harga dalam rupiah (0 = gratis) |
| `badge_color` | string | Warna badge untuk UI (primary, success, warning, dll) |
| `is_popular` | boolean | Tandai sebagai paket populer |
| `max_invitations` | integer | Maksimal undangan yang bisa dibuat |
| `max_gallery_photos` | integer | Maksimal foto gallery per undangan |
| `max_music_uploads` | integer | Maksimal upload musik custom |
| `gift_section_included` | boolean | Apakah fitur gift/amplop digital tersedia |
| `can_delete_music` | boolean | Apakah user bisa hapus musik yang diupload |
| `features` | JSON array | Daftar fitur tambahan (untuk display) |
| `is_active` | boolean | Aktif/nonaktif |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `subscriptions` | HasMany | `UserSubscription` | Subscription yang menggunakan paket ini |

### Method

| Method | Return | Keterangan |
|---|---|---|
| `isFree()` | `bool` | Cek apakah paket gratis (price = 0) |
| `formattedPrice()` | `string` | Format harga untuk display, contoh: `Rp 50.000` atau `Gratis` |

### Manajemen Pricing Plans (Admin)

Admin dapat mengelola paket pricing melalui menu "Pengaturan > Manajemen Pricing":

- **Tambah paket baru**: Tentukan nama, harga, limit, dan fitur
- **Edit paket**: Update detail paket yang sudah ada
- **Toggle aktif/nonaktif**: Sembunyikan paket dari user tanpa menghapus
- **Hapus paket**: Hanya bisa dihapus jika tidak ada subscription aktif

### Route Pricing Plans

| Method | URL | Action | Permission |
|---|---|---|---|
| GET | `/pricing-plans` | Index | `view-pricing-plans` |
| GET | `/pricing-plans/create` | Create form | `create-pricing-plans` |
| POST | `/pricing-plans` | Store | `create-pricing-plans` |
| GET | `/pricing-plans/{id}/edit` | Edit form | `edit-pricing-plans` |
| PUT | `/pricing-plans/{id}` | Update | `edit-pricing-plans` |
| DELETE | `/pricing-plans/{id}` | Delete | `delete-pricing-plans` |
| PATCH | `/pricing-plans/{id}/toggle` | Toggle active | `edit-pricing-plans` |

---

## 8. UserSubscription

**File:** `app/Models/UserSubscription.php`
**Tabel:** `user_subscriptions`

Menyimpan data subscription user ke pricing plan tertentu.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `user_id` | bigint (FK) | Referensi ke `users.id` |
| `pricing_plan_id` | bigint (FK) | Referensi ke `pricing_plans.id` |
| `order_number` | string (unique) | Nomor order unik |
| `amount` | integer | Jumlah yang dibayar |
| `status` | enum | Status: `pending`, `active`, `expired` |
| `payment_method` | string | Metode pembayaran |
| `starts_at` | timestamp, nullable | Waktu mulai aktif |
| `expires_at` | timestamp, nullable | Waktu kadaluarsa (null = unlimited) |
| `paid_at` | timestamp, nullable | Waktu pembayaran |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `user` | BelongsTo | `User` | User pemilik subscription |
| `plan` | BelongsTo | `PricingPlan` | Paket yang di-subscribe |

---

## 10. Music

**File:** `app/Models/Music.php`
**Tabel:** `music`

Menyimpan data musik/lagu yang bisa digunakan sebagai background music undangan. Musik bisa berasal dari sistem (admin) atau upload user.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `title` | string | Judul lagu |
| `artist` | string, nullable | Nama artis/penyanyi |
| `file_path` | string | Path file audio (mp3/ogg/wav) |
| `duration` | string, nullable | Durasi lagu, contoh: `3:45` |
| `type` | enum | Tipe: `free` (gratis), `premium` (berbayar) |
| `price` | integer | Harga dalam rupiah (0 untuk gratis) |
| `cover` | string, nullable | Path gambar cover lagu |
| `is_active` | boolean | Aktif/nonaktif |
| `uploaded_by` | bigint FK, nullable | User yang upload (null = musik sistem/admin) |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `uploader` | BelongsTo | `User` | User yang upload musik (jika user upload) |
| `users` | BelongsToMany | `User` | User yang sudah beli/punya akses (via `music_user`) |
| `orders` | HasMany | `MusicOrder` | Order pembelian musik premium |

### Method

| Method | Return | Keterangan |
|---|---|---|
| `isFree()` | `bool` | Cek apakah musik gratis (type = free) |
| `isUserUpload()` | `bool` | Cek apakah musik diupload user (uploaded_by != null) |
| `formattedPrice()` | `string` | Format harga untuk display |
| `audioUrl()` | `string` | URL lengkap file audio |
| `accessibleByUser(User $user)` | `Collection` | Static method: ambil semua musik yang bisa diakses user |

### Logika Akses Musik

Method `accessibleByUser()` mengembalikan musik yang bisa diakses user:

1. **Musik Gratis (Sistem)**: Type `free`, uploaded_by `null`
2. **Musik Premium yang Dibeli**: Type `premium`, user ada di pivot table `music_user`
3. **Musik Upload Sendiri**: uploaded_by = user_id (private, hanya pemilik)

**Privacy**: Musik yang diupload user hanya muncul untuk user tersebut, tidak bisa dilihat user lain.

**Display Logic**: Di halaman galeri musik, semua musik (termasuk premium yang belum dibeli) ditampilkan. Musik yang belum dimiliki menampilkan tombol "Beli", sedangkan yang sudah dimiliki menampilkan URL untuk digunakan.

### Upload Musik - Biaya & Pembatasan

**Biaya Upload**: Rp 5.000 per lagu

**Pembatasan**: Fitur upload musik HANYA tersedia untuk user dengan paket FREE. User dengan paket berbayar (Basic, Pro, dll) sudah mendapat akses musik premium sehingga tidak perlu upload sendiri.

**Alasan Pembatasan**:
- Paket berbayar sudah include akses musik premium
- Upload musik adalah alternatif untuk paket free
- Mencegah duplikasi fitur

**Flow Upload**:
1. User paket Free buka form upload
2. Upload file → tersimpan temporary
3. Order pending dibuat (Rp 5.000)
4. User bayar (simulasi)
5. File dipindahkan ke permanent
6. Musik tersedia di library user

**Validasi**:
- Cek paket user di `uploadForm()` dan `userUpload()`
- Redirect dengan error jika bukan paket Free
- Error message: "Fitur upload musik hanya tersedia untuk paket Free"

### File Storage

- **Musik Sistem**: `public/invitation-assets/music/` (admin upload)
- **Musik User (Temporary)**: `storage/app/public/music-uploads-temp/` (sebelum payment)
- **Musik User (Permanent)**: `storage/app/public/music-uploads/` (setelah payment)

---

## 11. MusicOrder

**File:** `app/Models/MusicOrder.php`
**Tabel:** `music_orders`

Menyimpan order pembelian musik premium oleh user.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `user_id` | bigint (FK) | Referensi ke `users.id` |
| `music_id` | bigint (FK) | Referensi ke `music.id` |
| `order_number` | string (unique) | Nomor order unik |
| `amount` | integer | Jumlah yang dibayar |
| `status` | enum | Status: `pending`, `paid`, `failed` |
| `payment_method` | string | Metode pembayaran |
| `paid_at` | timestamp, nullable | Waktu pembayaran |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `user` | BelongsTo | `User` | User yang beli |
| `music` | BelongsTo | `Music` | Musik yang dibeli |

---

## 12. MusicUploadOrder

**File:** `app/Models/MusicUploadOrder.php`
**Tabel:** `music_upload_orders`

Menyimpan order upload musik oleh user (paket Free only). Upload musik dikenakan biaya Rp 5.000 per lagu.

### Kolom

| Kolom | Tipe | Keterangan |
|---|---|---|
| `id` | bigint (PK) | Primary key |
| `user_id` | bigint (FK) | Referensi ke `users.id` |
| `order_number` | string (unique) | Nomor order unik (format: MUP-xxxxx) |
| `amount` | integer | Biaya upload (default: 5000) |
| `status` | enum | Status: `pending`, `paid`, `failed` |
| `payment_method` | string | Metode pembayaran |
| `paid_at` | timestamp, nullable | Waktu pembayaran |
| `temp_title` | string, nullable | Judul lagu (temporary) |
| `temp_artist` | string, nullable | Artis (temporary) |
| `temp_file_path` | string, nullable | Path file temporary |
| `music_id` | bigint FK, nullable | ID musik setelah upload selesai |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diupdate |

### Relasi

| Relasi | Tipe | Target | Keterangan |
|---|---|---|---|
| `user` | BelongsTo | `User` | User yang upload |
| `music` | BelongsTo | `Music` | Musik hasil upload (setelah paid) |

### Method

| Method | Return | Keterangan |
|---|---|---|
| `isPaid()` | `bool` | Cek apakah order sudah dibayar |
| `isPending()` | `bool` | Cek apakah order masih pending |
| `formattedAmount()` | `string` | Format harga untuk display |
| `generateOrderNumber()` | `string` | Static: generate nomor order unik |

### Flow Upload Musik

1. User (paket Free) buka form upload
2. User isi form dan upload file
3. File tersimpan di `music-uploads-temp/`
4. Order dibuat dengan status `pending`
5. Redirect ke halaman checkout
6. User klik "Bayar Sekarang" (simulasi)
7. File dipindahkan ke `music-uploads/`
8. Record Music dibuat dengan `uploaded_by = user_id`
9. Order status = `paid`, `music_id` diisi
10. File temporary dihapus
11. Musik tersedia di library user

### Pembatasan Upload

Upload musik HANYA untuk user dengan paket FREE:
- User paket berbayar sudah dapat akses musik premium
- Validasi di `MusicController::uploadForm()` dan `userUpload()`
- Redirect dengan error jika bukan paket Free

---

## 13. Role & Permission (Spatie)

**Package:** `spatie/laravel-permission`
**Tabel:** `roles`, `permissions`, `model_has_roles`, `model_has_permissions`, `role_has_permissions`

Manajemen hak akses berbasis role. User bisa memiliki satu atau lebih role, dan setiap role memiliki sekumpulan permission.

### Permission yang Tersedia

| Grup | Permission |
|---|---|
| Dashboard | `view-dashboard` |
| Users | `view-users`, `create-users`, `edit-users`, `delete-users` |
| Roles | `view-roles`, `create-roles`, `edit-roles`, `delete-roles` |
| Permissions | `view-permissions`, `create-permissions`, `delete-permissions` |
| Menus | `view-menus`, `create-menus`, `edit-menus`, `delete-menus` |
| Templates | `view-templates`, `create-templates`, `edit-templates`, `delete-templates` |
| Invitations | `view-invitations`, `create-invitations`, `edit-invitations`, `delete-invitations` |
| Music (User) | `view-music`, `upload-music` |
| Music (Admin) | `manage-music` |
| Pricing Plans | `view-pricing-plans`, `create-pricing-plans`, `edit-pricing-plans`, `delete-pricing-plans` |

### Role Default

| Role | Permission | Keterangan |
|---|---|---|
| `admin` | Semua permission | Administrator dengan akses penuh |
| `staff` | `view-dashboard`, `view-invitations`, `create-invitations`, `edit-invitations`, `delete-invitations`, `view-music`, `upload-music` | Staff yang dapat mengelola undangan termasuk menghapus |
| `pengguna` | `view-dashboard`, `view-invitations`, `create-invitations`, `edit-invitations`, `view-music`, `upload-music` | Role default untuk user registrasi, tidak dapat menghapus undangan |

### Auto-Assign Role saat Registrasi

Ketika user baru melakukan registrasi, secara otomatis akan diberikan role `pengguna`. Ini dapat dilakukan dengan menambahkan kode berikut di controller registrasi atau event listener:

```php
// Setelah user dibuat
$user->assignRole('pengguna');
```

### Proteksi Permission di View

Untuk menyembunyikan tombol atau elemen UI berdasarkan permission, gunakan directive `@can`:

```blade
@can('delete-invitations')
    <button>Hapus</button>
@endcan
```

### Proteksi Permission di Controller

Untuk memvalidasi permission di controller, gunakan method `can()`:

```php
if (!auth()->user()->can('delete-invitations')) {
    abort(403, 'Anda tidak memiliki izin untuk menghapus undangan.');
}
```

---

## Diagram Relasi

```
users
  ├── has many → invitations
  └── belongs to many → roles → has many → permissions

templates
  ├── has many → template_fields
  └── has many → invitations

invitations
  ├── belongs to → users
  ├── belongs to → templates
  └── has many → invitation_data
        └── belongs to → template_fields

menus
  ├── belongs to → menus (parent)
  └── has many → menus (children)
```

---

## Alur Pembuatan Undangan

```
1. User login
2. Klik "Buat Undangan" → GET /invitations/select-template
3. Pilih template → GET /invitations/create?template_id={id}
4. Form muncul dinamis berdasarkan TemplateField template yang dipilih
5. Submit → POST /invitations → Invitation + InvitationData tersimpan
6. Edit data → PUT /invitations/{id}
7. Preview → GET /invitations/{id}/preview (render blade_view template)
8. Publish → POST /invitations/{id}/publish → status = published
9. Akses publik → GET /inv/{slug} (tanpa auth)
```

---

## Alur Musik & Upload

### Galeri Musik (Semua User)

**Halaman**: `/music`

**Yang Ditampilkan**:
1. Musik gratis (sistem) - langsung bisa digunakan
2. Musik premium (sistem) - tampil dengan tombol "Beli" jika belum dimiliki
3. Musik upload sendiri - hanya muncul untuk pemilik dengan badge "Upload Saya"

**Logika Display**:
- Semua musik aktif ditampilkan (termasuk premium yang belum dibeli)
- Musik yang sudah dimiliki: tampil URL untuk copy
- Musik yang belum dimiliki: tampil tombol "Beli"
- Musik upload user lain: tidak ditampilkan (privacy)

### Upload Musik (Paket Free Only)

**Pembatasan**: Hanya user dengan paket FREE yang bisa upload musik

**Alasan**:
- Paket berbayar sudah include akses musik premium
- Upload adalah alternatif untuk paket free
- Mencegah duplikasi fitur

**Flow**:
```
1. User paket Free klik "Upload Lagu Saya"
2. Isi form (title, artist, file MP3)
3. Submit → file tersimpan temporary
4. Order pending dibuat (Rp 5.000)
5. Redirect ke checkout
6. Preview audio & detail order
7. Klik "Bayar Sekarang" (simulasi)
8. File dipindahkan ke permanent
9. Record Music dibuat
10. Order status = paid
11. File temporary dihapus
12. Musik tersedia di library
```

**Validasi**:
- Cek paket user: `$user->activePlan()->slug === 'free'`
- Redirect jika bukan paket Free
- Error: "Fitur upload musik hanya tersedia untuk paket Free"

### Beli Musik Premium

**Flow**:
```
1. User klik "Beli" pada musik premium
2. Redirect ke halaman konfirmasi
3. Klik "Bayar Sekarang" (simulasi)
4. Order dibuat dan paid
5. Akses musik di-grant ke user (pivot table music_user)
6. Musik tersedia di library
```

---

## Catatan Pengembangan

- **Menambah template baru**: buat blade view di `resources/views/invitation-templates/`, tambah record di tabel `templates`, lalu definisikan field-fieldnya di `template_fields`.
- **Menambah field baru ke template yang sudah ada**: tambah record di `template_fields` dengan `template_id` yang sesuai. Undangan lama tidak terpengaruh (nilai field baru akan `null`).
- **Pola EAV**: `invitation_data` menyimpan nilai per field secara terpisah. Gunakan `$invitation->getDataMap()` untuk mendapatkan semua nilai sekaligus sebagai array asosiatif di dalam blade template.
- **Akses kontrol**: semua route admin dilindungi middleware `can:{permission}`. Sidebar juga dicek secara dinamis berdasarkan permission user yang login.

---

## Update Terbaru (28 Maret 2026)

### 1. Role & Permission Management
- Role "Pengguna" sebagai default untuk user baru
- Proteksi tombol hapus undangan berdasarkan permission
- Auto-assign role saat user dibuat
- Permission matrix lengkap untuk semua fitur

### 2. Music Privacy & Upload Fee
- Musik upload user bersifat private (hanya pemilik yang bisa lihat)
- Upload musik dikenakan biaya Rp 5.000 per lagu
- Upload HANYA untuk user paket FREE
- Musik premium ditampilkan untuk semua user dengan opsi beli

**Logika Musik**:
- Paket Free: Bisa upload musik (bayar Rp 5.000) atau beli musik premium
- Paket Berbayar: Sudah include akses musik premium, tidak perlu upload

### 3. Pricing Plans Management
- Panel admin untuk CRUD paket pricing
- Toggle aktif/nonaktif paket
- Validasi subscription aktif sebelum hapus
- Dynamic features list
- Permission-based access control

### 4. Music Display Logic
**Galeri Musik** (`/music`):
- Tampilkan SEMUA musik aktif (gratis + premium + upload sendiri)
- Musik yang dimiliki: tampil URL untuk copy
- Musik yang belum dimiliki: tampil tombol "Beli"
- Musik upload user lain: tidak ditampilkan (privacy)

**Upload Button**:
- Paket Free: Tampil tombol "Upload Lagu Saya (Rp 5.000)"
- Paket Berbayar: Tampil info "Paket X sudah termasuk akses musik premium"

---

## Testing Checklist

### Role & Permission
- [ ] User dengan role "Pengguna" tidak bisa hapus undangan
- [ ] User dengan role "Staff" bisa hapus undangan
- [ ] User baru otomatis dapat role "Pengguna"

### Music Privacy
- [ ] Musik upload User A tidak muncul untuk User B
- [ ] Badge "Upload Saya" muncul untuk musik personal
- [ ] Musik sistem muncul untuk semua user

### Music Upload Fee
- [ ] User paket Free bisa akses form upload
- [ ] User paket berbayar tidak bisa akses form upload
- [ ] Upload flow: temporary → payment → permanent
- [ ] File temporary dihapus setelah payment

### Music Display
- [ ] Semua musik premium ditampilkan di galeri
- [ ] Musik yang belum dimiliki tampil tombol "Beli"
- [ ] Musik yang sudah dimiliki tampil URL
- [ ] Preview audio berfungsi untuk semua musik

### Pricing Management
- [ ] Admin bisa CRUD paket pricing
- [ ] Non-admin tidak bisa akses menu pricing
- [ ] Toggle aktif/nonaktif berfungsi
- [ ] Tidak bisa hapus paket dengan subscriber aktif

---

## Database Seeding

### Menjalankan Seeder

Untuk mengisi database dengan data awal, jalankan:

```bash
php artisan db:seed
```

Atau untuk seeder spesifik:

```bash
php artisan db:seed --class=UserRoleSeeder
```

### Seeder yang Tersedia

| Seeder | Keterangan |
|---|---|
| `DatabaseSeeder` | Seeder utama yang memanggil semua seeder lain |
| `UserRoleSeeder` | Membuat role "Pengguna" dengan permission terbatas |
| `InvitationSeeder` | Membuat data contoh undangan |
| `MusicSeeder` | Membuat data musik default |
| `BasicTemplateSeeder` | Membuat template basic |
| `PricingPlanSeeder` | Membuat paket langganan |

### Role & Permission Seeding

Role dan permission dibuat otomatis saat menjalankan `DatabaseSeeder`. Struktur role:

- **Admin**: Akses penuh ke semua fitur
- **Staff**: Dapat mengelola undangan termasuk menghapus
- **Pengguna**: Role default untuk registrasi, tidak dapat menghapus undangan

Untuk menambahkan role baru atau mengubah permission, edit file `database/seeders/DatabaseSeeder.php` atau buat seeder terpisah.

---

## Implementasi Role & Permission

### Menyembunyikan Tombol Berdasarkan Permission

Gunakan directive `@can` di Blade untuk menyembunyikan elemen UI:

```blade
@can('delete-invitations')
    <form action="{{ route('invitations.destroy', $invitation) }}" method="POST">
        @csrf @method('DELETE')
        <button class="btn btn-danger">Hapus</button>
    </form>
@endcan
```

### Validasi Permission di Controller

Tambahkan pengecekan permission di method controller:

```php
public function destroy(Invitation $invitation)
{
    // Cek permission
    if (!auth()->user()->can('delete-invitations')) {
        abort(403, 'Anda tidak memiliki izin untuk menghapus undangan.');
    }
    
    $invitation->delete();
    return redirect()->route('invitations.index')->with('success', 'Undangan berhasil dihapus.');
}
```

### Middleware Permission

Untuk melindungi route dengan permission, gunakan middleware `can`:

```php
Route::delete('invitations/{invitation}', [InvitationController::class, 'destroy'])
    ->name('invitations.destroy')
    ->middleware('can:delete-invitations');
```

---

## Auto-Assign Role saat User Dibuat

Jika aplikasi memiliki fitur registrasi, tambahkan kode berikut setelah user berhasil dibuat:

```php
use App\Models\User;

// Setelah user dibuat
$user = User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
]);

// Assign role default "pengguna"
$user->assignRole('pengguna');
```

Atau bisa menggunakan event listener di `app/Providers/EventServiceProvider.php`:

```php
use Illuminate\Auth\Events\Registered;

protected $listen = [
    Registered::class => [
        function ($event) {
            $event->user->assignRole('pengguna');
        },
    ],
];
```


---

## Update Terbaru (28 Maret 2026 - Sore)

### 1. Landing Page dengan Theme Demos
- Landing page menggunakan theme dari `demos/index-2-dark.html`
- Assets dicopy ke `public/demos-assets/`
- Menggunakan dark theme dengan styling modern
- Struktur: Hero, About, Features, Templates, Pricing, Footer
- Route: `/` (root path)

### 2. Dashboard Routes dengan Prefix /dash
- Semua route dashboard sekarang menggunakan prefix `/dash`
- Dashboard URL: `/dash/`
- Contoh: `/dash/invitations`, `/dash/music`, `/dash/users`, dll
- Landing page tetap di root path `/`

### 3. General Config Management
- Model: `GeneralConfig` dengan key-value storage
- Table: `general_configs` (key, value, timestamps)
- Controller: `GeneralConfigController`
- Route: `/dash/general-config`
- Permission: `view-general-config`, `edit-general-config`

**Konfigurasi yang Tersedia**:
- `site_name`: Nama situs
- `site_description`: Deskripsi situs
- `contact_email`: Email kontak
- `contact_phone`: Nomor telepon
- `logo`: Path logo (upload)
- `favicon`: Path favicon (upload)
- `hero_title`: Judul hero section
- `hero_subtitle`: Subtitle hero section
- `about_title`: Judul about section
- `about_description`: Deskripsi about section

**Method Helper**:
```php
// Get config
GeneralConfig::get('site_name', 'default value');

// Set config
GeneralConfig::set('site_name', 'New Name');

// Get all configs
GeneralConfig::getAll();
```

### 4. Menu Structure Update
Menu di DatabaseSeeder sudah diupdate dengan:
- Semua URL menggunakan prefix `/dash`
- Menu "Konfigurasi Umum" ditambahkan di bawah "Pengaturan"
- Order menu disesuaikan

### 5. Assets Structure
```
public/
├── demos-assets/          # Theme dari demos folder
│   ├── css/
│   ├── js/
│   ├── img/
│   └── fonts/
├── landing-assets/        # Assets Acara theme (backup)
│   ├── css/
│   ├── js/
│   ├── images/
│   └── vendor/
└── invitation-assets/     # Assets untuk template undangan
```

---

## Testing Checklist - Landing Page & Config

### Landing Page
- [ ] Akses `/` menampilkan landing page dengan theme dark
- [ ] Navbar sticky berfungsi
- [ ] Link "Login" redirect ke `/login`
- [ ] Link "Dashboard" muncul jika sudah login
- [ ] Hero section dengan form subscribe
- [ ] About section dengan fitur list
- [ ] Features section dengan 3 card
- [ ] Pricing section menampilkan paket dari database
- [ ] Footer dengan informasi kontak

### Dashboard Routes
- [ ] `/dash` menampilkan dashboard
- [ ] `/dash/invitations` menampilkan daftar undangan
- [ ] `/dash/music` menampilkan galeri musik
- [ ] `/dash/general-config` menampilkan form konfigurasi (admin only)
- [ ] Semua menu sidebar menggunakan URL dengan prefix `/dash`

### General Config
- [ ] Admin bisa akses menu "Konfigurasi Umum"
- [ ] Form konfigurasi menampilkan semua field
- [ ] Upload logo dan favicon berfungsi
- [ ] Simpan konfigurasi berhasil
- [ ] Konfigurasi tersimpan di database `general_configs`
- [ ] Non-admin tidak bisa akses menu konfigurasi

---

## File Structure Update

```
undangan/
├── app/
│   ├── Http/Controllers/
│   │   ├── LandingController.php (NEW)
│   │   └── GeneralConfigController.php (UPDATED)
│   └── Models/
│       └── GeneralConfig.php (NEW)
├── database/
│   └── migrations/
│       └── 2026_03_27_214425_create_general_configs_table.php (NEW)
├── public/
│   └── demos-assets/ (NEW - copied from demos/assets)
├── resources/views/
│   ├── landing/
│   │   └── index.blade.php (UPDATED - using demos theme)
│   └── general-config/
│       └── index.blade.php (UPDATED - added new fields)
└── routes/
    └── web.php (UPDATED - added /dash prefix & landing route)
```

---

## Cara Menggunakan Konfigurasi Umum

### Di Controller
```php
use App\Models\GeneralConfig;

$siteName = GeneralConfig::get('site_name', 'Default Name');
$logo = GeneralConfig::get('logo');
```

### Di Blade View
```blade
{{ GeneralConfig::get('site_name', config('app.name')) }}

@if(GeneralConfig::get('logo'))
    <img src="{{ asset('storage/' . GeneralConfig::get('logo')) }}" alt="Logo">
@endif
```

### Update Landing Page dengan Config
Landing page bisa diupdate untuk menggunakan konfigurasi dari database:
```blade
<h2>{{ GeneralConfig::get('hero_title', 'Buat Undangan Digital Impianmu') }}</h2>
<p>{{ GeneralConfig::get('hero_subtitle', 'Mulai Sekarang Gratis!') }}</p>
```

---


## Template Thumbnail Auto-Generation

### Current Status
Template thumbnails are currently uploaded manually by admin through the template management interface.

### Future Enhancement: Auto-Generate Thumbnail from Preview

**Concept:**
Automatically generate template thumbnail by taking a screenshot of the published invitation preview.

**Requirements:**
1. **Puppeteer/Browsershot Package**
   ```bash
   composer require spatie/browsershot
   ```
   
2. **Node.js & Puppeteer**
   ```bash
   npm install puppeteer
   ```

3. **Implementation Approach:**
   - When admin publishes an invitation for a template
   - System automatically captures screenshot of the preview URL
   - Save screenshot as template thumbnail
   - Update template record with new thumbnail path

**Sample Code Structure:**
```php
use Spatie\Browsershot\Browsershot;

public function generateThumbnail(Template $template)
{
    if (!$template->preview_url) {
        return false;
    }
    
    $filename = 'template-' . $template->slug . '-' . time() . '.jpg';
    $path = storage_path('app/public/templates/' . $filename);
    
    Browsershot::url($template->preview_url)
        ->windowSize(1200, 1800)
        ->setScreenshotType('jpeg', 80)
        ->save($path);
    
    $template->update([
        'thumbnail' => 'templates/' . $filename
    ]);
    
    return true;
}
```

**Pros:**
- ✅ Automatic thumbnail generation
- ✅ Always up-to-date with template design
- ✅ Consistent thumbnail quality
- ✅ No manual upload needed

**Cons:**
- ❌ Requires Node.js and Puppeteer installation
- ❌ Server resource intensive
- ❌ Slower than manual upload
- ❌ May fail if preview URL is not accessible

**Alternative: Manual Upload (Current)**
- Admin uploads thumbnail when creating/editing template
- Faster and more reliable
- Full control over thumbnail appearance
- No additional dependencies

**Recommendation:**
For production, consider implementing auto-generation as an optional feature that admin can trigger manually via a "Generate Thumbnail" button in the template edit page, rather than automatic on every publish.
