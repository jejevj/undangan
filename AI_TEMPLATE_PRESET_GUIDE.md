# Panduan Preset Field untuk Template Baru

## 🎯 WAJIB GUNAKAN PRESET STANDAR

Saat membuat template baru, WAJIB gunakan salah satu preset field standar berikut:

---

## 1. PERNIKAHAN (24 field)
**Preset Code:** `wedding`  
**Kategori:** Pernikahan  
**Untuk:** Undangan pernikahan dengan akad nikah dan resepsi

### Field yang Tersedia:
```php
// Mempelai Pria
groom_name*         // Nama lengkap mempelai pria
groom_nickname      // Nama panggilan pria
groom_photo         // Foto mempelai pria
groom_father        // Nama ayah mempelai pria
groom_mother        // Nama ibu mempelai pria

// Mempelai Wanita
bride_name*         // Nama lengkap mempelai wanita
bride_nickname      // Nama panggilan wanita
bride_photo         // Foto mempelai wanita
bride_father        // Nama ayah mempelai wanita
bride_mother        // Nama ibu mempelai wanita

// Akad Nikah
akad_date*          // Tanggal akad
akad_time*          // Waktu akad
akad_venue*         // Tempat akad
akad_address        // Alamat lengkap akad

// Resepsi
reception_date*     // Tanggal resepsi
reception_time*     // Waktu resepsi
reception_venue*    // Tempat resepsi
reception_address   // Alamat lengkap resepsi

// Tambahan
maps_url            // Link Google Maps
love_story          // Cerita cinta
cover_photo         // Foto cover

// Musik
music_url           // URL lagu (mp3)
music_title         // Judul lagu
music_artist        // Nama artis
```

### Contoh Penggunaan di Blade:
```blade
<h1>{{ $data['groom_nickname'] ?? $data['groom_name'] }} & {{ $data['bride_nickname'] ?? $data['bride_name'] }}</h1>
<p>{{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('d F Y') }}</p>
<p>{{ $data['akad_venue'] }}</p>
```

---

## 2. ACARA PERUSAHAAN (15 field)
**Preset Code:** `corporate`  
**Kategori:** Acara Perusahaan  
**Untuk:** Grand opening, seminar, launching produk, workshop

### Field yang Tersedia:
```php
// Perusahaan
company_name*       // Nama perusahaan
company_logo        // Logo perusahaan
company_tagline     // Tagline/slogan

// Acara
event_title*        // Judul acara
event_subtitle      // Subjudul acara
event_description   // Deskripsi acara
event_date*         // Tanggal acara
event_time*         // Waktu acara
event_venue*        // Tempat acara
event_address       // Alamat lengkap

// Tambahan
maps_url            // Link Google Maps
rsvp_note           // Catatan RSVP
qr_note             // Catatan QR code
cover_photo         // Foto cover
music_url           // URL musik latar (mp3)
```

### Contoh Penggunaan di Blade:
```blade
<img src="{{ asset('storage/' . $data['company_logo']) }}" alt="Logo">
<h1>{{ $data['event_title'] }}</h1>
<p>{{ $data['event_subtitle'] }}</p>
<p>{{ \Carbon\Carbon::parse($data['event_date'])->translatedFormat('d F Y') }}</p>
```

---

## 3. ULANG TAHUN (13 field)
**Preset Code:** `birthday`  
**Kategori:** Ulang Tahun  
**Untuk:** Pesta ulang tahun anak atau dewasa

### Field yang Tersedia:
```php
// Yang Berulang Tahun
celebrant_name*     // Nama yang berulang tahun
celebrant_nickname  // Nama panggilan
celebrant_photo     // Foto
celebrant_age       // Usia (tahun ke-)
celebrant_birthdate // Tanggal lahir

// Acara
party_theme         // Tema pesta
party_date*         // Tanggal pesta
party_time*         // Waktu pesta
party_venue*        // Tempat pesta
party_address       // Alamat lengkap

// Tambahan
maps_url            // Link Google Maps
special_message     // Pesan khusus
cover_photo         // Foto cover
```

### Contoh Penggunaan di Blade:
```blade
<h1>{{ $data['celebrant_nickname'] ?? $data['celebrant_name'] }}</h1>
<p>Ulang Tahun ke-{{ $data['celebrant_age'] }}</p>
<p>Tema: {{ $data['party_theme'] }}</p>
```

