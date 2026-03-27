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

## 7. Role & Permission (Spatie)

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

### Role Default

| Role | Permission |
|---|---|
| `admin` | Semua permission |
| `staff` | `view-dashboard`, `view-invitations`, `create-invitations`, `edit-invitations`, `delete-invitations` |

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

## Catatan Pengembangan

- **Menambah template baru**: buat blade view di `resources/views/invitation-templates/`, tambah record di tabel `templates`, lalu definisikan field-fieldnya di `template_fields`.
- **Menambah field baru ke template yang sudah ada**: tambah record di `template_fields` dengan `template_id` yang sesuai. Undangan lama tidak terpengaruh (nilai field baru akan `null`).
- **Pola EAV**: `invitation_data` menyimpan nilai per field secara terpisah. Gunakan `$invitation->getDataMap()` untuk mendapatkan semua nilai sekaligus sebagai array asosiatif di dalam blade template.
- **Akses kontrol**: semua route admin dilindungi middleware `can:{permission}`. Sidebar juga dicek secara dinamis berdasarkan permission user yang login.
