<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e($invitation->title); ?></title>
    
    
    <meta name="description" content="<?php echo e($data['event_title'] ?? 'Undangan Acara'); ?> - <?php echo e($data['event_description'] ?? ''); ?>">
    <meta name="keywords" content="undangan acara, corporate event, seminar, grand opening">
    
    
    <link rel="canonical" href="<?php echo e($canonicalUrl ?? url()->current()); ?>">
    
    
    <meta property="og:type" content="website">
    <meta property="og:title" content="<?php echo e($invitation->title); ?>">
    <meta property="og:description" content="<?php echo e($data['event_title'] ?? 'Undangan Acara'); ?>">
    <meta property="og:url" content="<?php echo e(url()->current()); ?>">
    <?php if(!empty($data['cover_photo'])): ?>
    <meta property="og:image" content="<?php echo e(asset('storage/' . $data['cover_photo'])); ?>">
    <?php endif; ?>
    
    
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($invitation->title); ?>">
    
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    
    <link href="<?php echo e(asset('invitation-assets/sanno/css/style.css')); ?>" rel="stylesheet">
    
    
    <?php if(auth()->guard()->check()): ?>
    <?php if($invitation->user_id === auth()->id() || auth()->user()->hasRole('admin')): ?>
    <script src="<?php echo e(asset('assets/js/live-edit.js')); ?>" defer></script>
    <?php endif; ?>
    <?php endif; ?>
</head>
<body 
    <?php if(auth()->guard()->check()): ?>
    <?php if($invitation->user_id === auth()->id() || auth()->user()->hasRole('admin')): ?>
    data-invitation-id="<?php echo e($invitation->id); ?>"
    data-is-owner="true"
    <?php endif; ?>
    <?php endif; ?>
>


<section id="hero" class="hero">
    <div class="hero-inner reveal">
        <?php if(!empty($data['company_logo'])): ?>
        <div class="hero-logo"
             data-editable
             data-field-key="company_logo"
             data-field-type="image"
             data-field-label="Logo Perusahaan">
            <img src="<?php echo e(asset('storage/' . $data['company_logo'])); ?>" alt="Logo">
        </div>
        <?php endif; ?>
        
        <p class="hero-label">You are invited to the</p>
        <h1 class="hero-title"
            data-editable
            data-field-key="event_title"
            data-field-type="text"
            data-field-label="Judul Acara">
            <?php echo e($data['event_title'] ?? 'Event Title'); ?>

        </h1>
        
        <?php if(!empty($data['event_subtitle'])): ?>
        <p class="hero-subtitle"
           data-editable
           data-field-key="event_subtitle"
           data-field-type="text"
           data-field-label="Sub Judul">
            <?php echo e($data['event_subtitle']); ?>

        </p>
        <?php endif; ?>
        
        <?php if(!empty($data['event_description'])): ?>
        <p class="hero-description"
           data-editable
           data-field-key="event_description"
           data-field-type="textarea"
           data-field-label="Deskripsi Acara">
            <?php echo e($data['event_description']); ?>

        </p>
        <?php endif; ?>
    </div>
</section>


<section id="event" class="section reveal">
    <p class="t-upper t-muted">Event Details</p>
    <h2 class="section-title">Informasi Acara</h2>
    <div class="divider"></div>
    
    <div class="event-card-wrap">
        <div class="event-card">
            <div class="event-icon">📅</div>
            
            <?php if(!empty($data['event_date'])): ?>
                <div class="event-date"
                     data-editable
                     data-field-key="event_date"
                     data-field-type="date"
                     data-field-label="Tanggal Acara">
                    <?php echo e(\Carbon\Carbon::parse($data['event_date'])->translatedFormat('l, d F Y')); ?>

                </div>
            <?php endif; ?>
            
            <?php if(!empty($data['event_time'])): ?>
                <div class="event-time"
                     data-editable
                     data-field-key="event_time"
                     data-field-type="time"
                     data-field-label="Waktu Acara">
                    <?php echo e($data['event_time']); ?> WIB
                </div>
            <?php endif; ?>
            
            <div class="event-venue"
                 data-editable
                 data-field-key="event_venue"
                 data-field-type="text"
                 data-field-label="Tempat Acara">
                <?php echo e($data['event_venue'] ?? 'Venue'); ?>

            </div>
            
            <?php if(!empty($data['event_address'])): ?>
                <div class="event-address"
                     data-editable
                     data-field-key="event_address"
                     data-field-type="textarea"
                     data-field-label="Alamat Lengkap">
                    <?php echo e($data['event_address']); ?>

                </div>
            <?php endif; ?>
            
            <?php if(!empty($data['maps_url'])): ?>
                <a href="<?php echo e($data['maps_url']); ?>" target="_blank" rel="noopener" class="btn-primary">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="18" height="18">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    Buka Google Maps
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>


