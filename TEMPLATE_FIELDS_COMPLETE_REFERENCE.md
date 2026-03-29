# Template Fields - Complete Reference Guide

Dokumentasi lengkap untuk semua field yang tersedia di sistem undangan digital. Dokumen ini mencakup semua preset field untuk berbagai jenis acara.

---

## 📋 Daftar Preset yang Tersedia

| Preset Key | Label | Total Fields | Deskripsi |
|------------|-------|--------------|-----------|
| `wedding` | Pernikahan | 24 fields | Field standar untuk undangan pernikahan (akad + resepsi) |
| `corporate` | Acara Perusahaan | 15 fields | Field untuk grand opening, seminar, launching produk |
| `birthday` | Ulang Tahun | 13 fields | Field untuk undangan ulang tahun anak atau dewasa |
| `circumcision` | Khitanan | 14 fields | Field untuk undangan khitanan/sunatan |
| `empty` | Kosong | 0 fields | Template tanpa field (custom manual) |

---

## 1️⃣ PRESET: PERNIKAHAN (Wedding)

Total: **24 Fields**

### 👰 Mempelai (Group: `mempelai`)

#### Mempelai Pria
| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `groom_name` | Nama Lengkap Mempelai Pria | text | ✅ Yes | 1 |
| `groom_nickname` | Nama Panggilan Pria | text | ❌ No | 2 |
| `groom_photo` | Foto Mempelai Pria | image | ❌ No | 3 |
| `groom_father` | Nama Ayah Mempelai Pria | text | ❌ No | 4 |
| `groom_mother` | Nama Ibu Mempelai Pria | text | ❌ No | 5 |

#### Mempelai Wanita
| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `bride_name` | Nama Lengkap Mempelai Wanita | text | ✅ Yes | 6 |
| `bride_nickname` | Nama Panggilan Wanita | text | ❌ No | 7 |
| `bride_photo` | Foto Mempelai Wanita | image | ❌ No | 8 |
| `bride_father` | Nama Ayah Mempelai Wanita | text | ❌ No | 9 |
| `bride_mother` | Nama Ibu Mempelai Wanita | text | ❌ No | 10 |

### 📅 Acara (Group: `acara`)

#### Akad Nikah
| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `akad_date` | Tanggal Akad | date | ✅ Yes | 11 |
| `akad_time` | Waktu Akad | time | ✅ Yes | 12 |
| `akad_venue` | Tempat Akad | text | ✅ Yes | 13 |
| `akad_address` | Alamat Lengkap Akad | textarea | ❌ No | 14 |

#### Resepsi
| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `reception_date` | Tanggal Resepsi | date | ✅ Yes | 15 |
| `reception_time` | Waktu Resepsi | time | ✅ Yes | 16 |
| `reception_venue` | Tempat Resepsi | text | ✅ Yes | 17 |
| `reception_address` | Alamat Lengkap Resepsi | textarea | ❌ No | 18 |

### ➕ Informasi Tambahan (Group: `tambahan`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `maps_url` | Link Google Maps | url | ❌ No | 19 |
| `love_story` | Cerita Cinta | textarea | ❌ No | 20 |
| `cover_photo` | Foto Cover | image | ❌ No | 21 |

### 🎵 Musik Latar (Group: `musik`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `music_url` | URL Lagu (mp3) | url | ❌ No | 22 |
| `music_title` | Judul Lagu | text | ❌ No | 23 |
| `music_artist` | Nama Artis | text | ❌ No | 24 |

---

## 2️⃣ PRESET: ACARA PERUSAHAAN (Corporate)

Total: **15 Fields**

### 🏢 Informasi Perusahaan (Group: `perusahaan`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `company_name` | Nama Perusahaan | text | ✅ Yes | 1 |
| `company_logo` | Logo Perusahaan | image | ❌ No | 2 |
| `company_tagline` | Tagline/Slogan | text | ❌ No | 3 |

### 📅 Detail Acara (Group: `acara`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `event_title` | Judul Acara | text | ✅ Yes | 4 |
| `event_subtitle` | Subjudul Acara | text | ❌ No | 5 |
| `event_description` | Deskripsi Acara | textarea | ❌ No | 6 |
| `event_date` | Tanggal Acara | date | ✅ Yes | 7 |
| `event_time` | Waktu Acara | time | ✅ Yes | 8 |
| `event_venue` | Tempat Acara | text | ✅ Yes | 9 |
| `event_address` | Alamat Lengkap | textarea | ❌ No | 10 |

