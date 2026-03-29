
<?php $__env->startSection('title', 'Upload Lagu'); ?>
<?php $__env->startSection('page-title', 'Upload Lagu Saya'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('music.index')); ?>">Musik</a></li>
    <li class="breadcrumb-item active">Upload</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Upload Lagu Sendiri</h4></div>
            <div class="card-body">

                <div class="alert alert-info mb-4">
                    <i class="fa fa-info-circle"></i>
                    Lagu yang Anda upload hanya bisa digunakan untuk undangan Anda sendiri.
                    Format: <strong>MP3, OGG, WAV</strong> — maks <strong>15MB</strong>.
                </div>

                <div class="alert alert-warning mb-4">
                    <i class="fa fa-money"></i>
                    <strong>Biaya Upload: Rp <?php echo e(number_format($uploadFee, 0, ',', '.')); ?></strong> per lagu.
                    <br>
                    <small>Pembayaran akan dilakukan setelah Anda mengisi form ini.</small>
                </div>

                <form action="<?php echo e(route('music.upload.store')); ?>" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>

                    <div class="form-group mb-3">
                        <label>Judul Lagu <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            value="<?php echo e(old('title')); ?>" required>
                        <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="form-group mb-3">
                        <label>Artis / Penyanyi</label>
                        <input type="text" name="artist" class="form-control" value="<?php echo e(old('artist')); ?>">
                    </div>

                    <div class="form-group mb-3">
                        <label>File Audio <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            accept=".mp3,.ogg,.wav" required id="audioFileInput">
                        <?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        
                        <div id="localPreviewWrap" class="mt-2" style="display:none">
                            <audio id="localPreview" controls class="w-100" style="height:32px"></audio>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-arrow-right"></i> Lanjut ke Pembayaran
                        </button>
                        <a href="<?php echo e(route('music.index')); ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Preview audio sebelum upload
document.getElementById('audioFileInput')?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const url  = URL.createObjectURL(file);
    const wrap = document.getElementById('localPreviewWrap');
    const audio = document.getElementById('localPreview');
    audio.src = url;
    wrap.style.display = '';
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/music/upload.blade.php ENDPATH**/ ?>