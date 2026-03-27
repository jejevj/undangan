{{--
    Partial galeri foto — dipakai oleh semua template undangan.
    Variabel yang dibutuhkan:
      $gallery         — Collection of InvitationGallery
      $invitation      — Invitation model (untuk gallery_display)
      $galleryColumns  — (opsional) jumlah kolom grid, default 3
--}}
@if(isset($gallery) && $gallery->count())
@php
    $display = $invitation->gallery_display ?? 'grid';
    $cols    = $galleryColumns ?? 3;
@endphp

<section id="galeri" class="section {{ $sectionClass ?? '' }} reveal">
    <p class="t-upper t-muted">Momen Kami</p>
    <h2 class="section-title">Galeri Foto</h2>
    <div class="divider"></div>

    @if($display === 'slideshow')
    {{-- ── SLIDESHOW ──────────────────────────────────────────────── --}}
    <div class="slideshow" id="gallerySlideshow">
        <div class="slideshow-track" id="slideshowTrack">
            @foreach($gallery as $photo)
            <div class="slide">
                <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?? '' }}" loading="lazy">
                @if($photo->caption)
                    <div class="slide-caption">{{ $photo->caption }}</div>
                @endif
            </div>
            @endforeach
        </div>
        <button class="slide-btn slide-prev" onclick="slidePrev()" aria-label="Sebelumnya">&#8249;</button>
        <button class="slide-btn slide-next" onclick="slideNext()" aria-label="Berikutnya">&#8250;</button>
        <div class="slide-dots" id="slideDots">
            @foreach($gallery as $i => $photo)
            <button class="dot {{ $i === 0 ? 'active' : '' }}" onclick="goToSlide({{ $i }})" aria-label="Foto {{ $i+1 }}"></button>
            @endforeach
        </div>
    </div>

    @else
    {{-- ── GRID ───────────────────────────────────────────────────── --}}
    <div class="gallery-grid" style="grid-template-columns: repeat({{ $cols }}, 1fr)">
        @foreach($gallery as $photo)
        <div class="gallery-item" onclick="openLightbox('{{ $photo->url() }}', '{{ addslashes($photo->caption ?? '') }}')">
            <img src="{{ $photo->url() }}" alt="{{ $photo->caption ?? '' }}" loading="lazy">
            @if($photo->caption)
                <div class="gallery-caption">{{ $photo->caption }}</div>
            @endif
        </div>
        @endforeach
    </div>
    @endif
</section>

{{-- Lightbox (shared) --}}
<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <img id="lightboxImg" src="" alt="">
</div>
@endif
