<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($invitation->title); ?></title>
    
    
    <meta name="description" content="Undangan pernikahan <?php echo e($data['groom_name'] ?? 'Mempelai Pria'); ?> & <?php echo e($data['bride_name'] ?? 'Mempelai Wanita'); ?>. <?php echo e(!empty($data['akad_date']) ? 'Tanggal: ' . \Carbon\Carbon::parse($data['akad_date'])->translatedFormat('d F Y') : ''); ?>">
    <meta name="keywords" content="undangan pernikahan, wedding invitation, <?php echo e($data['groom_name'] ?? ''); ?>, <?php echo e($data['bride_name'] ?? ''); ?>">
    
    
    <link rel="canonical" href="<?php echo e($canonicalUrl ?? url()->current()); ?>">
    
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($invitation->title); ?>">
    <meta property="og:description" content="Undangan pernikahan <?php echo e($data['groom_name'] ?? 'Mempelai Pria'); ?> & <?php echo e($data['bride_name'] ?? 'Mempelai Wanita'); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <?php if(!empty($data['groom_photo'])): ?>
    <meta property="og:image" content="<?php echo e(asset('storage/' . $data['groom_photo'])); ?>">
    <?php elseif(!empty($data['bride_photo'])): ?>
    <meta property="og:image" content="<?php echo e(asset('storage/' . $data['bride_photo'])); ?>">
    <?php endif; ?>
    
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($invitation->title); ?>">
    <meta name="twitter:description" content="Undangan pernikahan <?php echo e($data['groom_name'] ?? 'Mempelai Pria'); ?> & <?php echo e($data['bride_name'] ?? 'Mempelai Wanita'); ?>">
    <?php if(!empty($data['groom_photo'])): ?>
    <meta name="twitter:image" content="<?php echo e(asset('storage/' . $data['groom_photo'])); ?>">
    <?php elseif(!empty($data['bride_photo'])): ?>
    <meta name="twitter:image" content="<?php echo e(asset('storage/' . $data['bride_photo'])); ?>">
    <?php endif; ?>
    
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Lato:wght@300;400;700&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('invitation-assets/premium-white-1/css/style.css')); ?>" rel="stylesheet">
</head>
<body>


<section id="hero" class="hero">
    <div class="hero-card reveal">
        <div class="hero-bismillah">بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيم</div>
        <p class="hero-label">Undangan Pernikahan</p>
        <h1 class="hero-names">
            <span data-editable data-field-key="groom_nickname" data-field-type="text" data-field-label="Nama Panggilan Pria"><?php echo e($data['groom_nickname'] ?? $data['groom_name'] ?? 'Mempelai Pria'); ?></span>
            <span class="hero-ampersand">&</span>
            <span data-editable data-field-key="bride_nickname" data-field-type="text" data-field-label="Nama Panggilan Wanita"><?php echo e($data['bride_nickname'] ?? $data['bride_name'] ?? 'Mempelai Wanita'); ?></span>
        </h1>
        <?php if(!empty($data['akad_date'])): ?>
            <p class="hero-date">
                <?php echo e(\Carbon\Carbon::parse($data['akad_date'])->translatedFormat('d F Y')); ?>

            </p>
        <?php endif; ?>
    </div>
</section>


<section id="mempelai" class="section reveal">
    <p class="t-upper t-muted">Yang Berbahagia</p>
    <h2 class="section-title">Mempelai</h2>
    <div class="divider"></div>
    <div class="couple-grid">
        <div class="couple-card">
            <?php if(!empty($data['groom_photo'])): ?>
                <div data-editable data-field-key="groom_photo" data-field-type="image" data-field-label="Foto Mempelai Pria">
                    <img src="<?php echo e(asset('storage/' . $data['groom_photo'])); ?>" class="couple-photo" alt="">
                </div>
            <?php else: ?>
                <div class="couple-photo-placeholder">♂</div>
            <?php endif; ?>
            <div class="couple-name" data-editable data-field-key="groom_name" data-field-type="text" data-field-label="Nama Lengkap Pria"><?php echo e($data['groom_name'] ?? '-'); ?></div>
            <div class="couple-parents">
                Putra dari<br>
                <span data-editable data-field-key="groom_father" data-field-type="text" data-field-label="Nama Ayah Pria"><?php echo e($data['groom_father'] ?? ''); ?></span>
                <?php if(!empty($data['groom_father']) && !empty($data['groom_mother'])): ?> & <?php endif; ?>
                <span data-editable data-field-key="groom_mother" data-field-type="text" data-field-label="Nama Ibu Pria"><?php echo e($data['groom_mother'] ?? ''); ?></span>
            </div>
        </div>
        <div class="couple-sep">&</div>
        <div class="couple-card">
            <?php if(!empty($data['bride_photo'])): ?>
                <div data-editable data-field-key="bride_photo" data-field-type="image" data-field-label="Foto Mempelai Wanita">
                    <img src="<?php echo e(asset('storage/' . $data['bride_photo'])); ?>" class="couple-photo" alt="">
                </div>
            <?php else: ?>
                <div class="couple-photo-placeholder">♀</div>
            <?php endif; ?>
            <div class="couple-name" data-editable data-field-key="bride_name" data-field-type="text" data-field-label="Nama Lengkap Wanita"><?php echo e($data['bride_name'] ?? '-'); ?></div>
            <div class="couple-parents">
                Putri dari<br>
                <span data-editable data-field-key="bride_father" data-field-type="text" data-field-label="Nama Ayah Wanita"><?php echo e($data['bride_father'] ?? ''); ?></span>
                <?php if(!empty($data['bride_father']) && !empty($data['bride_mother'])): ?> & <?php endif; ?>
                <span data-editable data-field-key="bride_mother" data-field-type="text" data-field-label="Nama Ibu Wanita"><?php echo e($data['bride_mother'] ?? ''); ?></span>
            </div>
        </div>
    </div>
