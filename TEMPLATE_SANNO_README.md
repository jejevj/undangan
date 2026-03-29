# Template Sanno - Corporate Event

## Overview
Template "Sanno" adalah template premium untuk undangan acara perusahaan, seminar, grand opening, dan acara bisnis lainnya. Template ini memiliki desain profesional dan modern dengan fokus pada informasi acara yang jelas.

## Fitur Utama

### 1. Hero Section
- Logo perusahaan
- Judul acara yang menonjol
- Sub judul dan deskripsi acara
- Desain gradient biru profesional

### 2. Event Information
- Tanggal dan waktu acara
- Lokasi dan alamat lengkap
- Integrasi Google Maps
- Icon yang informatif

### 3. Countdown Timer
- Hitung mundur real-time
- Tampilan hari, jam, menit, detik
- Auto-update setiap detik

### 4. Gallery
- Grid layout responsif
- Lightbox untuk preview full-size
- Lazy loading untuk performa optimal
- Hover effect yang smooth

### 5. RSVP Form
- Form konfirmasi kehadiran
- Field: Nama, Instansi, Status Kehadiran, Pesan
- Desain form yang clean dan modern

### 6. QR Code Section
- Placeholder untuk QR code check-in
- Informasi tentang penggunaan QR code
- Untuk penukaran souvenir

### 7. Bottom Navigation
- Fixed bottom navbar
- Smooth scroll ke section
- Active state indicator
- Responsive design

### 8. Music Player
- Floating action button
- Auto-play dengan fallback
- Play/pause control
- Rotating disc animation

## Template Fields

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| company_logo | image | No | Logo perusahaan |
| event_title | text | Yes | Judul acara |
| event_subtitle | text | No | Sub judul acara |
| event_description | textarea | Yes | Deskripsi acara |
| event_date | date | Yes | Tanggal acara |
| event_time | time | Yes | Waktu acara |
| event_venue | text | Yes | Tempat acara |
| event_address | textarea | Yes | Alamat lengkap |
| maps_url | url | No | Link Google Maps |
| rsvp_note | textarea | No | Catatan untuk RSVP |
| qr_note | textarea | No | Catatan untuk QR Code |
| cover_photo | image | No | Foto cover untuk sharing |

## Color Scheme

```css
Primary: #1e3a8a (Blue)
Secondary: #3b82f6 (Light Blue)
Accent: #fbbf24 (Yellow)
Dark: #1f2937 (Dark Gray)
Light: #f3f4f6 (Light Gray)
```

## Typography
- Font Family: Inter (Google Fonts)
- Weights: 400, 500, 600, 700, 800

## Installation

Template sudah terinstall melalui seeder. Untuk menggunakan:

1. Login ke admin panel
2. Buat undangan baru
3. Pilih template "Sanno"
4. Isi semua field yang diperlukan
5. Upload logo dan foto
6. Preview dan publish

## File Structure

```
undangan/
├── database/seeders/
│   └── SannoTemplateSeeder.php
├── resources/views/invitation-templates/sanno/
│   └── index.blade.php
└── public/invitation-assets/sanno/
    ├── css/
    │   └── style.css
    ├── js/
    │   └── app.js
    └── images/
        └── .gitkeep
```

## Customization

### Mengubah Warna
Edit file `public/invitation-assets/sanno/css/style.css`:
```css
:root {
    --primary: #1e3a8a;    /* Ubah warna primary */
    --secondary: #3b82f6;  /* Ubah warna secondary */
    --accent: #fbbf24;     /* Ubah warna accent */
}
```

### Menambah Section Baru
Edit file `resources/views/invitation-templates/sanno/index.blade.php` dan tambahkan section baru sebelum footer.

## Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance
- Lazy loading images
- Optimized CSS animations
- Minimal JavaScript
- Fast page load

## Responsive Design
- Mobile-first approach
- Breakpoint: 768px
- Touch-friendly navigation
- Optimized for all screen sizes

## Version
1.0.0 - Initial Release

## Category
Acara Perusahaan (Corporate Event)

## Price
Rp 149.000

## Support
Untuk pertanyaan atau bantuan, hubungi tim support.
