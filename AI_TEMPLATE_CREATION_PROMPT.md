# AI Agent Prompt: Creating New Invitation Templates

## Overview
This guide provides comprehensive instructions for AI agents to create new invitation templates that are fully compatible with the existing undangan (invitation) system. Follow these instructions carefully to ensure consistency and functionality.

---

## System Architecture

### Template Components
Each template consists of:
1. **Database Record** - Template metadata in `templates` table
2. **Template Fields** - Dynamic fields in `template_fields` table
3. **Blade View** - HTML template in `resources/views/invitation-templates/`
4. **Assets** - CSS, JS, images in `public/invitation-assets/`
5. **Seeder** - Database seeder for installation

### File Structure
```
undangan/
├── app/
│   └── Models/
│       ├── Template.php
│       └── TemplateField.php
├── database/
│   └── seeders/
│       └── [TemplateName]Seeder.php
├── resources/
│   └── views/
│       └── invitation-templates/
│           ├── [template-slug]/
│           │   └── index.blade.php
│           ├── _gallery.blade.php (shared)
│           └── _gift.blade.php (shared)
└── public/
    └── invitation-assets/
        └── [template-slug]/
            ├── css/
            │   └── style.css
            ├── js/
            │   └── app.js
            ├── images/
            │   └── .gitkeep
            └── fonts/ (optional)
```

---

## Step 1: Planning Your Template

### Template Metadata
Before coding, define:

- **Name**: Display name (e.g., "Elegant Rose", "Modern Minimalist")
- **Slug**: URL-friendly identifier (e.g., "elegant-rose", "modern-minimalist")
- **Type**: `free`, `premium`, or `custom`
- **Price**: Integer (0 for free templates)
- **Category**: pernikahan, ulang-tahun, acara-perusahaan, khitanan
- **Photo Limits**: 
  - `free_photo_limit`: Number of free photos (null = unlimited)
  - `extra_photo_price`: Price per additional photo (default: 5000)
- **Guest Limit**: Maximum guests (null = unlimited)
- **Gift Feature Price**: Price to enable gift/bank account feature (default: 10000)

### Field Preset Selection
Choose one of these presets:
- **wedding_standard** (21 fields) - Full wedding template with all details
- **wedding_simple** (12 fields) - Basic wedding template
- **empty** - No default fields (for custom events)

---

## Step 2: Create Database Seeder

### File: `database/seeders/[TemplateName]Seeder.php`

```php
<?php

namespace Database\Seeders;

use App\Models\Template;
use App\Support\TemplateFieldPreset;
use Illuminate\Database\Seeder;

class ElegantRoseSeeder extends Seeder
{
    public function run(): void
    {
        $template = Template::firstOrCreate(
            ['slug' => 'elegant-rose'],
            [
                'category_id'        => 2, // 1=all, 2=pernikahan, 3=ulang-tahun, 4=acara-perusahaan, 5=khitanan
                'name'               => 'Elegant Rose',
                'type'               => 'premium', // free, premium, custom
                'price'              => 99000,
                'free_photo_limit'   => 10,
                'extra_photo_price'  => 5000,
                'gift_feature_price' => 10000,
                'guest_limit'        => null, // null = unlimited
                'blade_view'         => 'invitation-templates.elegant-rose.index',
                'asset_folder'       => 'elegant-rose',
                'version'            => '1.0.0',
                'description'        => 'Template undangan pernikahan dengan desain elegan dan romantis dengan aksen bunga mawar.',
                'is_active'          => true,
            ]

        );

        // Load preset fields
        foreach (TemplateFieldPreset::weddingStandard() as $field) {
            $template->fields()->firstOrCreate(
                ['key' => $field['key']],
                array_merge($field, ['template_id' => $template->id])
            );
        }

        $this->command->info("Template 'Elegant Rose' seeded successfully.");
    }
}
```

### Register Seeder
Add to `database/seeders/DatabaseSeeder.php`:
```php
$this->call(ElegantRoseSeeder::class);
```

---

## Step 3: Create Blade View Template

### File: `resources/views/invitation-templates/[slug]/index.blade.php`

### Available Variables in Blade
```php
$invitation      // Invitation model instance
$data            // Array of field values (e.g., $data['groom_name'])
$gallery         // Collection of InvitationGallery
$canonicalUrl    // SEO canonical URL
```

### Template Structure Requirements

