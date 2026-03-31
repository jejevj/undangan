# 📝 Panduan Membuat Template Baru

## 🎯 Overview

Dokumen ini adalah panduan lengkap untuk membuat template undangan baru dari awal hingga siap digunakan di production, termasuk setup otomatis untuk preview public dan pricing.

---

## 📋 Checklist Template Baru

- [ ] 1. Persiapan Asset & Struktur Folder
- [ ] 2. Buat Blade Template
- [ ] 3. Buat CSS/JS Custom
- [ ] 4. Buat Template Seeder
- [ ] 5. Buat Preview Seeder
- [ ] 6. Update DatabaseSeeder
- [ ] 7. Testing & Validation
- [ ] 8. Commit & Push

---

## 📁 STEP 1: Persiapan Asset & Struktur Folder

### 1.1 Struktur Folder Asset

Buat folder baru di `public/invitation-assets/[template-slug]/`

```
public/invitation-assets/[template-slug]/
├── css/
│   └── style.css
├── js/
│   └── script.js (optional)
├── images/
│   ├── ornaments/
│   │   ├── before-up-section-name.png
│   │   ├── welcome-ornament.png
│   │   ├── ornament-diagonal-left.png
│   │   └── ornament-diagonal-right.png (mirror dari left)
│   ├── backgrounds/
│   │   ├── hero-bg.png
│   │   └── section-bg.png
│   └── icons/
│       ├── calendar-icon.svg
│       └── location-icon.svg
└── fonts/ (optional)
    └── custom-font.woff2
```

### 1.2 Naming Convention untuk Asset

**Format:** `[position]-[element]-[variant].ext`

**Contoh:**
- `before-up-section-name.png` → Ornament sebelum section name, posisi atas
- `welcome-ornament.png` → Ornament untuk welcome section
- `ornament-diagonal-left-please-mirror.png` → Ornament diagonal kiri (perlu di-mirror untuk kanan)
- `hero-bg-gradient.png` → Background hero dengan gradient
- `section-divider-floral.svg` → Divider section dengan motif floral

**Tips Naming:**
- Gunakan kebab-case (huruf kecil dengan dash)
- Jelaskan posisi: `top`, `bottom`, `left`, `right`, `center`
- Jelaskan fungsi: `ornament`, `divider`, `background`, `icon`
- Tambahkan variant jika ada: `light`, `dark`, `gradient`, `floral`

---

## 🎨 STEP 2: Buat Blade Template

### 2.1 Lokasi File

Buat file blade di: `resources/views/invitation-templates/[template-slug]/index.blade.php`

### 2.2 Template Structure (Minimal)

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }}</title>
    
    {{-- SEO Meta Tags --}}
    @include('invitation-templates._seo_meta')
    
    {{-- CSS --}}
    <link rel="stylesheet" href="{{ asset('invitation-assets/[template-slug]/css/style.css') }}">
</head>
<body>
    {{-- Cover Section --}}
    <section id="cover">
        <!-- Cover content -->
    </section>
    
    {{-- Main Content --}}
    <main>
        {{-- Couple Section --}}
        <section id="couple">
            <!-- Couple info -->
        </section>
        
        {{-- Event Section --}}
        <section id="event">
            <!-- Event details -->
        </section>
        
        {{-- Gallery Section --}}
        @include('invitation-templates._gallery')
        
        {{-- Guest Messages Section --}}
        @include('invitation-templates._guest_messages')
    </main>
    
    {{-- Music Player --}}
    @include('invitation-templates._music_player')
    
    {{-- Bottom Navigation (if preview) --}}
    @include('invitation-templates._cta_preview')
    
    {{-- Scripts --}}
    <script src="{{ asset('invitation-assets/[template-slug]/js/script.js') }}"></script>
</body>
</html>
```


### 2.3 Menggunakan Template Fields

Template fields diambil dari database. Gunakan helper function:

```blade
{{-- Get field value --}}
{{ $invitation->getFieldValue('groom_name') }}