### ➕ Informasi Tambahan (Group: `tambahan`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `maps_url` | Link Google Maps | url | ❌ No | 11 |
| `rsvp_note` | Catatan RSVP | textarea | ❌ No | 12 |
| `qr_note` | Catatan QR Code | textarea | ❌ No | 13 |
| `cover_photo` | Foto Cover | image | ❌ No | 14 |

### 🎵 Musik (Group: `musik`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `music_url` | URL Musik Latar (mp3) | url | ❌ No | 15 |

---

## 3️⃣ PRESET: ULANG TAHUN (Birthday)

Total: **13 Fields**

### 🎂 Yang Berulang Tahun (Group: `celebrant`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `celebrant_name` | Nama Yang Berulang Tahun | text | ✅ Yes | 1 |
| `celebrant_nickname` | Nama Panggilan | text | ❌ No | 2 |
| `celebrant_photo` | Foto | image | ❌ No | 3 |
| `celebrant_age` | Usia (tahun ke-) | number | ❌ No | 4 |
| `celebrant_birthdate` | Tanggal Lahir | date | ❌ No | 5 |

### 🎉 Detail Acara (Group: `acara`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `party_theme` | Tema Pesta | text | ❌ No | 6 |
| `party_date` | Tanggal Pesta | date | ✅ Yes | 7 |
| `party_time` | Waktu Pesta | time | ✅ Yes | 8 |
| `party_venue` | Tempat Pesta | text | ✅ Yes | 9 |
| `party_address` | Alamat Lengkap | textarea | ❌ No | 10 |

### ➕ Informasi Tambahan (Group: `tambahan`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `maps_url` | Link Google Maps | url | ❌ No | 11 |
| `special_message` | Pesan Khusus | textarea | ❌ No | 12 |
| `cover_photo` | Foto Cover | image | ❌ No | 13 |

---

## 4️⃣ PRESET: KHITANAN (Circumcision)

Total: **14 Fields**

### 👦 Anak Yang Dikhitan (Group: `anak`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `child_name` | Nama Lengkap Anak | text | ✅ Yes | 1 |
| `child_nickname` | Nama Panggilan | text | ❌ No | 2 |
| `child_photo` | Foto Anak | image | ❌ No | 3 |
| `child_age` | Usia Anak | number | ❌ No | 4 |

### 👨‍👩‍👦 Orang Tua (Group: `orangtua`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `father_name` | Nama Ayah | text | ❌ No | 5 |
| `mother_name` | Nama Ibu | text | ❌ No | 6 |

### 📅 Detail Acara (Group: `acara`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `event_date` | Tanggal Acara | date | ✅ Yes | 7 |
| `event_time` | Waktu Acara | time | ✅ Yes | 8 |
| `event_venue` | Tempat Acara | text | ✅ Yes | 9 |
| `event_address` | Alamat Lengkap | textarea | ❌ No | 10 |

### ➕ Informasi Tambahan (Group: `tambahan`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `maps_url` | Link Google Maps | url | ❌ No | 11 |
| `special_message` | Pesan Khusus | textarea | ❌ No | 12 |
| `cover_photo` | Foto Cover | image | ❌ No | 13 |

### 🎵 Musik (Group: `musik`)

| Key | Label | Type | Required | Order |
|-----|-------|------|----------|-------|
| `music_url` | URL Musik Latar (mp3) | url | ❌ No | 14 |

---

## 📝 Field Types Reference

| Type | Description | Input Element | Validation |
|------|-------------|---------------|------------|
| `text` | Single line text | `<input type="text">` | Max 255 chars |
| `textarea` | Multi-line text | `<textarea>` | Max 5000 chars |
| `date` | Date picker | `<input type="date">` | Valid date format |
| `time` | Time picker | `<input type="time">` | HH:MM format |
| `number` | Numeric input | `<input type="number">` | Integer only |
| `url` | URL input | `<input type="url">` | Valid URL format |
| `image` | Image upload | `<input type="file">` | jpg, jpeg, png, gif, webp |

---

## 🎯 Usage Examples