</section>


<section id="acara" class="section section-cream reveal">
    <p class="t-upper t-muted">Rangkaian</p>
    <h2 class="section-title">Acara</h2>
    <div class="divider"></div>
    <div class="event-grid">
        <div class="event-card">
            <h3>Akad Nikah</h3>
            <?php if(!empty($data['akad_date'])): ?>
                <div class="event-date" data-editable data-field-key="akad_date" data-field-type="date" data-field-label="Tanggal Akad"><?php echo e(\Carbon\Carbon::parse($data['akad_date'])->translatedFormat('l, d F Y')); ?></div>
            <?php endif; ?>
            <?php if(!empty($data['akad_time'])): ?>
                <div class="event-time" data-editable data-field-key="akad_time" data-field-type="time" data-field-label="Waktu Akad"><?php echo e($data['akad_time']); ?> WIB</div>
            <?php endif; ?>
            <div class="event-venue" data-editable data-field-key="akad_venue" data-field-type="text" data-field-label="Tempat Akad"><?php echo e($data['akad_venue'] ?? ''); ?></div>
            <div class="event-address" data-editable data-field-key="akad_address" data-field-type="textarea" data-field-label="Alamat Akad"><?php echo e($data['akad_address'] ?? ''); ?></div>
        </div>
        <div class="event-card">
            <h3>Resepsi</h3>
            <?php if(!empty($data['reception_date'])): ?>
                <div class="event-date" data-editable data-field-key="reception_date" data-field-type="date" data-field-label="Tanggal Resepsi"><?php echo e(\Carbon\Carbon::parse($data['reception_date'])->translatedFormat('l, d F Y')); ?></div>
            <?php endif; ?>
            <?php if(!empty($data['reception_time'])): ?>
                <div class="event-time" data-editable data-field-key="reception_time" data-field-type="time" data-field-label="Waktu Resepsi"><?php echo e($data['reception_time']); ?> WIB</div>
            <?php endif; ?>
            <div class="event-venue" data-editable data-field-key="reception_venue" data-field-type="text" data-field-label="Tempat Resepsi"><?php echo e($data['reception_venue'] ?? ''); ?></div>
            <div class="event-address" data-editable data-field-key="reception_address" data-field-type="textarea" data-field-label="Alamat Resepsi"><?php echo e($data['reception_address'] ?? ''); ?></div>
        </div>
    </div>
    <?php if(!empty($data['maps_url'])): ?>
        <a href="<?php echo e($data['maps_url']); ?>" target="_blank" rel="noopener" class="btn-gold" style="margin-top:32px">
            📍 Lihat di Google Maps
        </a>
    <?php endif; ?>
</section>


<?php if(!empty($data['akad_date'])): ?>
<section id="countdown-section" class="section reveal">
    <p class="t-upper t-muted">Hitung Mundur</p>
    <h2 class="section-title">Menuju Hari Bahagia</h2>
    <div class="divider"></div>
    <div class="countdown" id="countdown"
         data-date="<?php echo e($data['akad_date']); ?> <?php echo e($data['akad_time'] ?? '00:00'); ?>"></div>
</section>
<?php endif; ?>


<?php echo $__env->make('invitation-templates._gallery', ['galleryColumns' => 3], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<?php if(!empty($data['love_story'])): ?>
<section id="cerita" class="section section-cream reveal">
    <p class="t-upper t-muted">Kisah Kami</p>
    <h2 class="section-title">Cerita Cinta</h2>
    <div class="divider"></div>
    <p style="max-width:520px;margin:20px auto 0;line-height:1.9;color:#666" data-editable data-field-key="love_story" data-field-type="textarea" data-field-label="Cerita Cinta">
        <?php echo e($data['love_story']); ?>

    </p>
</section>
<?php endif; ?>


<?php echo $__env->make('invitation-templates._gift', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<footer class="inv-footer">
    <p><?php echo e($invitation->title); ?></p>
    <p style="margin-top:8px;opacity:.5;font-size:.75rem">Dibuat dengan ❤ menggunakan sistem undangan digital</p>
</footer>


<?php $musicUrl = $data['music_url'] ?? null; ?>
<?php if($musicUrl): ?>
<button class="music-fab playing" id="musicFab" aria-label="Play/Pause musik">
    
    <svg class="icon-disc" viewBox="0 0 24 24" fill="currentColor">
        <path d="M12 3v10.55A4 4 0 1 0 14 17V7h4V3h-6z"/>
    </svg>
    
    <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor">
        <path d="M8 5v14l11-7z"/>
    </svg>
</button>
<audio id="bgMusic" src="<?php echo e($musicUrl); ?>" loop preload="auto"></audio>
<?php endif; ?>


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

    <?php if(!empty($data['love_story'])): ?>
    <a href="#cerita" class="nav-item" data-section="cerita" aria-label="Cerita">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
        Cerita
    </a>
    <?php endif; ?>

    <?php if(isset($gallery) && $gallery->count()): ?>
    <a href="#galeri" class="nav-item" data-section="galeri" aria-label="Galeri">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>
        </svg>
        Galeri
    </a>
    <?php endif; ?>

</nav>

<script src="<?php echo e(asset('invitation-assets/premium-white-1/js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitation-templates/premium-white-1/index.blade.php ENDPATH**/ ?>