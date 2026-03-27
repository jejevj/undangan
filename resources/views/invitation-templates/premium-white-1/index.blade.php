<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invitation->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('invitation-assets/premium-white-1/css/style.css') }}" rel="stylesheet">
</head>
<body>

{{-- ══════════════════════════════════════════════════════════════════
     HERO — floating card, dark background
     ══════════════════════════════════════════════════════════════════ --}}
<section id="hero" class="hero">
    <div class="hero-card reveal">
        <div class="hero-bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
        <p class="hero-label">Undangan Pernikahan</p>
        <h1 class="hero-names">
            {{ $data['groom_nickname'] ?? $data['groom_name'] ?? 'Mempelai Pria' }}
            <span class="hero-ampersand">&</span>
            {{ $data['bride_nickname'] ?? $data['bride_name'] ?? 'Mempelai Wanita' }}
        </h1>
        @if(!empty($data['akad_date']))
            <p class="hero-date">
                {{ \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('d F Y') }}
            </p>
        @endif
    </div>
</section>

{{-- ══════════════════════════════════════════════════════════════════
     MEMPELAI
     ══════════════════════════════════════════════════════════════════ --}}
<section id="mempelai" class="section reveal">
    <p class="t-upper t-muted">Yang Berbahagia</p>
    <h2 class="section-title">Mempelai</h2>
    <div class="divider"></div>
    <div class="couple-grid">
        <div class="couple-card">
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
        <div class="couple-card">
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

{{-- ══════════════════════════════════════════════════════════════════
     ACARA
     ══════════════════════════════════════════════════════════════════ --}}
<section id="acara" class="section section-cream reveal">
    <p class="t-upper t-muted">Rangkaian</p>
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
            <div class="event-address">{{ $data['akad_address'] ?? '' }}</div>
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
            <div class="event-address">{{ $data['reception_address'] ?? '' }}</div>
        </div>
    </div>
    @if(!empty($data['maps_url']))
        <a href="{{ $data['maps_url'] }}" target="_blank" rel="noopener" class="btn-gold" style="margin-top:32px">
            📍 Lihat di Google Maps
        </a>
    @endif
</section>

{{-- ══════════════════════════════════════════════════════════════════
     COUNTDOWN
     ══════════════════════════════════════════════════════════════════ --}}
@if(!empty($data['akad_date']))
<section id="countdown-section" class="section reveal">
    <p class="t-upper t-muted">Hitung Mundur</p>
    <h2 class="section-title">Menuju Hari Bahagia</h2>
    <div class="divider"></div>
    <div class="countdown" id="countdown"
         data-date="{{ $data['akad_date'] }} {{ $data['akad_time'] ?? '00:00' }}"></div>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     GALERI FOTO
     ══════════════════════════════════════════════════════════════════ --}}
@include('invitation-templates._gallery', ['galleryColumns' => 3])

{{-- ══════════════════════════════════════════════════════════════════
     LOVE STORY
     ══════════════════════════════════════════════════════════════════ --}}
@if(!empty($data['love_story']))
<section id="cerita" class="section section-cream reveal">
    <p class="t-upper t-muted">Kisah Kami</p>
    <h2 class="section-title">Cerita Cinta</h2>
    <div class="divider"></div>
    <p style="max-width:520px;margin:20px auto 0;line-height:1.9;color:#666">
        {{ $data['love_story'] }}
    </p>
</section>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     GIFT SECTION
     ══════════════════════════════════════════════════════════════════ --}}
@include('invitation-templates._gift')

{{-- ══════════════════════════════════════════════════════════════════
     FOOTER
     ══════════════════════════════════════════════════════════════════ --}}
<footer class="inv-footer">
    <p>{{ $invitation->title }}</p>
    <p style="margin-top:8px;opacity:.5;font-size:.75rem">Dibuat dengan ❤ menggunakan sistem undangan digital</p>
</footer>

{{-- ══════════════════════════════════════════════════════════════════
     MUSIC FAB — tombol bulat floating, autoplay
     ══════════════════════════════════════════════════════════════════ --}}
@php $musicUrl = $data['music_url'] ?? null; @endphp
@if($musicUrl)
<button class="music-fab playing" id="musicFab" aria-label="Play/Pause musik">
    {{-- Ikon disc (berputar saat playing) --}}
    <svg class="icon-disc" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
    </svg>
    {{-- Ikon play (muncul saat paused) --}}
    <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor">
        <path d="M8 5v14l11-7z"/>
    </svg>
</button>
<audio id="bgMusic" src="{{ $musicUrl }}" loop preload="auto"></audio>
@endif

{{-- ══════════════════════════════════════════════════════════════════
     BOTTOM NAVBAR — floating, tidak full width
     ══════════════════════════════════════════════════════════════════ --}}
<nav class="bottom-navbar" role="navigation" aria-label="Navigasi undangan">

    <a href="#hero" class="nav-item active" data-section="hero" aria-label="Beranda">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>
        </svg>
        Beranda
    </a>

    <a href="#mempelai" class="nav-item" data-section="mempelai" aria-label="Mempelai">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
        Mempelai
    </a>

    <a href="#acara" class="nav-item" data-section="acara" aria-label="Acara">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Acara
    </a>

    @if(!empty($data['love_story']))
    <a href="#cerita" class="nav-item" data-section="cerita" aria-label="Cerita">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Cerita
    </a>
    @endif

    @if(isset($gallery) && $gallery->count())
    <a href="#galeri" class="nav-item" data-section="galeri" aria-label="Galeri">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
        </svg>
        Galeri
    </a>
    @endif

</nav>

<script src="{{ asset('invitation-assets/premium-white-1/js/app.js') }}"></script>
</body>
</html>