{{-- Get field with default --}}
{{ $invitation->getFieldValue('groom_name', 'Nama Mempelai Pria') }}

{{-- Check if field exists --}}
@if($invitation->getFieldValue('cover_photo'))
    <img src="{{ asset('storage/' . $invitation->getFieldValue('cover_photo')) }}">
@endif
```

**Field Keys yang Tersedia:**

Lihat file `TEMPLATE_FIELDS_COMPLETE_REFERENCE.md` untuk daftar lengkap field yang bisa digunakan.

---

## 💅 STEP 3: Buat CSS/JS Custom

### 3.1 CSS Structure

File: `public/invitation-assets/[template-slug]/css/style.css`

```css
/* ========================================
   [TEMPLATE NAME] - Custom Styles
   ======================================== */

/* === Variables === */
:root {
    --primary-color: #2cc392;
    --secondary-color: #1a9d6f;
    --text-color: #333;
    --bg-color: #fff;
    --font-primary: 'Poppins', sans-serif;
    --font-secondary: 'Playfair Display', serif;
}

/* === Base Styles === */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: var(--font-primary);
    color: var(--text-color);
    overflow-x: hidden;
}

/* === Cover Section === */
#cover {
    height: 100vh;
    background: url('../images/backgrounds/hero-bg.png') center/cover;
    position: relative;
}

/* === Ornaments === */
.ornament-top-left {
    position: absolute;
    top: 0;
    left: 0;
    width: 200px;
    opacity: 0.8;
}

.ornament-diagonal-right {
    transform: scaleX(-1); /* Mirror dari left */
}

/* === Responsive === */
@media (max-width: 768px) {
    .ornament-top-left {
        width: 120px;
    }
}
```

### 3.2 JavaScript (Optional)

File: `public/invitation-assets/[template-slug]/js/script.js`

```javascript
// Smooth scroll
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

// Countdown timer
function updateCountdown() {
    const eventDate = new Date(document.getElementById('event-date').dataset.date);
    const now = new Date();
    const diff = eventDate - now;
    
    // Calculate days, hours, minutes, seconds
    // Update DOM
}

setInterval(updateCountdown, 1000);
```

---

## 🗄️ STEP 4: Buat Template Seeder

### 4.1 Buat File Seeder

File: `database/seeders/[TemplateName]TemplateSeeder.php`

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Template;
use App\Support\TemplateFieldPreset;

class [TemplateName]TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::firstOrCreate(
            ['slug' => '[template-slug]'],
            [
                'name' => '[Template Display Name]',
                'type' => 'premium', // atau 'basic'
                'price' => 150000, // Harga dalam Rupiah
                'free_photo_limit' => null, // null = unlimited, atau angka untuk limit
                'extra_photo_price' => 5000, // Harga per foto tambahan
                'blade_view' => 'invitation-templates.[template-slug].index',
                'asset_folder' => '[template-slug]',
                'version' => '1.0.0',
                'description' => 'Deskripsi template yang menarik untuk customer',
                'is_active' => true,
            ]
        );

        // Add template fields
        foreach (TemplateFieldPreset::wedding() as $field) {
            $template->fields()->firstOrCreate(
                ['key' => $field['key']],
                array_merge($field, ['template_id' => $template->id])
            );
        }

        $this->command->info("✓ Template '[Template Display Name]' created successfully!");
    }
}
```

### 4.2 Preset Fields yang Tersedia

- `TemplateFieldPreset::wedding()` - Untuk undangan pernikahan
- `TemplateFieldPreset::corporate()` - Untuk event corporate (seperti Sanno)
- `TemplateFieldPreset::birthday()` - Untuk ulang tahun (coming soon)

**Custom Fields:**

Jika butuh field khusus, tambahkan manual:

```php
$template->fields()->firstOrCreate(
    ['key' => 'custom_field_name'],
    [
        'template_id' => $template->id,
        'label' => 'Label Field',
        'type' => 'text', // text, textarea, image, date, time, url
        'default_value' => null,
        'is_required' => false,
        'order' => 100,
    ]
);
```