<?php if(!empty($data['event_date'])): ?>
<section id="countdown-section" class="section section-alt reveal">
    <p class="t-upper t-muted">Countdown</p>
    <h2 class="section-title">Hitung Mundur</h2>
    <div class="divider"></div>
    <div class="countdown" id="countdown"
         data-date="<?php echo e($data['event_date']); ?> <?php echo e($data['event_time'] ?? '00:00'); ?>"></div>
</section>
<?php endif; ?>



<?php if(isset($gallery) && $gallery->count()): ?>
<section id="galeri" class="section reveal">
    <p class="t-upper t-muted">Gallery</p>
    <h2 class="section-title">Galeri Foto</h2>
    <div class="divider"></div>
    
    <div class="gallery-grid">
        <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="gallery-item" onclick="openLightbox('<?php echo e($photo->url()); ?>', '<?php echo e(addslashes($photo->caption ?? '')); ?>')">
            <img src="<?php echo e($photo->url()); ?>" alt="<?php echo e($photo->caption ?? ''); ?>" loading="lazy">
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>


<div class="lightbox" id="lightbox" onclick="closeLightbox()" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,.9); z-index: 2000; align-items: center; justify-content: center; padding: 20px;">
    <button class="lightbox-close" onclick="closeLightbox()" style="position: absolute; top: 16px; right: 20px; color: #fff; font-size: 2rem; cursor: pointer; background: none; border: none; opacity: .7;">&times;</button>
    <img id="lightboxImg" src="" alt="" style="max-width: 100%; max-height: 90vh; border-radius: 6px; object-fit: contain;">
</div>
<style>
.lightbox.open { display: flex !important; }
</style>
<?php endif; ?>


<section id="rsvp" class="section section-dark reveal">
    <p class="t-upper" style="color: rgba(255,255,255,.6);">Konfirmasi Kehadiran</p>
    <h2 class="section-title">RSVP</h2>
    <div class="divider"></div>
    
    <?php if(!empty($data['rsvp_note'])): ?>
    <p class="section-subtitle" 
       style="color: rgba(255,255,255,.7); margin-bottom: 30px;"
       data-editable
       data-field-key="rsvp_note"
       data-field-type="textarea"
       data-field-label="Catatan RSVP">
        <?php echo e($data['rsvp_note']); ?>

    </p>
    <?php else: ?>
    <p class="section-subtitle" style="color: rgba(255,255,255,.7); margin-bottom: 30px;">
        Mohon konfirmasi kehadiran Anda untuk membantu kami mempersiapkan acara dengan lebih baik.
    </p>
    <?php endif; ?>
    
    <div class="rsvp-form-wrap">
        <form id="rsvpForm" onsubmit="return false;">
            <div class="form-group">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" class="form-input" placeholder="Masukkan nama Anda" required>
            </div>
            
            <div class="form-group">
                <label class="form-label">Instansi / Perusahaan</label>
                <input type="text" class="form-input" placeholder="Nama instansi">
            </div>
            
            <div class="form-group">
                <label class="form-label">Konfirmasi Kehadiran</label>
                <select class="form-select" required>
                    <option value="">Pilih...</option>
                    <option value="hadir">Hadir</option>
                    <option value="tidak-hadir">Tidak Hadir</option>
                    <option value="ragu">Masih Ragu</option>
                </select>
            </div>
            
            <div class="form-group">
                <label class="form-label">Pesan / Ucapan (Opsional)</label>
                <textarea class="form-textarea" placeholder="Tulis pesan Anda..."></textarea>
            </div>
            
            <button type="submit" class="btn-primary" style="width: 100%;">
                Kirim Konfirmasi
            </button>
        </form>
    </div>