#### 1. HTML Head with SEO
```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }}</title>
    
    {{-- SEO Meta Tags --}}
    <meta name="description" content="Undangan pernikahan {{ $data['groom_name'] ?? 'Mempelai Pria' }} & {{ $data['bride_name'] ?? 'Mempelai Wanita' }}">
    <meta name="keywords" content="undangan pernikahan, wedding invitation">
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    
    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $invitation->title }}">
    <meta property="og:description" content="Undangan pernikahan {{ $data['groom_name'] ?? '' }} & {{ $data['bride_name'] ?? '' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if(!empty($data['groom_photo']))
    <meta property="og:image" content="{{ asset('storage/' . $data['groom_photo']) }}">
    @endif
    
    {{-- Twitter Card --}}
    <meta name="twitter:card" content="summary_large_image">
    
    {{-- Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    
    {{-- Template CSS --}}
    <link href="{{ asset('invitation-assets/[slug]/css/style.css') }}" rel="stylesheet">
</head>
```

#### 2. Hero Section
```blade
<section id="hero" class="hero">
    <div class="hero-inner reveal">
        <div class="hero-bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
        <p class="hero-label">Undangan Pernikahan</p>
        <h1 class="hero-names">
            {{ $data['groom_nickname'] ?? $data['groom_name'] ?? 'Mempelai Pria' }}
            <span class="hero-ampersand">&</span>
            {{ $data['bride_nickname'] ?? $data['bride_name'] ?? 'Mempelai Wanita' }}
        </h1>
        @if(!empty($data['akad_date']))
            <p class="hero-date">{{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('d F Y') }}</p>
        @endif
    </div>
</section>
```

#### 3. Mempelai (Couple) Section
```blade
<section id="mempelai" class="section reveal">
    <h2 class="section-title">Mempelai</h2>
    <div class="divider"></div>
    <div class="couple-grid">
        <div>
            @if(!empty($data['groom_photo']))
                <img src="{{ asset('storage/' . $data['groom_photo']) }}" class="couple-photo" alt="">
            @else
                <div class="couple-photo-placeholder">♂</div>
            @endif
            <div class="couple-name">{{ $data['groom_name'] ?? '-' }}</div>
            <div class="couple-parents">
                Putra dari<br>
                {{ $data['groom_father'] ?? '' }}
                @if(!empty($data['groom_father']) && !empty($data['groom_mother'])) & @endif
                {{ $data['groom_mother'] ?? '' }}
            </div>
        </div>
        <div class="couple-sep">&</div>
        <div>
            @if(!empty($data['bride_photo']))
                <img src="{{ asset('storage/' . $data['bride_photo']) }}" class="couple-photo" alt="">
            @else
                <div class="couple-photo-placeholder">♀</div>
            @endif
            <div class="couple-name">{{ $data['bride_name'] ?? '-' }}</div>
            <div class="couple-parents">
                Putri dari<br>
                {{ $data['bride_father'] ?? '' }}
                @if(!empty($data['bride_father']) && !empty($data['bride_mother'])) & @endif
                {{ $data['bride_mother'] ?? '' }}
            </div>
        </div>
    </div>
</section>
```


#### 4. Acara (Event) Section
```blade
<section id="acara" class="section section-alt reveal">
    <h2 class="section-title">Acara</h2>
    <div class="divider"></div>
    <div class="event-grid">
        <div class="event-card">
            <h3>Akad Nikah</h3>
            @if(!empty($data['akad_date']))
                <div class="event-date">{{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('l, d F Y') }}</div>
            @endif
            @if(!empty($data['akad_time']))
                <div class="event-time">{{ $data['akad_time'] }} WIB</div>
            @endif
            <div class="event-venue">{{ $data['akad_venue'] ?? '' }}</div>
            @if(!empty($data['akad_address']))
                <div class="event-address">{{ $data['akad_address'] }}</div>
            @endif
        </div>
        <div class="event-card">
            <h3>Resepsi</h3>
            @if(!empty($data['reception_date']))
                <div class="event-date">{{ \Carbon\Carbon::parse($data['reception_date'])->translatedFormat('l, d F Y') }}</div>
            @endif
            @if(!empty($data['reception_time']))
                <div class="event-time">{{ $data['reception_time'] }} WIB</div>
            @endif
            <div class="event-venue">{{ $data['reception_venue'] ?? '' }}</div>
            @if(!empty($data['reception_address']))
                <div class="event-address">{{ $data['reception_address'] }}</div>
            @endif
        </div>
    </div>
    @if(!empty($data['maps_url']))
        <a href="{{ $data['maps_url'] }}" target="_blank" rel="noopener" class="btn-primary-inv">
            📍 Lihat di Google Maps
        </a>
    @endif
</section>
```