---

## 🎬 STEP 5: Buat Preview Seeder

### 5.1 Buat File Preview Seeder

File: `database/seeders/[TemplateName]PreviewSeeder.php`


```php
<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Models\Invitation;
use App\Models\User;
use App\Models\InvitationData;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class [TemplateName]PreviewSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::where('slug', '[template-slug]')->first();
        
        if (!$template) {
            $this->command->error('Template [template-slug] tidak ditemukan!');
            return;
        }

        // Find or create preview user
        $previewUser = User::firstOrCreate(
            ['email' => 'preview@system.local'],
            [
                'name' => 'Preview System',
                'password' => bcrypt('preview-system-' . Str::random(16)),
            ]
        );

        // Create or update preview invitation
        $invitation = Invitation::updateOrCreate(
            [
                'template_id' => $template->id,
                'user_id' => $previewUser->id,
                'slug' => '[template-slug]-preview',
            ],
            [
                'title' => 'Pernikahan Budi & Siti',
                'status' => 'published',
                'published_at' => now(),
                'gallery_display' => 'grid',
                'gift_enabled' => true, // true untuk premium, false untuk basic
                'love_story_mode' => 'timeline', // 'timeline' atau 'longtext'
            ]
        );

        // Set invitation data
        $dataValues = [
            // Mempelai Pria
            'groom_name' => 'Budi Santoso, S.Kom',
            'groom_nickname' => 'Budi',
            'groom_photo' => 'https://img.freepik.com/free-photo/portrait-smiling-friendly-male-waiter_171337-5266.jpg',
            'groom_father' => 'Bapak Santoso',
            'groom_mother' => 'Ibu Sumiati',
            
            // Mempelai Wanita
            'bride_name' => 'Siti Nurhaliza, S.Pd',
            'bride_nickname' => 'Siti',
            'bride_photo' => 'https://static.vecteezy.com/system/resources/thumbnails/073/181/213/small/joyful-young-woman-holding-transparent-veil-over-head-in-bright-natural-light-photo.jpg',
            'bride_father' => 'Bapak Halim',
            'bride_mother' => 'Ibu Nurlaila',
            
            // Akad Nikah
            'akad_date' => '2026-05-15',
            'akad_time' => '08:00',
            'akad_venue' => 'Masjid Al-Ikhlas',
            'akad_address' => 'Jl. Merdeka No. 123, Jakarta Selatan',
            
            // Resepsi
            'reception_date' => '2026-05-15',
            'reception_time' => '11:00',
            'reception_venue' => 'Gedung Serbaguna Melati',
            'reception_address' => 'Jl. Melati Raya No. 45, Jakarta Selatan',
            
            // Tambahan
            'maps_url' => 'https://maps.google.com/?q=-6.2088,106.8456',
            'love_story' => 'Kami bertemu pertama kali di kampus pada tahun 2020...',
            'cover_photo' => null,
            'music_url' => 'invitation-assets/music/wedding-song.mp3',
            'music_title' => 'Wedding Song',
            'music_artist' => 'Instrumental',
        ];

        // Save data to invitation_data table
        foreach ($dataValues as $key => $value) {
            $field = $template->fields()->where('key', $key)->first();
            if ($field) {
                InvitationData::updateOrCreate(
                    [
                        'invitation_id' => $invitation->id,
                        'template_field_id' => $field->id,
                    ],
                    [
                        'value' => $value,
                    ]
                );
            }
        }

        // Gallery Photos (using external URLs from Unsplash)
        \App\Models\InvitationGallery::where('invitation_id', $invitation->id)->delete();
        
        $galleryPhotos = [
            'https://images.unsplash.com/photo-1519741497674-611481863552?w=800&q=80',
            'https://images.unsplash.com/photo-1606800052052-a08af7148866?w=800&q=80',
            'https://images.unsplash.com/photo-1591604466107-ec97de577aff?w=800&q=80',
            'https://images.unsplash.com/photo-1465495976277-4387d4b0b4c6?w=800&q=80',
            'https://images.unsplash.com/photo-1522673607200-164d1b6ce486?w=800&q=80',
            'https://images.unsplash.com/photo-1583939003579-730e3918a45a?w=800&q=80',
        ];

        foreach ($galleryPhotos as $index => $photoUrl) {
            $userPhoto = \App\Models\UserGalleryPhoto::create([
                'user_id' => $previewUser->id,
                'path' => $photoUrl,
                'caption' => 'Preview Photo ' . ($index + 1),
            ]);

            \App\Models\InvitationGallery::create([
                'invitation_id' => $invitation->id,
                'photo_id' => $userPhoto->id,
                'order' => $index,
            ]);
        }

        // Guest Messages
        \App\Models\GuestMessage::where('invitation_id', $invitation->id)->delete();
        $messages = [
            [
                'guest_name' => 'bapak-ibu-hendra',
                'message' => 'Selamat menempuh hidup baru! Barakallah! 🤲',
                'likes_count' => 15,
                'created_at' => now()->subDays(2),
            ],
            [
                'guest_name' => 'keluarga-budi',
                'message' => 'Selamat ya! Semoga langgeng! ❤️',
                'likes_count' => 12,
                'created_at' => now()->subDays(1),
            ],
        ];

        foreach ($messages as $msgData) {
            \App\Models\GuestMessage::create(array_merge($msgData, [
                'invitation_id' => $invitation->id,
                'ip_address' => '127.0.0.1',
                'is_approved' => true,
            ]));
        }

        // Update template with preview URL (with demo user parameter)
        $previewUrl = route('invitation.show', ['slug' => $invitation->slug]) . '?to=demo-user';
        $template->update(['preview_url' => $previewUrl]);

        $this->command->info("✓ Preview invitation untuk template '[Template Name]' berhasil dibuat!");
        $this->command->info("Preview URL: {$previewUrl}");
    }
}
```

