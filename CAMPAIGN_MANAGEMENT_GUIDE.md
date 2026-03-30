# Campaign Management System

Sistem manajemen kampanye untuk memberikan akses gratis ke plan tertentu kepada user yang mendaftar dengan kode kampanye khusus.

## Fitur

1. **Manajemen Kampanye**
   - Buat kampanye dengan kode unik
   - Tentukan plan yang diberikan gratis
   - Atur kuota maksimal user (atau unlimited)
   - Atur periode aktif kampanye
   - Toggle status aktif/nonaktif

2. **URL Registrasi Kampanye**
   - Format: `http://domain.com/register?ref=KODE_KAMPANYE`
   - User yang mendaftar dengan URL ini otomatis mendapat plan gratis

3. **Tracking**
   - Lihat jumlah user yang menggunakan kampanye
   - Lihat detail user yang terdaftar melalui kampanye
   - Monitor sisa kuota

## Cara Menggunakan

### 1. Membuat Kampanye Baru

1. Login sebagai admin
2. Buka menu **Marketing > Kampanye**
3. Klik **Buat Kampanye Baru**
4. Isi form:
   - **Nama Kampanye**: Nama deskriptif (contoh: "Promo Tahun Baru 2026")
   - **Kode Kampanye**: Kode unik untuk URL (contoh: "NEWYEAR2026")
   - **Deskripsi**: Penjelasan kampanye (opsional)
   - **Plan yang Diberikan**: Pilih plan yang akan diberikan gratis
   - **Maksimal User**: Jumlah maksimal user (0 = unlimited)
   - **Tanggal Mulai**: Kapan kampanye mulai aktif (kosongkan untuk langsung aktif)
   - **Tanggal Berakhir**: Kapan kampanye berakhir (kosongkan untuk tanpa batas)
   - **Aktifkan kampanye**: Centang untuk langsung aktifkan
5. Klik **Simpan Kampanye**

### 2. Membagikan URL Kampanye

Setelah kampanye dibuat, bagikan URL registrasi:
```
http://yourdomain.com/register?ref=KODE_KAMPANYE
```

Contoh:
```
http://yourdomain.com/register?ref=NEWYEAR2026
```

### 3. Monitoring Kampanye

1. Buka **Marketing > Kampanye**
2. Klik nama kampanye untuk melihat detail
3. Lihat:
   - Status kampanye (Aktif, Nonaktif, Kuota Penuh, dll)
   - Jumlah user yang sudah menggunakan
   - Sisa kuota
   - Daftar user yang terdaftar

### 4. Mengelola Kampanye

- **Edit**: Klik tombol edit untuk mengubah detail kampanye
- **Toggle Status**: Klik tombol toggle untuk aktifkan/nonaktifkan kampanye
- **Hapus**: Kampanye hanya bisa dihapus jika belum ada user yang menggunakan

## Status Kampanye

- **Aktif**: Kampanye berjalan normal dan bisa digunakan
- **Nonaktif**: Kampanye dinonaktifkan manual oleh admin
- **Belum Dimulai**: Tanggal mulai belum tiba
- **Berakhir**: Tanggal berakhir sudah lewat
- **Kuota Penuh**: Maksimal user sudah tercapai

## Validasi Kampanye

Kampanye dianggap valid jika:
1. Status aktif (is_active = true)
2. Tanggal sekarang >= tanggal mulai (jika ada)
3. Tanggal sekarang <= tanggal berakhir (jika ada)
4. Kuota belum penuh (jika max_users > 0)

## Database Schema

### Tabel `campaigns`
- `id`: Primary key
- `name`: Nama kampanye
- `code`: Kode unik untuk URL
- `description`: Deskripsi kampanye
- `pricing_plan_id`: Foreign key ke pricing_plans
- `max_users`: Maksimal user (0 = unlimited)
- `used_count`: Jumlah user yang sudah menggunakan
- `start_date`: Tanggal mulai (nullable)
- `end_date`: Tanggal berakhir (nullable)
- `is_active`: Status aktif/nonaktif
- `created_at`, `updated_at`: Timestamps

### Tabel `users` (tambahan)
- `campaign_id`: Foreign key ke campaigns (nullable)

## Permission

- `campaigns.view`: Melihat daftar kampanye
- `campaigns.create`: Membuat kampanye baru
- `campaigns.edit`: Mengedit kampanye
- `campaigns.delete`: Menghapus kampanye

Semua permission otomatis diberikan ke role `admin`.

## Contoh Use Case

### Kampanye "10 Pendaftar Pertama Gratis Basic"

1. Buat kampanye:
   - Nama: "10 Pendaftar Pertama Gratis Basic"
   - Kode: "FIRST10"
   - Plan: Basic Plan
   - Maksimal User: 10
   - Tanggal Mulai: (kosongkan)
   - Tanggal Berakhir: (kosongkan)
   - Aktif: Ya

2. Bagikan URL: `http://yourdomain.com/register?ref=FIRST10`

3. 10 user pertama yang mendaftar dengan URL tersebut akan otomatis mendapat Basic Plan gratis

4. Setelah 10 user terdaftar, kampanye otomatis berstatus "Kuota Penuh" dan tidak bisa digunakan lagi

### Kampanye "Promo Ramadan 2026"

1. Buat kampanye:
   - Nama: "Promo Ramadan 2026"
   - Kode: "RAMADAN2026"
   - Plan: Premium Plan
   - Maksimal User: 0 (unlimited)
   - Tanggal Mulai: 2026-03-01
   - Tanggal Berakhir: 2026-03-31
   - Aktif: Ya

2. Kampanye hanya aktif selama bulan Maret 2026
3. Tidak ada batasan jumlah user
4. Setelah 31 Maret 2026, kampanye otomatis berstatus "Berakhir"

## Tips

1. **Kode Kampanye**: Gunakan kode yang mudah diingat dan relevan dengan kampanye
2. **Monitoring**: Pantau secara berkala untuk melihat efektivitas kampanye
3. **Kuota**: Atur kuota sesuai budget dan kapasitas server
4. **Periode**: Gunakan periode untuk kampanye musiman atau event khusus
5. **Testing**: Test URL kampanye sebelum dibagikan ke publik

## Troubleshooting

### Kampanye tidak bisa digunakan
- Cek status kampanye (harus Aktif)
- Cek tanggal mulai dan berakhir
- Cek kuota (jika sudah penuh)

### User tidak mendapat plan gratis
- Pastikan kode kampanye benar di URL
- Pastikan kampanye masih valid saat registrasi
- Cek di detail kampanye apakah user tercatat

### Tidak bisa menghapus kampanye
- Kampanye yang sudah digunakan tidak bisa dihapus
- Alternatif: Nonaktifkan kampanye