#### 5. Countdown Section
```blade
@if(!empty($data['akad_date']))
<section id="countdown-section" class="section reveal">
    <h2 class="section-title">Menuju Hari Bahagia</h2>
    <div class="divider"></div>
    <div class="countdown" id="countdown"
         data-date="{{ $data['akad_date'] }} {{ $data['akad_time'] ?? '00:00' }}"></div>
</section>
@endif
```

#### 6. Gallery Section (Use Shared Partial)
```blade
{{-- Include shared gallery partial --}}
@include('invitation-templates._gallery', [
    'sectionClass' => 'section-alt',
    'galleryColumns' => 3  // 2 or 3 columns
])
```

#### 7. Gift Section (Use Shared Partial)
```blade
{{-- Include shared gift/bank account partial --}}
@include('invitation-templates._gift', [
    'giftSectionClass' => 'section-alt'
])
```

#### 8. Footer
```blade
<footer class="inv-footer">
    <p>{{ $invitation->title }}</p>
    <p style="margin-top:6px;opacity:.5;font-size:.75rem">
        Dibuat dengan ❤ menggunakan sistem undangan digital
    </p>
</footer>
```

#### 9. Music FAB (Floating Action Button)
```blade
@php $musicUrl = $data['music_url'] ?? null; @endphp
@if($musicUrl)
<button class="music-fab playing" id="musicFab" aria-label="Play/Pause musik">
    <svg class="icon-disc" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
    </svg>
    <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor">
        <path d="M8 5v14l11-7z"/>
    </svg>
</button>
<audio id="bgMusic" src="{{ $musicUrl }}" loop preload="auto"></audio>
@endif
```

#### 10. Bottom Navigation Bar
```blade
<nav class="bottom-navbar" role="navigation">
    <a href="#hero" class="nav-item active" data-section="hero" aria-label="Beranda">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
            <polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Beranda
    </a>
    <a href="#mempelai" class="nav-item" data-section="mempelai" aria-label="Mempelai">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        Mempelai
    </a>
    <a href="#acara" class="nav-item" data-section="acara" aria-label="Acara">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Acara
    </a>
    @if(isset($gallery) && $gallery->count())
    <a href="#galeri" class="nav-item" data-section="galeri" aria-label="Galeri">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
        </svg>
        Galeri
    </a>
    @endif
</nav>

<script src="{{ asset('invitation-assets/[slug]/js/app.js') }}"></script>
</body>
</html>
```

---

## Step 4: Create CSS Stylesheet

### File: `public/invitation-assets/[slug]/css/style.css`

### Required CSS Variables
```css
:root {
    --primary:  #4a7c59;  /* Main brand color */
    --gold:     #c9a96e;  /* Accent color */
    --cream:    #faf8f5;  /* Background color */
    --text:     #333;     /* Text color */
    --muted:    #777;     /* Muted text */
    --navbar-h: 60px;     /* Bottom navbar height */
}
```

### Required CSS Classes


#### Base Styles
```css
*, *::before, *::after { 
    box-sizing: border-box; 
    margin: 0; 
    padding: 0; 
}

html { scroll-behavior: smooth; }

body {
    font-family: 'Lato', sans-serif;
    color: var(--text);
    background: var(--cream);
    overflow-x: hidden;
    padding-bottom: var(--navbar-h);
}
```

#### Typography Classes
```css
.t-serif { font-family: 'Playfair Display', serif; }
.t-upper { 
    letter-spacing: 3px; 
    text-transform: uppercase; 
    font-size: .72rem; 
}
.t-muted { color: var(--muted); }
```

#### Section Classes
```css
.section { 
    padding: 60px 20px; 
    text-align: center; 
}
.section-alt { background: #fff; }
.section-title { 
    font-family: 'Playfair Display', serif; 
    font-size: 1.8rem; 
    color: #333; 
}
.divider {
    width: 50px; 
    height: 2px;
    background: var(--gold);
    margin: 14px auto;
}
```