### 5.2 Preview untuk Template Corporate (seperti Sanno)

Jika template untuk corporate event, gunakan data yang sesuai:

```php
$dataValues = [
    'company_logo' => null,
    'event_title' => 'Grand Opening',
    'event_subtitle' => 'Of our new Factory',
    'event_description' => 'We are pleased to invite you...',
    'event_date' => '2026-12-29',
    'event_time' => '10:00',
    'event_venue' => 'Venue Name',
    'event_address' => 'Full Address',
    'maps_url' => 'https://maps.google.com/',
    'rsvp_note' => 'Please confirm your attendance...',
    'qr_note' => 'Use QR Code for check-in...',
    'cover_photo' => null,
    'music_url' => 'invitation-assets/music/wedding-song.mp3',
    'music_title' => 'Background Music',
    'music_artist' => 'Instrumental',
];
```

---

## 🔧 STEP 6: Update DatabaseSeeder

### 6.1 Tambahkan ke DatabaseSeeder

File: `database/seeders/DatabaseSeeder.php`

Tambahkan seeder baru ke dalam array `$this->call()`:

```php
$this->call([
    // ... existing seeders ...
    
    // TEMPLATE - Tambahkan di sini
    [TemplateName]TemplateSeeder::class,
    [TemplateName]PreviewSeeder::class,
    
    // ... rest of seeders ...
]);
```

**Urutan yang Benar:**

```php
$this->call([
    // BASIC
    CurrentGeneralConfigSeeder::class,
    UserRoleSeeder::class,

    // TEMPLATE
    TemplateCategorySeeder::class,
    BasicTemplateSeeder::class,
    BasicPreviewSeeder::class,
    SannoTemplateSeeder::class,
    SannoPreviewSeeder::class,
    PremiumWhite1PreviewSeeder::class,
    [TemplateName]TemplateSeeder::class,      // ← BARU
    [TemplateName]PreviewSeeder::class,       // ← BARU
    TemplatePreviewInvitationSeeder::class,

    // ... rest
]);
```

