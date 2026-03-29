# Template Field Presets - Panduan Lengkap

Sistem preset field memastikan konsistensi field di semua template berdasarkan kategori acara.

## 📋 Daftar Preset

### 1. PERNIKAHAN (24 field)
**Kategori:** Pernikahan  
**Untuk:** Undangan pernikahan dengan akad nikah dan resepsi

#### Field Groups:
- **Mempelai** (10 field)
  - `groom_name` - Nama lengkap mempelai pria (required)
  - `groom_nickname` - Nama panggilan pria
  - `groom_photo` - Foto mempelai pria
  - `groom_father` - Nama ayah mempelai pria
  - `groom_mother` - Nama ibu mempelai pria
  - `bride_name` - Nama lengkap mempelai wanita (required)
  - `bride_nickname` - Nama panggilan wanita
  - `bride_photo` - Foto mempelai wanita
  - `bride_father` - Nama ayah mempelai wanita
  - `bride_mother` - Nama ibu mempelai wanita

- **Acara** (8 field)
  - `akad_date` - Tanggal akad (required)
  - `akad_time` - Waktu akad (required)
  - `akad_venue` - Tempat akad (required)
  - `akad_address` - Alamat lengkap akad
  - `reception_date` - Tanggal resepsi (required)
  - `reception_time` - Waktu resepsi (required)
  - `reception_venue` - Tempat resepsi (required)
  - `reception_address` - Alamat lengkap resepsi

- **Tambahan** (3 field)
  - `maps_url` - Link Google Maps
  - `love_story` - Cerita cinta
  - `cover_photo` - Foto cover

- **Musik** (3 field)
  - `music_url` - URL lagu (mp3)
  - `music_title` - Judul lagu
  - `music_artist` - Nama artis

---

### 2. ACARA PERUSAHAAN (15 field)
**Kategori:** Acara Perusahaan  
**Untuk:** Grand opening, seminar, launching produk, workshop

#### Field Groups:
- **Perusahaan** (3 field)
  - `company_name` - Nama perusahaan (required)
  - `company_logo` - Logo perusahaan
  - `company_tagline` - Tagline/slogan

- **Acara** (7 field)
  - `event_title` - Judul acara (required)
  - `event_subtitle` - Subjudul acara
  - `event_description` - Deskripsi acara
  - `event_date` - Tanggal acara (required)
  - `event_time` - Waktu acara (required)
  - `event_venue` - Tempat acara (required)
  - `event_address` - Alamat lengkap

- **Tambahan** (4 field)
  - `maps_url` - Link Google Maps
  - `rsvp_note` - Catatan RSVP
  - `qr_note` - Catatan QR code
  - `cover_photo` - Foto cover

- **Musik** (1 field)
  - `music_url` - URL musik latar (mp3)

---

### 3. ULANG TAHUN (13 field)
**Kategori:** Ulang Tahun  
**Untuk:** Pesta ulang tahun anak atau dewasa

#### Field Groups:
- **Yang Berulang Tahun** (5 field)
  - `celebrant_name` - Nama yang berulang tahun (required)
  - `celebrant_nickname` - Nama panggilan
  - `celebrant_photo` - Foto
  - `celebrant_age` - Usia (tahun ke-)
  - `celebrant_birthdate` - Tanggal lahir

- **Acara** (5 field)
  - `party_theme` - Tema pesta
  - `party_date` - Tanggal pesta (required)
  - `party_time` - Waktu pesta (required)
  - `party_venue` - Tempat pesta (required)
  - `party_address` - Alamat lengkap

- **Tambahan** (3 field)
  - `maps_url` - Link Google Maps
  - `special_message` - Pesan khusus
  - `cover_photo` - Foto cover

---

### 4. KHITANAN (14 field)
**Kategori:** Khitanan  
**Untuk:** Undangan khitanan/sunatan

#### Field Groups:
- **Anak** (4 field)
  - `child_name` - Nama lengkap anak (required)
  - `child_nickname` - Nama panggilan
  - `child_photo` - Foto anak
  - `child_age` - Usia anak

- **Orang Tua** (2 field)
  - `father_name` - Nama ayah
  - `mother_name` - Nama ibu

- **Acara** (4 field)
  - `event_date` - Tanggal acara (required)
  - `event_time` - Waktu acara (required)
  - `event_venue` - Tempat acara (required)
  - `event_address` - Alamat lengkap

- **Tambahan** (3 field)
  - `maps_url` - Link Google Maps
  - `special_message` - Pesan khusus
  - `cover_photo` - Foto cover

- **Musik** (1 field)
  - `music_url` - URL musik latar (mp3)

---

## 🎯 Cara Menggunakan

### 1. Saat Membuat Template Baru
```php
// Di admin panel, pilih preset saat membuat template
$preset = 'wedding'; // atau 'corporate', 'birthday', 'circumcision'
$fields = TemplateFieldPreset::get($preset);
```

### 2. Saat Membuat Seeder
```php
use App\Support\TemplateFieldPreset;

// Ambil field dari preset
$fields = TemplateFieldPreset::wedding();

// Atau untuk acara perusahaan
$fields = TemplateFieldPreset::corporate();
```

### 3. Update Template Existing
Gunakan command untuk update field template yang sudah ada:
```bash
php artisan template:update-fields {template_id} --preset=wedding
```

---

## 📝 Aturan Penting

1. **Konsistensi Field**
   - Semua template dalam kategori yang sama HARUS menggunakan preset yang sama
   - Field key harus sama persis (case-sensitive)
   - Urutan field mengikuti order yang ditentukan

2. **Required Fields**
   - Field yang ditandai `required => true` wajib diisi user
   - Minimal ada 3-5 required field per preset

3. **Field Types**
   - `text` - Input teks pendek (nama, tempat)
   - `textarea` - Input teks panjang (alamat, cerita)
   - `date` - Input tanggal
   - `time` - Input waktu
   - `image` - Upload gambar
   - `url` - Input URL
   - `number` - Input angka

4. **Field Groups**
   - Mengelompokkan field untuk UI yang lebih rapi
   - Group umum: `mempelai`, `acara`, `tambahan`, `musik`

---

## 🔄 Migration dari Preset Lama

Jika ada template yang masih menggunakan preset lama (`wedding_standard`, `wedding_simple`):

1. Backup database
2. Update preset ke `wedding`
3. Jalankan migration field
4. Test semua template

---

## 🆕 Menambah Preset Baru

Jika perlu preset untuk kategori baru (misal: Aqiqah, Wisuda):

1. Tambahkan method di `TemplateFieldPreset.php`
2. Definisikan field sesuai kebutuhan
3. Update dokumentasi ini
4. Buat seeder contoh

---

## 📞 Support

Untuk pertanyaan atau request preset baru, hubungi tim development.