#### Reveal Animation
```css
.reveal { 
    opacity: 0; 
    transform: translateY(24px); 
    transition: opacity .5s, transform .5s; 
}
.reveal.revealed { 
    opacity: 1; 
    transform: none; 
}
```

#### Button Styles
```css
.btn-primary-inv {
    display: inline-block;
    padding: 12px 32px;
    background: var(--primary);
    color: #fff;
    text-decoration: none;
    border-radius: 50px;
    font-size: .85rem;
    letter-spacing: 1px;
    border: none;
    cursor: pointer;
    transition: background .2s, transform .2s;
    margin-top: 28px;
}
.btn-primary-inv:hover { 
    background: #3a6347; 
    transform: translateY(-1px); 
    color: #fff; 
}
```

#### Countdown Styles
```css
.countdown {
    display: flex; 
    justify-content: center;
    gap: 20px; 
    margin-top: 24px; 
    flex-wrap: wrap;
}
.countdown-item { text-align: center; min-width: 60px; }
.countdown-item span { 
    display: block; 
    font-family: 'Playfair Display', serif; 
    font-size: 2.2rem; 
    color: var(--primary); 
    line-height: 1; 
}
.countdown-item small { 
    display: block; 
    font-size: .68rem; 
    letter-spacing: 2px; 
    text-transform: uppercase; 
    color: var(--muted); 
    margin-top: 4px; 
}
.countdown-done { 
    font-family: 'Playfair Display', serif; 
    font-size: 1.3rem; 
    color: var(--primary); 
}
```

#### Bottom Navbar Styles
```css
.bottom-navbar {
    position: fixed; 
    bottom: 12px; 
    left: 50%;
    transform: translateX(-50%);
    width: calc(100% - 32px); 
    max-width: 460px;
    height: var(--navbar-h);
    background: rgba(74,124,89,.92);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255,255,255,.15);
    border-radius: 18px;
    display: flex; 
    align-items: center; 
    justify-content: space-around;
    padding: 0 8px; 
    z-index: 1000;
    box-shadow: 0 6px 24px rgba(0,0,0,.25);
}

.nav-item {
    display: flex; 
    flex-direction: column; 
    align-items: center; 
    gap: 3px;
    color: rgba(255,255,255,.5); 
    text-decoration: none;
    font-size: .58rem; 
    letter-spacing: 1px; 
    text-transform: uppercase;
    padding: 8px 10px; 
    border-radius: 10px;
    transition: color .2s, background .2s;
    cursor: pointer; 
    background: none; 
    border: none; 
    flex: 1;
}

.nav-item svg { width: 18px; height: 18px; }
.nav-item:hover, .nav-item.active { 
    color: #fff; 
    background: rgba(255,255,255,.12); 
}
```

#### Music FAB Styles
```css
.music-fab {
    position: fixed; 
    bottom: calc(var(--navbar-h) + 18px); 
    right: 16px;
    width: 44px; 
    height: 44px; 
    border-radius: 50%;
    background: var(--primary); 
    border: none; 
    cursor: pointer;
    display: flex; 
    align-items: center; 
    justify-content: center;
    box-shadow: 0 4px 14px rgba(74,124,89,.5); 
    z-index: 999; 
    padding: 0;
}

.music-fab svg { 
    width: 20px; 
    height: 20px; 
    color: #fff; 
    flex-shrink: 0; 
}
.music-fab .icon-disc { display: block; }
.music-fab .icon-play { display: none; }
.music-fab.playing .icon-disc { 
    animation: spin 3s linear infinite; 
}
.music-fab.paused .icon-disc { display: none; }
.music-fab.paused .icon-play { display: block; }

@keyframes spin { 
    to { transform: rotate(360deg); } 
}
```

#### Responsive Design
```css
@media (max-width: 560px) {
    .hero-names { font-size: 2rem; }
    .couple-grid { grid-template-columns: 1fr; }
    .couple-sep { display: none; }
    .event-grid { grid-template-columns: 1fr; }
    .bottom-navbar { width: calc(100% - 20px); }
}
```

---

## Step 5: Create JavaScript File

### File: `public/invitation-assets/[slug]/js/app.js`

