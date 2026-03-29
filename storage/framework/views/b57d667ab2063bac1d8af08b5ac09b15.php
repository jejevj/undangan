
<?php if(isset($gallery) && $gallery->count()): ?>
<?php
    $display = $invitation->gallery_display ?? 'grid';
    $cols    = $galleryColumns ?? 3;
?>

<section id="galeri" class="section <?php echo e($sectionClass ?? ''); ?> reveal">
    <p class="t-upper t-muted">Momen Kami</p>
    <h2 class="section-title">Galeri Foto</h2>
    <div class="divider"></div>

    <?php if($display === 'slideshow'): ?>
    
    <div class="slideshow" id="gallerySlideshow">
        <div class="slideshow-track" id="slideshowTrack">
            <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="slide">
                <img src="<?php echo e($photo->url()); ?>" alt="<?php echo e($photo->caption ?? ''); ?>" loading="lazy">
                <?php if($photo->caption): ?>
                    <div class="slide-caption"><?php echo e($photo->caption); ?></div>
                <?php endif; ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
        <button class="slide-btn slide-prev" onclick="slidePrev()" aria-label="Sebelumnya">&#8249;</button>
        <button class="slide-btn slide-next" onclick="slideNext()" aria-label="Berikutnya">&#8250;</button>
        <div class="slide-dots" id="slideDots">
            <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button class="dot <?php echo e($i === 0 ? 'active' : ''); ?>" onclick="goToSlide(<?php echo e($i); ?>)" aria-label="Foto <?php echo e($i+1); ?>"></button>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <?php else: ?>
    
    <div class="gallery-grid" style="grid-template-columns: repeat(<?php echo e($cols); ?>, 1fr)">
        <?php $__currentLoopData = $gallery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="gallery-item" onclick="openLightbox('<?php echo e($photo->url()); ?>', '<?php echo e(addslashes($photo->caption ?? '')); ?>')">
            <img src="<?php echo e($photo->url()); ?>" alt="<?php echo e($photo->caption ?? ''); ?>" loading="lazy">
            <?php if($photo->caption): ?>
                <div class="gallery-caption"><?php echo e($photo->caption); ?></div>
            <?php endif; ?>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>
</section>


<div class="lightbox" id="lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">&times;</button>
    <img id="lightboxImg" src="" alt="">
</div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitation-templates/_gallery.blade.php ENDPATH**/ ?>