### 1. Menggunakan Preset di Seeder

```php
use App\Support\TemplateFieldPreset;

$template = Template::create([
    'name' => 'Premium White',
    'slug' => 'premium-white-1',
    'category' => 'wedding',
]);

// Apply wedding preset (24 fields)
foreach (TemplateFieldPreset::wedding() as $field) {
    $template->fields()->create($field);
}
```

### 2. Mengambil Field Definitions

```php
// Get all available presets
$presets = TemplateFieldPreset::all();
// Returns: ['wedding' => 'Pernikahan (24 field)', ...]

// Get specific preset fields
$weddingFields = TemplateFieldPreset::get('wedding');
$corporateFields = TemplateFieldPreset::get('corporate');
```

### 3. Menambah Field Custom

```php
$template->fields()->create([
    'key' => 'custom_field',
    'label' => 'Custom Field Label',
    'type' => 'text',
    'group' => 'custom',
    'is_required' => false,
    'order' => 100,
]);
```

---

## 🔧 Field Groups

Field dikelompokkan untuk memudahkan organisasi di form edit:

### Wedding Groups:
- `mempelai` - Data mempelai pria dan wanita
- `acara` - Informasi akad dan resepsi
- `tambahan` - Maps, love story, cover photo
- `musik` - Background music

### Corporate Groups:
- `perusahaan` - Company information
- `acara` - Event details
- `tambahan` - Additional info
- `musik` - Background music

### Birthday Groups:
- `celebrant` - Birthday person info
- `acara` - Party details
- `tambahan` - Additional info

### Circumcision Groups:
- `anak` - Child information
- `orangtua` - Parents information
- `acara` - Event details
- `tambahan` - Additional info
- `musik` - Background music

---

## 📊 Database Schema

```sql
CREATE TABLE template_fields (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    template_id BIGINT UNSIGNED NOT NULL,
    `key` VARCHAR(255) NOT NULL,
    label VARCHAR(255) NOT NULL,
    type VARCHAR(50) NOT NULL,
    `group` VARCHAR(100) NULL,
    placeholder TEXT NULL,
    default_value TEXT NULL,
    is_required BOOLEAN DEFAULT FALSE,
    `order` INT DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    FOREIGN KEY (template_id) REFERENCES templates(id) ON DELETE CASCADE,
    UNIQUE KEY unique_template_field (template_id, `key`)
);
```

---

## 🎨 Template Integration

### Accessing Fields in Blade Template

```blade
{{-- Access field value --}}
{{ $data['groom_name'] ?? 'Default Name' }}

{{-- Check if field exists --}}
@if(!empty($data['love_story']))
    <section id="cerita">
        {{ $data['love_story'] }}
    </section>
@endif

{{-- Image field --}}
@if(!empty($data['groom_photo']))
    <img src="{{ asset('storage/' . $data['groom_photo']) }}" alt="Groom">
@endif

{{-- Date formatting --}}
@if(!empty($data['akad_date']))
    {{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('d F Y') }}
@endif
```

### Live Edit Integration

```blade
<div data-editable 
     data-field-key="groom_name" 
     data-field-type="text" 
     data-field-label="Nama Mempelai Pria">
    {{ $data['groom_name'] }}
</div>
```

---

## 🚀 Best Practices

1. **Consistent Naming**: Gunakan naming convention yang konsisten (snake_case)
2. **Logical Grouping**: Group fields berdasarkan konteks yang sama
3. **Order Matters**: Set order field sesuai urutan tampilan di form
4. **Required Fields**: Hanya set required untuk field yang benar-benar wajib
5. **Default Values**: Berikan default value untuk field yang sering diisi sama
6. **Placeholder Text**: Gunakan placeholder untuk memberikan contoh format input

---

## 📚 Related Files

- **Preset Class**: `app/Support/TemplateFieldPreset.php`
- **Model**: `app/Models/TemplateField.php`
- **Migration**: `database/migrations/*_create_template_fields_table.php`
- **Seeder**: `database/seeders/DatabaseSeeder.php`
- **Controller**: `app/Http/Controllers/Admin/TemplateController.php`

---

## 📞 Support

Untuk pertanyaan atau request field baru, silakan hubungi tim development.

**Last Updated**: March 30, 2026
**Version**: 2.0