```javascript
/**
 * Template: [Name] — app.js v1.0.0
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Reveal on Scroll ──────────────────────────────────────────
    const revealEls = document.querySelectorAll('.reveal');
    if ('IntersectionObserver' in window) {
        const obs = new IntersectionObserver(entries => {
            entries.forEach(e => { 
                if (e.isIntersecting) { 
                    e.target.classList.add('revealed'); 
                    obs.unobserve(e.target); 
                } 
            });
        }, { threshold: 0.12 });
        revealEls.forEach(el => obs.observe(el));
    } else {
        revealEls.forEach(el => el.classList.add('revealed'));
    }

    // ── Countdown Timer ───────────────────────────────────────────
    const countdownEl = document.getElementById('countdown');
    if (countdownEl) {
        const target = new Date(countdownEl.dataset.date).getTime();
        function tick() {
            const diff = target - Date.now();
            if (diff <= 0) { 
                countdownEl.innerHTML = '<span class="countdown-done">Hari Bahagia Telah Tiba 🎉</span>'; 
                return; 
            }
            const d = Math.floor(diff / 86400000);
            const h = Math.floor((diff % 86400000) / 3600000);
            const m = Math.floor((diff % 3600000) / 60000);
            const s = Math.floor((diff % 60000) / 1000);
            countdownEl.innerHTML = `
                <div class="countdown-item"><span>${d}</span><small>Hari</small></div>
                <div class="countdown-item"><span>${h}</span><small>Jam</small></div>
                <div class="countdown-item"><span>${m}</span><small>Menit</small></div>
                <div class="countdown-item"><span>${s}</span><small>Detik</small></div>`;
        }
        tick(); 
        setInterval(tick, 1000);
    }

    // ── Smooth Scroll ─────────────────────────────────────────────
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { 
                e.preventDefault(); 
                t.scrollIntoView({ behavior: 'smooth', block: 'start' }); 
            }
        });
    });

    // ── Active Navigation ─────────────────────────────────────────
    const sections = document.querySelectorAll('section[id]');
    const navItems = document.querySelectorAll('.nav-item[data-section]');
    if (sections.length && 'IntersectionObserver' in window) {
        sections.forEach(s => {
            new IntersectionObserver(entries => {
                entries.forEach(e => {
                    if (e.isIntersecting) {
                        navItems.forEach(n => n.classList.remove('active'));
                        document.querySelector(`.nav-item[data-section="${e.target.id}"]`)?.classList.add('active');
                    }
                });
            }, { threshold: 0.4 }).observe(s);
        });
    }

    // ── Music Player ──────────────────────────────────────────────
    const audio = document.getElementById('bgMusic');
    const fab   = document.getElementById('musicFab');
    if (audio && fab) {
        audio.volume = 0.6;
        function setPlaying(s) { 
            fab.classList.toggle('playing', s); 
            fab.classList.toggle('paused', !s); 
        }
        audio.play().then(() => setPlaying(true)).catch(() => {
            setPlaying(false);
            const start = () => { 
                audio.play().then(() => setPlaying(true)).catch(() => {}); 
            };
            ['click','touchstart','scroll'].forEach(ev => 
                document.addEventListener(ev, start, { once: true })
            );
        });
        fab.addEventListener('click', () => {
            audio.paused 
                ? audio.play().then(() => setPlaying(true)).catch(() => {}) 
                : (audio.pause(), setPlaying(false));
        });
    }

}); // end DOMContentLoaded

// ── Copy to Clipboard ─────────────────────────────────────────────
function copyToClipboard(text, btn) {
    navigator.clipboard.writeText(text).then(() => {
        const orig = btn.textContent;
        btn.textContent = '✅';
        setTimeout(() => btn.textContent = orig, 2000);
    });
}

// ── Lightbox Gallery ──────────────────────────────────────────────
function openLightbox(src, caption) {
    const lb = document.getElementById('lightbox');
    document.getElementById('lightboxImg').src = src;
    lb.classList.add('open');
    document.body.style.overflow = 'hidden';
}
function closeLightbox() {
    document.getElementById('lightbox').classList.remove('open');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { 
    if (e.key === 'Escape') closeLightbox(); 
});

// ── Slideshow (if using slideshow mode) ──────────────────────────
(function () {
    const track = document.getElementById('slideshowTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.slide');
    const dots   = document.querySelectorAll('#slideDots .dot');
    let current  = 0, timer;

    function goToSlide(n) {
        current = (n + slides.length) % slides.length;
        track.style.transform = `translateX(-${current * 100}%)`;
        dots.forEach((d, i) => d.classList.toggle('active', i === current));
    }

    window.goToSlide = goToSlide;
    window.slideNext = () => goToSlide(current + 1);
    window.slidePrev = () => goToSlide(current - 1);

    const container = track.closest('.slideshow');
    const startAuto = () => { 
        timer = setInterval(() => goToSlide(current + 1), 4000); 
    };
    const stopAuto = () => clearInterval(timer);

    container?.addEventListener('mouseenter', stopAuto);
    container?.addEventListener('mouseleave', startAuto);
    container?.addEventListener('touchstart', stopAuto, { passive: true });
    startAuto();
})();
```

