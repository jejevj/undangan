
<?php $__env->startSection('title', 'Edit Undangan'); ?>
<?php $__env->startSection('page-title', 'Edit Undangan'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('invitations.index')); ?>">Undangan Saya</a></li>
    <li class="breadcrumb-item active">Edit</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="d-flex gap-2 mb-3">
    <a href="<?php echo e(route('invitations.preview', $invitation)); ?>" class="btn btn-info btn-sm" target="_blank" rel="noopener">
        <i class="fa fa-eye"></i> Preview
    </a>
    <a href="<?php echo e(route('invitations.guests.index', $invitation)); ?>" class="btn btn-secondary btn-sm">
        <i class="fa fa-users"></i> Kelola Tamu
        <?php if($invitation->guests()->count()): ?>
            <span class="badge badge-light ms-1"><?php echo e($invitation->guests()->count()); ?></span>
        <?php endif; ?>
    </a>
    <a href="<?php echo e(route('invitations.gallery.index', $invitation)); ?>" class="btn btn-secondary btn-sm">
        <i class="fa fa-image"></i> Galeri Foto
        <?php $galleryCount = $invitation->gallery()->count(); ?>
        <?php if($galleryCount): ?>
            <span class="badge badge-light ms-1"><?php echo e($galleryCount); ?></span>
        <?php endif; ?>
    </a>
    <a href="<?php echo e(route('invitations.gift.index', $invitation)); ?>" class="btn btn-secondary btn-sm">
        <i class="fa fa-gift"></i> Gift Section
        <?php if($invitation->isGiftActive()): ?>
            <span class="badge badge-success ms-1"><?php echo e($invitation->bankAccounts()->count()); ?></span>
        <?php else: ?>
            <span class="badge badge-warning ms-1">Locked</span>
        <?php endif; ?>
    </a>
    <?php if($invitation->status !== 'published'): ?>
        <form action="<?php echo e(route('invitations.publish', $invitation)); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <button class="btn btn-success btn-sm"><i class="fa fa-globe"></i> Publish</button>
        </form>
    <?php else: ?>
        <span class="badge badge-success align-self-center">Published</span>
        <a href="<?php echo e(route('invitation.show', $invitation->slug)); ?>" class="btn btn-outline-success btn-sm" target="_blank">
            <i class="fa fa-external-link-alt"></i> Lihat Link Publik
        </a>
        <form action="<?php echo e(route('invitations.unpublish', $invitation)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengubah status ke draft? Undangan tidak akan bisa diakses publik.')">
            <?php echo csrf_field(); ?>
            <button class="btn btn-warning btn-sm"><i class="fa fa-undo"></i> Unpublish (Kembali ke Draft)</button>
        </form>
    <?php endif; ?>
</div>

<form action="<?php echo e(route('invitations.update', $invitation)); ?>" method="POST" enctype="multipart/form-data">
    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>

    <div class="card mb-4">
        <div class="card-header"><h4 class="card-title">Judul Undangan</h4></div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label>Judul <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                    value="<?php echo e(old('title', $invitation->title)); ?>" required>
                <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div class="form-group mb-0">
                <label>Tampilan Galeri Foto</label>
                <div class="d-flex gap-3 mt-1">
                    <div class="form-check">
                        <input type="radio" name="gallery_display" class="form-check-input" id="gd_grid"
                            value="grid" <?php echo e(old('gallery_display', $invitation->gallery_display ?? 'grid') === 'grid' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="gd_grid">
                            <i class="fa fa-th"></i> Grid View
                            <small class="text-muted d-block">Foto ditampilkan dalam grid kotak</small>
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="gallery_display" class="form-check-input" id="gd_slideshow"
                            value="slideshow" <?php echo e(old('gallery_display', $invitation->gallery_display ?? 'grid') === 'slideshow' ? 'checked' : ''); ?>>
                        <label class="form-check-label" for="gd_slideshow">
                            <i class="fa fa-play-circle"></i> Slideshow
                            <small class="text-muted d-block">Foto ditampilkan bergantian otomatis</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php echo $__env->make('invitations._fields', ['fieldsByGroup' => $fieldsByGroup, 'existingData' => $existingData], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="<?php echo e(route('invitations.index')); ?>" class="btn btn-secondary">Kembali</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitations/edit.blade.php ENDPATH**/ ?>