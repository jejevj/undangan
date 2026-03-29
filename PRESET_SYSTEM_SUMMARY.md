# Sistem Preset Field - Ringkasan

## ✅ Yang Sudah Dilakukan

### 1. Preset Baru Dibuat
File: `app/Support/TemplateFieldPreset.php`

- ✅ **Pernikahan** (24 field) - Untuk undangan pernikahan
- ✅ **Acara Perusahaan** (15 field) - Untuk grand opening, seminar, dll
- ✅ **Ulang Tahun** (13 field) - Untuk pesta ulang tahun
- ✅ **Khitanan** (14 field) - Untuk undangan khitanan
- ❌ **Pernikahan Sederhana** - DIHAPUS (diganti dengan preset Pernikahan standar)

### 2. Template Existing Diupdate

| Template ID | Nama | Preset Lama | Preset Baru | Status |
|-------------|------|-------------|-------------|--------|
| 1 | Premium White 1 | wedding_standard (24) | wedding (24) | ✅ Updated |
| 2 | Basic | wedding_simple (12) | wedding (24) | ✅ Updated |
| 3 | Sanno | custom (12) | corporate (15) | ✅ Updated |

### 3. Command Baru
```bash
# Update template ke preset tertentu
php artisan template:update-fields {template_id} --preset=wedding --force
```

---

## 📋 Field Mapping Per Kategori

### PERNIKAHAN (24 field)
```
Mempelai (10): groom_name, groom_nickname, groom_photo, groom_father, groom_mother,
                bride_name, bride_nickname, bride_photo, bride_father, bride_mother

Acara (8): akad_date, akad_time, akad_venue, akad_address,
           reception_date, reception_time, reception_venue, reception_address

Tambahan (3): maps_url, love_story, cover_photo

Musik (3): music_url, music_title, music_artist
```

### ACARA PERUSAHAAN (15 field)
```
Perusahaan (3): company_name, company_logo, company_tagline

Acara (7): event_title, event_subtitle, event_description,
           event_date, event_time, event_venue, event_address

Tambahan (4): maps_url, rsvp_note, qr_note, cover_photo

Musik (1): music_url
```

### ULANG TAHUN (13 field)
```
Celebrant (5): celebrant_name, celebrant_nickname, celebrant_photo,
               celebrant_age, celebrant_birthdate

Acara (5): party_theme, party_date, party_time, party_venue, party_address

Tambahan (3): maps_url, special_message, cover_photo
```

### KHITANAN (14 field)
```
Anak (4): child_name, child_nickname, child_photo, child_age

Orang Tua (2): father_name, mother_name

Acara (4): event_date, event_time, event_venue, event_address

Tambahan (3): maps_url, special_message, cover_photo

Musik (1): music_url
```

---

## 🎯 Keuntungan Sistem Baru

1. **Konsistensi** - Semua template dalam kategori sama punya field yang sama
2. **Mudah Maintain** - Update field di satu tempat (TemplateFieldPreset.php)
3. **Scalable** - Mudah tambah preset baru untuk kategori baru
4. **Live Edit Compatible** - Semua field sudah siap untuk live edit
5. **Package Rules** - Fitur premium (galeri, gift) tetap dikontrol oleh package

---

## 📝 Cara Membuat Template Baru

### 1. Tentukan Kategori
Pilih kategori yang sesuai:
- Pernikahan → preset `wedding`
- Acara Perusahaan → preset `corporate`
- Ulang Tahun → preset `birthday`
- Khitanan → preset `circumcision`

### 2. Buat Seeder
```php
use App\Support\TemplateFieldPreset;

// Ambil field dari preset
$fields = TemplateFieldPreset::wedding(); // atau corporate(), birthday(), circumcision()

// Buat template
$template = Template::create([...]);

// Buat fields
foreach ($fields as $fieldData) {
    TemplateField::create([
        'template_id' => $template->id,
        ...
    ]);
}
```

### 3. Buat Blade Template
Gunakan field key yang sesuai preset:
```blade
{{-- Untuk wedding --}}
{{ $data['groom_name'] }}
{{ $data['bride_name'] }}

{{-- Untuk corporate --}}
{{ $data['company_name'] }}
{{ $data['event_title'] }}

{{-- Untuk birthday --}}
{{ $data['celebrant_name'] }}
{{ $data['party_theme'] }}

{{-- Untuk circumcision --}}
{{ $data['child_name'] }}
{{ $data['father_name'] }}
```

### 4. Tambahkan data-editable untuk Live Edit
```blade
<span data-editable 
      data-field-key="groom_name" 
      data-field-type="text" 
      data-field-label="Nama Mempelai Pria">
    {{ $data['groom_name'] }}
</span>
```

---

## 🔄 Migration Data Existing

Jika ada undangan yang sudah dibuat dengan field lama:

1. **Data tetap aman** - InvitationData menggunakan template_field_id
2. **Field baru otomatis tersedia** - User bisa isi field tambahan
3. **Field lama tetap bisa diakses** - Selama key-nya sama

---

## 🆕 Menambah Preset Baru

Contoh: Preset untuk Aqiqah

1. Edit `app/Support/TemplateFieldPreset.php`
2. Tambahkan di method `all()`:
```php
'aqiqah' => 'Aqiqah (12 field)',
```

3. Tambahkan di method `get()`:
```php
'aqiqah' => static::aqiqah(),
```

4. Buat method baru:
```php
public static function aqiqah(): array
{
    return [
        ['key' => 'baby_name', 'label' => 'Nama Bayi', ...],
        // dst
    ];
}
```

---

## 📞 Next Steps

1. ✅ Update template Sanno blade view untuk gunakan field corporate
2. ✅ Update template Basic blade view untuk gunakan field wedding lengkap
3. ✅ Test live edit di semua template
4. ✅ Update AI_TEMPLATE_CREATION_PROMPT.md dengan preset baru
5. ⏳ Buat template contoh untuk birthday dan circumcision

---

## 🎉 Kesimpulan

Sistem preset sekarang lebih terstruktur dan konsisten:
- 4 preset standar untuk 4 kategori acara
- Semua template existing sudah diupdate
- Command tersedia untuk update template
- Dokumentasi lengkap tersedia
- Live edit sudah terintegrasi