---

## Step 6: Create Asset Folders

### Directory Structure
```bash
mkdir -p public/invitation-assets/[slug]/css
mkdir -p public/invitation-assets/[slug]/js
mkdir -p public/invitation-assets/[slug]/images
mkdir -p public/invitation-assets/[slug]/fonts  # optional
```

### Add .gitkeep to images folder
```bash
touch public/invitation-assets/[slug]/images/.gitkeep
```

---

## Step 7: Available Template Fields

### Wedding Standard Preset (21 fields)


| Key | Label | Type | Group | Required |
|-----|-------|------|-------|----------|
| groom_name | Nama Mempelai Pria | text | mempelai | Yes |
| groom_nickname | Nama Panggilan Pria | text | mempelai | No |
| groom_photo | Foto Mempelai Pria | image | mempelai | No |
| groom_father | Nama Ayah Mempelai Pria | text | mempelai | No |
| groom_mother | Nama Ibu Mempelai Pria | text | mempelai | No |
| bride_name | Nama Mempelai Wanita | text | mempelai | Yes |
| bride_nickname | Nama Panggilan Wanita | text | mempelai | No |
| bride_photo | Foto Mempelai Wanita | image | mempelai | No |
| bride_father | Nama Ayah Mempelai Wanita | text | mempelai | No |
| bride_mother | Nama Ibu Mempelai Wanita | text | mempelai | No |
| akad_date | Tanggal Akad | date | acara | Yes |
| akad_time | Waktu Akad | time | acara | Yes |
| akad_venue | Tempat Akad | text | acara | Yes |
| akad_address | Alamat Akad | textarea | acara | No |
| reception_date | Tanggal Resepsi | date | acara | Yes |
| reception_time | Waktu Resepsi | time | acara | Yes |
| reception_venue | Tempat Resepsi | text | acara | Yes |
| reception_address | Alamat Resepsi | textarea | acara | No |
| maps_url | Link Google Maps | url | tambahan | No |
| love_story | Cerita Cinta | textarea | tambahan | No |
| cover_photo | Foto Cover | image | tambahan | No |

### Wedding Simple Preset (12 fields)
| Key | Label | Type | Group | Required |
|-----|-------|------|-------|----------|
| groom_name | Nama Mempelai Pria | text | mempelai | Yes |
| groom_photo | Foto Mempelai Pria | image | mempelai | No |
| bride_name | Nama Mempelai Wanita | text | mempelai | Yes |
| bride_photo | Foto Mempelai Wanita | image | mempelai | No |
| akad_date | Tanggal Akad | date | acara | Yes |
| akad_time | Waktu Akad | time | acara | Yes |
| akad_venue | Tempat Akad | text | acara | Yes |
| reception_date | Tanggal Resepsi | date | acara | Yes |
| reception_time | Waktu Resepsi | time | acara | Yes |
| reception_venue | Tempat Resepsi | text | acara | Yes |
| maps_url | Link Google Maps | url | tambahan | No |
| cover_photo | Foto Cover | image | tambahan | No |

### Field Types
- **text**: Single line text input
- **textarea**: Multi-line text input
- **date**: Date picker
- **time**: Time picker
- **datetime**: Date and time picker
- **image**: Image upload (stored in `storage/`)
- **url**: URL input
- **number**: Numeric input
- **select**: Dropdown selection

---

## Step 8: Accessing Field Data in Blade

### Text Fields
```blade
{{ $data['groom_name'] ?? 'Default Value' }}
```

### Image Fields
```blade
@if(!empty($data['groom_photo']))
    <img src="{{ asset('storage/' . $data['groom_photo']) }}" alt="">
@endif
```

### Date Fields (with Carbon)
```blade
@if(!empty($data['akad_date']))
    {{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('l, d F Y') }}
@endif
```

### Time Fields
```blade
@if(!empty($data['akad_time']))
    {{ $data['akad_time'] }} WIB
@endif
```