---

## 4. KHITANAN (14 field)
**Preset Code:** `circumcision`  
**Kategori:** Khitanan  
**Untuk:** Undangan khitanan/sunatan

### Field yang Tersedia:
```php
// Anak
child_name*         // Nama lengkap anak
child_nickname      // Nama panggilan
child_photo         // Foto anak
child_age           // Usia anak

// Orang Tua
father_name         // Nama ayah
mother_name         // Nama ibu

// Acara
event_date*         // Tanggal acara
event_time*         // Waktu acara
event_venue*        // Tempat acara
event_address       // Alamat lengkap

// Tambahan
maps_url            // Link Google Maps
special_message     // Pesan khusus
cover_photo         // Foto cover
music_url           // URL musik latar (mp3)
```

### Contoh Penggunaan di Blade:
```blade
<h1>{{ $data['child_nickname'] ?? $data['child_name'] }}</h1>
<p>Putra dari {{ $data['father_name'] }} & {{ $data['mother_name'] }}</p>
<p>Usia: {{ $data['child_age'] }} tahun</p>
```

---

## 📝 Cara Membuat Seeder dengan Preset

```php
<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\TemplateField;
use App\Support\TemplateFieldPreset;
use Illuminate\Database\Seeder;

class MyNewTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat template
        $template = Template::create([
            'category_id' => 2, // Sesuaikan dengan kategori
            'name' => 'My Template Name',
            'slug' => 'my-template-slug',
            'type' => 'premium', // atau 'free'
            'price' => 150000,
            'blade_view' => 'invitation-templates.my-template-slug.index',
            'asset_folder' => 'my-template-slug',
            'description' => 'Deskripsi template',
            'is_active' => true,
        ]);

        // 2. Ambil field dari preset (PILIH SALAH SATU)
        $fields = TemplateFieldPreset::wedding();      // Untuk pernikahan
        // $fields = TemplateFieldPreset::corporate();  // Untuk acara perusahaan
        // $fields = TemplateFieldPreset::birthday();   // Untuk ulang tahun
        // $fields = TemplateFieldPreset::circumcision(); // Untuk khitanan

        // 3. Buat fields
        foreach ($fields as $fieldData) {
            TemplateField::create([
                'template_id' => $template->id,
                'key' => $fieldData['key'],
                'label' => $fieldData['label'],
                'type' => $fieldData['type'],
                'group' => $fieldData['group'],
                'required' => $fieldData['required'],
                'order' => $fieldData['order'],
            ]);
        }
    }
}
```

---

## 🎨 Live Edit Support

Untuk mendukung live edit, tambahkan atribut `data-editable` pada elemen yang bisa di-edit:

```blade
<span data-editable 
      data-field-key="groom_name" 
      data-field-type="text" 
      data-field-label="Nama Mempelai Pria">
    {{ $data['groom_name'] }}
</span>

<div data-editable 
     data-field-key="groom_photo" 
     data-field-type="image" 
     data-field-label="Foto Mempelai Pria">
    <img src="{{ asset('storage/' . $data['groom_photo']) }}" alt="">
</div>

<div data-editable 
     data-field-key="akad_date" 
     data-field-type="date" 
     data-field-label="Tanggal Akad">
    {{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('l, d F Y') }}
</div>
```

**Field Types untuk Live Edit:**
- `text` - Teks pendek (nama, tempat)
- `textarea` - Teks panjang (alamat, cerita)
- `date` - Tanggal
- `time` - Waktu
- `image` - Gambar
- `url` - URL

---

## ⚠️ ATURAN PENTING

1. **WAJIB gunakan preset standar** - Jangan buat field custom
2. **Field key harus sama persis** - Case-sensitive
3. **Gunakan preset yang sesuai kategori** - Wedding untuk pernikahan, Corporate untuk acara perusahaan, dst
4. **Semua field di preset harus ada di template** - Meskipun tidak semua ditampilkan
5. **Tambahkan data-editable** - Untuk mendukung live edit

---

## 📞 Referensi Lengkap

- File preset: `app/Support/TemplateFieldPreset.php`
- Dokumentasi lengkap: `TEMPLATE_FIELD_PRESETS.md`
- Contoh seeder: `database/seeders/SannoTemplateSeeder.php` (corporate)
- Contoh template: `resources/views/invitation-templates/premium-white-1/index.blade.php` (wedding)