---

## ✅ STEP 7: Testing & Validation

### 7.1 Run Seeder

```bash
php artisan db:seed --class=[TemplateName]TemplateSeeder
php artisan db:seed --class=[TemplateName]PreviewSeeder
```

Atau run semua seeder:

```bash
php artisan migrate:fresh --seed
```

### 7.2 Checklist Testing

- [ ] Template muncul di database `templates` table
- [ ] Template fields ter-create dengan benar
- [ ] Preview invitation ter-create dengan slug `[template-slug]-preview`
- [ ] Preview URL bisa diakses: `/i/[template-slug]-preview?to=demo-user`
- [ ] Semua asset (CSS, JS, images) ter-load dengan benar
- [ ] Responsive di mobile & desktop
- [ ] Music player berfungsi
- [ ] Gallery photos tampil
- [ ] Guest messages tampil
- [ ] CTA button "Gunakan Template Ini" muncul (untuk preview)
- [ ] Live edit berfungsi (jika sudah login)

### 7.3 Browser Testing

Test di berbagai browser:
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge
- [ ] Mobile Chrome
- [ ] Mobile Safari

---


## 📤 STEP 8: Commit & Push

### 8.1 Git Add Files

```bash
# Add template files
git add resources/views/invitation-templates/[template-slug]/
git add public/invitation-assets/[template-slug]/
git add database/seeders/[TemplateName]TemplateSeeder.php
git add database/seeders/[TemplateName]PreviewSeeder.php
git add database/seeders/DatabaseSeeder.php
```

### 8.2 Commit

```bash
git commit -m "Add new template: [Template Name]

- Add blade template and assets
- Add template seeder with pricing
- Add preview seeder with demo data
- Update DatabaseSeeder
"
```

### 8.3 Push

```bash
git push origin main
```

---

## 🎨 TEMPLATE SPECIFICATION FORMAT

Untuk dokumentasi template, buat file `TEMPLATE_[SLUG]_SPEC.md`:

```markdown
# Template: [Template Name]

## Basic Info
- **Slug:** [template-slug]
- **Type:** Premium / Basic
- **Price:** Rp [price]
- **Version:** 1.0.0
- **Created:** 2026-03-30

## Description
[Deskripsi lengkap template untuk marketing]

## Features
- ✅ Responsive design
- ✅ Music player
- ✅ Gallery photos
- ✅ Guest messages
- ✅ Countdown timer
- ✅ Google Maps integration
- ✅ RSVP form
- ✅ Gift/Bank account section (Premium only)
- ✅ Love story timeline (Premium only)

## Color Scheme
- Primary: #2cc392
- Secondary: #1a9d6f
- Text: #333
- Background: #fff

## Fonts
- Primary: Poppins
- Secondary: Playfair Display

## Assets Used
### Images
- `hero-bg.png` - Hero background (1920x1080)
- `ornament-top-left.png` - Top left ornament (300x300)
- `ornament-diagonal-left.png` - Diagonal ornament (400x400)

### Icons
- `calendar-icon.svg` - Calendar icon
- `location-icon.svg` - Location icon

## Field Preset
- Uses: `TemplateFieldPreset::wedding()`
- Custom fields: None

## Preview Data
- Groom: Budi Santoso
- Bride: Siti Nurhaliza
- Event Date: 2026-05-15
- Music: wedding-song.mp3

## Browser Support
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

## Performance
- Page Load: < 3s
- First Contentful Paint: < 1.5s
- Lighthouse Score: 90+

## Known Issues
- None

## Future Improvements
- [ ] Add animation on scroll
- [ ] Add video background option
- [ ] Add more color variants
```

---

## 🤖 AUTOMATION PROMPT (untuk AI)

Ketika Anda ingin membuat template baru dengan AI, gunakan prompt ini:

```
Saya ingin membuat template undangan baru dengan spesifikasi berikut:

**Template Info:**
- Name: [Template Display Name]
- Slug: [template-slug]
- Type: [premium/basic]
- Price: Rp [price]
- Description: [deskripsi singkat]

**Design Style:**
- Theme: [modern/classic/elegant/minimalist/floral/etc]
- Color Scheme: [primary color, secondary color]
- Fonts: [font names]

**Assets yang Sudah Disiapkan:**
Folder: public/invitation-assets/[template-slug]/images/
Files:
- before-up-section-name.png (ornament sebelum section name, posisi atas)
- welcome-ornament.png (ornament untuk welcome section)
- ornament-diagonal-left-please-mirror.png (ornament diagonal kiri, perlu di-mirror untuk kanan)
- hero-bg-gradient.png (background hero dengan gradient)
- section-divider-floral.svg (divider section dengan motif floral)

**Features yang Diinginkan:**
- [ ] Music player
- [ ] Gallery photos
- [ ] Guest messages
- [ ] Countdown timer
- [ ] Google Maps
- [ ] RSVP form
- [ ] Gift section (Premium only)
- [ ] Love story timeline (Premium only)

**Field Preset:**
- [ ] Wedding (TemplateFieldPreset::wedding())
- [ ] Corporate (TemplateFieldPreset::corporate())
- [ ] Custom fields: [list custom fields jika ada]

**Preview Data:**
- Groom: [nama]
- Bride: [nama]
- Event Date: [date]
- Music: [music file]

Tolong buatkan:
1. Blade template lengkap dengan struktur HTML
2. CSS custom dengan responsive design
3. JavaScript untuk interaksi (jika perlu)
4. Template Seeder
5. Preview Seeder
6. Update DatabaseSeeder

Ikuti panduan di NEW_TEMPLATE_CREATION_GUIDE.md
```

---

## 📊 PRICING GUIDELINES

### Rekomendasi Harga Template

**Basic Template:**
- Price: Rp 0 - Rp 50.000
- Features: Basic features only
- Gallery: Limited (6-12 photos)
- No gift section
- No love story timeline
- Simple design

**Premium Template:**
- Price: Rp 100.000 - Rp 200.000
- Features: All features
- Gallery: Unlimited
- Gift section included
- Love story timeline included
- Advanced design with animations

**Ultimate Template:**
- Price: Rp 250.000 - Rp 500.000
- Features: All features + extras
- Gallery: Unlimited
- Custom domain support
- Priority support
- Unique design with advanced animations
- Video background support

### Pricing Strategy

**Faktor yang Mempengaruhi Harga:**
1. Kompleksitas design (simple vs complex)
2. Jumlah animasi
3. Custom features
4. Asset quality (SVG vs PNG)
5. Responsive optimization
6. Browser compatibility
7. Performance optimization

**Upselling Opportunities:**
- Extra gallery slots: +Rp 25.000 (12 photos)
- Extra music slots: +Rp 15.000 (1 music)
- Custom domain: +Rp 50.000
- Remove watermark: +Rp 30.000
- Priority support: +Rp 20.000/month

---

## 🎯 BEST PRACTICES

### Design
1. **Mobile First** - Design untuk mobile dulu, baru desktop
2. **Performance** - Optimize images (WebP, lazy loading)
3. **Accessibility** - Gunakan semantic HTML, alt text, ARIA labels
4. **SEO** - Meta tags, structured data, sitemap
5. **Browser Compatibility** - Test di semua browser utama

### Code
1. **Clean Code** - Readable, maintainable, documented
2. **DRY Principle** - Don't Repeat Yourself
3. **Naming Convention** - Consistent naming (kebab-case untuk CSS, camelCase untuk JS)
4. **Comments** - Jelaskan code yang complex
5. **Version Control** - Commit dengan pesan yang jelas

### Assets
1. **Optimization** - Compress images (TinyPNG, ImageOptim)
2. **Format** - Gunakan WebP untuk photos, SVG untuk icons
3. **Lazy Loading** - Load images on demand
4. **CDN** - Gunakan CDN untuk fonts & libraries
5. **Caching** - Set proper cache headers