### URL Fields
```blade
@if(!empty($data['maps_url']))
    <a href="{{ $data['maps_url'] }}" target="_blank" rel="noopener">
        Lihat di Google Maps
    </a>
@endif
```

### Textarea Fields
```blade
@if(!empty($data['love_story']))
    <p>{{ nl2br(e($data['love_story'])) }}</p>
@endif
```

---

## Step 9: Shared Partials

### Gallery Partial
The system provides a shared gallery component at `resources/views/invitation-templates/_gallery.blade.php`.

**Usage:**
```blade
@include('invitation-templates._gallery', [
    'sectionClass' => 'section-alt',  // Optional CSS class
    'galleryColumns' => 3             // 2 or 3 columns
])
```

**Features:**
- Grid or slideshow display mode
- Lightbox for full-size viewing
- Captions support
- Lazy loading
- Responsive design

### Gift/Bank Account Partial
The system provides a shared gift section at `resources/views/invitation-templates._gift.blade.php`.

**Usage:**
```blade
@include('invitation-templates._gift', [
    'giftSectionClass' => 'section-alt'  // Optional CSS class
])
```

**Features:**
- Multiple bank accounts support
- Dropdown selector for multiple accounts
- Copy to clipboard functionality
- Color-coded bank cards
- Responsive design

---

## Step 10: Testing Checklist

### Before Deployment
- [ ] Run seeder: `php artisan db:seed --class=[TemplateName]Seeder`
- [ ] Verify template appears in admin panel
- [ ] Create test invitation using the template
- [ ] Test all field inputs (text, image, date, time, url)
- [ ] Test image uploads
- [ ] Test gallery display (grid and slideshow modes)
- [ ] Test gift/bank account section
- [ ] Test music player functionality
- [ ] Test countdown timer
- [ ] Test bottom navigation
- [ ] Test smooth scrolling
- [ ] Test active navigation highlighting
- [ ] Test reveal animations
- [ ] Test lightbox gallery
- [ ] Test copy to clipboard
- [ ] Test responsive design on mobile
- [ ] Test on different browsers (Chrome, Firefox, Safari)
- [ ] Verify SEO meta tags
- [ ] Verify Open Graph tags
- [ ] Test social media sharing

### Performance Checks
- [ ] Optimize images (compress, use WebP if possible)
- [ ] Minimize CSS and JS files
- [ ] Test page load speed
- [ ] Verify lazy loading works
- [ ] Check for console errors
- [ ] Verify no broken links

---

## Step 11: Common Patterns & Best Practices

### 1. Always Check for Empty Data
```blade
@if(!empty($data['field_name']))
    {{-- Display content --}}
@endif
```

### 2. Use Fallback Values
```blade
{{ $data['groom_name'] ?? 'Mempelai Pria' }}
```

### 3. Escape User Input
```blade
{{ e($data['user_input']) }}
```

### 4. Use Carbon for Dates
```blade
{{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('l, d F Y') }}
```

### 5. Responsive Images
```blade
<img src="{{ asset('storage/' . $data['photo']) }}" 
     alt="" 
     loading="lazy"
     style="max-width: 100%; height: auto;">
```

### 6. Accessibility
- Use semantic HTML (`<section>`, `<nav>`, `<footer>`)
- Add `aria-label` to buttons and links
- Use `alt` attributes for images
- Ensure keyboard navigation works
- Maintain good color contrast

### 7. Performance
- Use `loading="lazy"` for images
- Minimize CSS and JS
- Use CSS animations instead of JS when possible
- Avoid large background images
- Compress all assets

---

## Step 12: Color Schemes & Design Tips

### Popular Wedding Color Palettes

#### Classic Elegant
```css
--primary: #2c3e50;  /* Dark blue-gray */
--gold: #d4af37;     /* Gold */
--cream: #faf8f5;    /* Cream */
```

#### Romantic Rose
```css
--primary: #c06c84;  /* Rose pink */
--gold: #f8b500;     /* Golden yellow */
--cream: #fff5f7;    /* Light pink */
```

#### Modern Minimalist
```css
--primary: #1a1a1a;  /* Almost black */
--gold: #b8860b;     /* Dark gold */
--cream: #ffffff;    /* Pure white */
```

#### Nature Green
```css
--primary: #4a7c59;  /* Forest green */
--gold: #c9a96e;     /* Tan gold */
--cream: #faf8f5;    /* Cream */
```