</section>


<section id="qrcode" class="section section-alt reveal">
    <p class="t-upper t-muted">Check-in</p>
    <h2 class="section-title">QR Code</h2>
    <div class="divider"></div>
    
    <?php if(!empty($data['qr_note'])): ?>
    <p class="section-subtitle" 
       style="margin-bottom: 30px;"
       data-editable
       data-field-key="qr_note"
       data-field-type="textarea"
       data-field-label="Catatan QR Code">
        <?php echo e($data['qr_note']); ?>

    </p>
    <?php else: ?>
    <p class="section-subtitle" style="margin-bottom: 30px;">
        Setelah mengisi RSVP, Anda akan mendapatkan QR Code untuk check-in dan penukaran souvenir.
    </p>
    <?php endif; ?>
    
    <div class="qr-card">
        <div class="qr-placeholder">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="80" height="80" style="color: #9ca3af;">
                <rect x="3" y="3" width="7" height="7"/>
                <rect x="14" y="3" width="7" height="7"/>
                <rect x="14" y="14" width="7" height="7"/>
                <rect x="3" y="14" width="7" height="7"/>
            </svg>
        </div>
        <p class="qr-note">
            QR Code akan muncul setelah Anda mengisi formulir RSVP di atas.
        </p>
    </div>
</section>


<footer class="inv-footer">
    <?php if(!empty($data['company_logo'])): ?>
    <div class="footer-logo">
        <img src="<?php echo e(asset('storage/' . $data['company_logo'])); ?>" alt="Logo" style="max-width: 100%; filter: brightness(0) invert(1); opacity: .3;">
    </div>
    <?php endif; ?>
    <p><?php echo e($invitation->title); ?></p>
    <p style="margin-top:8px;opacity:.5;font-size:.75rem">
        Dibuat dengan sistem undangan digital
    </p>
</footer>


<?php $musicUrl = $data['music_url'] ?? null; ?>
<?php if($musicUrl): ?>
<button class="music-fab playing" id="musicFab" aria-label="Play/Pause musik">
    <svg class="icon-disc" viewBox="0 0 24 24" fill="currentColor">
        <circle cx="12" cy="12" r="10"/>
        <circle cx="12" cy="12" r="3"/>
    </svg>
    <svg class="icon-play" viewBox="0 0 24 24" fill="currentColor">
        <path d="M8 5v14l11-7z"/>
    </svg>
</button>
<audio id="bgMusic" src="<?php echo e($musicUrl); ?>" loop preload="auto"></audio>
<?php endif; ?>


<?php echo $__env->make('invitation-templates._cta_preview', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


<nav class="bottom-navbar" role="navigation">
    <a href="#hero" class="nav-item active" data-section="hero" aria-label="Home">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
        </svg>
        Home
    </a>
    <a href="#event" class="nav-item" data-section="event" aria-label="Event">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2"/>
            <line x1="16" y1="2" x2="16" y2="6"/>
            <line x1="8" y1="2" x2="8" y2="6"/>
            <line x1="3" y1="10" x2="21" y2="10"/>
        </svg>
        Event
    </a>
    <?php if(isset($gallery) && $gallery->count()): ?>
    <a href="#galeri" class="nav-item" data-section="galeri" aria-label="Gallery">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="3" width="18" height="18" rx="2"/>
            <circle cx="8.5" cy="8.5" r="1.5"/>
            <polyline points="21 15 16 10 5 21"/>
        </svg>
        Gallery
    </a>
    <?php endif; ?>
    <a href="#rsvp" class="nav-item" data-section="rsvp" aria-label="RSVP">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
            <circle cx="8.5" cy="7" r="4"/>
            <line x1="20" y1="8" x2="20" y2="14"/>
            <line x1="23" y1="11" x2="17" y2="11"/>
        </svg>
        RSVP
    </a>
</nav>

<script src="<?php echo e(asset('invitation-assets/sanno/js/app.js')); ?>"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitation-templates/sanno/index.blade.php ENDPATH**/ ?>