### Testing
1. **Functional Testing** - Semua fitur berfungsi
2. **Visual Testing** - Design sesuai mockup
3. **Performance Testing** - Load time < 3s
4. **Cross-browser Testing** - Works di semua browser
5. **Mobile Testing** - Works di berbagai device

---

## 🚨 COMMON ISSUES & SOLUTIONS

### Issue 1: Asset tidak ter-load
**Solusi:**
- Cek path asset (gunakan `asset()` helper)
- Cek permission folder (755 untuk folder, 644 untuk file)
- Clear cache: `php artisan cache:clear`

### Issue 2: Template tidak muncul di database
**Solusi:**
- Run seeder: `php artisan db:seed --class=[TemplateName]TemplateSeeder`
- Cek error di console
- Cek slug tidak duplikat

### Issue 3: Preview URL 404
**Solusi:**
- Cek invitation ter-create dengan status 'published'
- Cek slug invitation benar
- Cek route `invitation.show` ada

### Issue 4: Field value tidak muncul
**Solusi:**
- Cek field key benar
- Cek field ter-create di `template_fields` table
- Cek data ter-save di `invitation_data` table
- Gunakan `$invitation->getFieldValue('key')` bukan langsung akses

### Issue 5: CSS tidak apply
**Solusi:**
- Cek path CSS benar
- Clear browser cache (Ctrl+Shift+R)
- Cek CSS syntax error
- Cek specificity CSS

---

## 📚 REFERENCES

### Internal Documentation
- `TEMPLATE_FIELDS_COMPLETE_REFERENCE.md` - Daftar lengkap template fields
- `AI_TEMPLATE_CREATION_PROMPT.md` - Prompt untuk AI template creation
- `LIVE_EDIT_GUIDE.md` - Panduan live edit feature
- `TEMPLATE_SANNO_README.md` - Contoh template corporate

### External Resources
- [Laravel Blade Documentation](https://laravel.com/docs/blade)
- [Tailwind CSS](https://tailwindcss.com/) (optional)
- [Unsplash](https://unsplash.com/) - Free stock photos
- [SVG Repo](https://www.svgrepo.com/) - Free SVG icons
- [Google Fonts](https://fonts.google.com/) - Free fonts

---

## ✨ QUICK START CHECKLIST

Untuk membuat template baru dengan cepat:

```bash
# 1. Buat folder asset
mkdir -p public/invitation-assets/[template-slug]/{css,js,images}

# 2. Buat blade template
touch resources/views/invitation-templates/[template-slug]/index.blade.php

# 3. Buat CSS
touch public/invitation-assets/[template-slug]/css/style.css

# 4. Buat seeders
php artisan make:seeder [TemplateName]TemplateSeeder
php artisan make:seeder [TemplateName]PreviewSeeder

# 5. Edit seeders (copy dari template lain, modify)

# 6. Update DatabaseSeeder.php

# 7. Run seeder
php artisan db:seed --class=[TemplateName]TemplateSeeder
php artisan db:seed --class=[TemplateName]PreviewSeeder

# 8. Test preview
# Open: http://localhost:8000/i/[template-slug]-preview?to=demo-user

# 9. Commit & push
git add .
git commit -m "Add new template: [Template Name]"
git push
```

---

## 🎉 CONGRATULATIONS!

Anda sudah berhasil membuat template baru! 🚀

Template Anda sekarang:
- ✅ Tersimpan di database
- ✅ Memiliki preview public
- ✅ Memiliki pricing yang jelas
- ✅ Siap digunakan oleh customer
- ✅ Ter-commit di Git

**Next Steps:**
1. Share preview URL ke tim untuk review
2. Test di berbagai device & browser
3. Collect feedback dari user
4. Iterate & improve
5. Launch & promote! 🎊

---

**Happy Coding! 💻✨**

*Last Updated: 2026-03-30*