#### Royal Purple
```css
--primary: #6a4c93;  /* Purple */
--gold: #ffd700;     /* Gold */
--cream: #f8f4ff;    /* Light lavender */
```

### Typography Recommendations
- **Serif fonts**: Playfair Display, Cormorant, Lora, Crimson Text
- **Sans-serif fonts**: Lato, Montserrat, Open Sans, Raleway
- **Script fonts**: Great Vibes, Dancing Script, Parisienne (use sparingly)

---

## Step 13: Advanced Features (Optional)

### RSVP Section
```blade
<section id="rsvp" class="section reveal">
    <h2 class="section-title">Konfirmasi Kehadiran</h2>
    <div class="divider"></div>
    <p>Mohon konfirmasi kehadiran Anda</p>
    <a href="{{ route('rsvp', $invitation->slug) }}" class="btn-primary-inv">
        Konfirmasi Kehadiran
    </a>
</section>
```

### Love Story Timeline
```blade
@if(!empty($data['love_story']))
<section id="story" class="section section-alt reveal">
    <h2 class="section-title">Cerita Kami</h2>
    <div class="divider"></div>
    <div class="story-content">
        {!! nl2br(e($data['love_story'])) !!}
    </div>
</section>
@endif
```

### Video Background
```blade
<div class="video-bg">
    <video autoplay muted loop playsinline>
        <source src="{{ asset('invitation-assets/[slug]/video/bg.mp4') }}" type="video/mp4">
    </video>
</div>
```

### Parallax Effect
```css
.parallax {
    background-attachment: fixed;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
}
```

---

## Step 14: Deployment Commands

### Run Seeder
```bash
php artisan db:seed --class=[TemplateName]Seeder
```

### Clear Cache
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear
```

### Optimize for Production
```bash
php artisan optimize
php artisan view:cache
php artisan config:cache
```

---

## Step 15: Troubleshooting

### Template Not Showing
- Check `is_active` is `true` in database
- Verify seeder ran successfully
- Clear cache: `php artisan cache:clear`

### Images Not Loading
- Check file path: `asset('storage/' . $data['photo'])`
- Verify storage link: `php artisan storage:link`
- Check file permissions

### CSS/JS Not Loading
- Verify file paths in blade template
- Check asset folder name matches `asset_folder` in database
- Clear browser cache

### Fields Not Saving
- Verify field keys match exactly
- Check field types are correct
- Ensure template_id is set correctly

---

## Complete Example: Creating "Elegant Rose" Template

### 1. Create Seeder
```bash
php artisan make:seeder ElegantRoseSeeder
```

### 2. Edit Seeder File
See Step 2 for complete seeder code.

### 3. Create Directories
```bash
mkdir -p public/invitation-assets/elegant-rose/css
mkdir -p public/invitation-assets/elegant-rose/js
mkdir -p public/invitation-assets/elegant-rose/images
touch public/invitation-assets/elegant-rose/images/.gitkeep
```

### 4. Create Files
- `public/invitation-assets/elegant-rose/css/style.css`
- `public/invitation-assets/elegant-rose/js/app.js`
- `resources/views/invitation-templates/elegant-rose/index.blade.php`

### 5. Run Seeder
```bash
php artisan db:seed --class=ElegantRoseSeeder
```

### 6. Test Template
- Go to admin panel
- Create new invitation
- Select "Elegant Rose" template
- Fill in all fields
- Preview invitation

---

## Summary

Creating a new template requires:
1. Database seeder with template metadata and fields
2. Blade view template with proper structure
3. CSS stylesheet with required classes
4. JavaScript file with interactive features
5. Asset folders for images and fonts
6. Testing on multiple devices and browsers

Follow this guide carefully to ensure your template integrates seamlessly with the existing system. All templates must use the shared gallery and gift partials for consistency.

---

## Support & Resources

- **Template Models**: `app/Models/Template.php`, `app/Models/TemplateField.php`
- **Field Presets**: `app/Support/TemplateFieldPreset.php`
- **Shared Partials**: `resources/views/invitation-templates/_gallery.blade.php`, `_gift.blade.php`
- **Example Templates**: `basic`, `premium-white-1`
- **Controller**: `app/Http/Controllers/TemplateController.php`

---

**Version**: 1.0.0  
**Last Updated**: March 29, 2026  
**System**: Undangan Digital Platform
