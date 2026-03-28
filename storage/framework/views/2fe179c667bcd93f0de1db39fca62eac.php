
<?php $__env->startSection('title', 'Pilih Musik Undangan'); ?>
<?php $__env->startSection('page-title', 'Musik Undangan'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Musik</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<p class="text-muted mb-4">
    Pilih lagu latar untuk undangan Anda. Lagu gratis langsung bisa digunakan.
    <?php if(!$hasPremiumAccess): ?>
    Lagu premium perlu dibeli terlebih dahulu.
    <?php endif; ?>
</p>

<div class="d-flex gap-2 mb-4 flex-wrap align-items-center">
    <?php 
        $activePlan = auth()->user()->activePlan();
        $user = auth()->user();
        $uploadedCount = \App\Models\Music::where('uploaded_by', $user->id)->count();
        $canUpload = $user->isAdmin() 
                  || $activePlan->max_music_uploads === null 
                  || ($activePlan->max_music_uploads > 0 && $uploadedCount < $activePlan->max_music_uploads);
    ?>
    
    <?php if($hasPremiumAccess): ?>
        <div class="alert alert-success py-2 px-3 mb-0">
            <i class="fa fa-crown"></i>
            Paket <strong><?php echo e($activePlan->name); ?></strong> — Semua lagu premium gratis!
        </div>
    <?php else: ?>
        <div class="alert alert-info py-2 px-3 mb-0">
            <i class="fa fa-info-circle"></i>
            Paket <strong><?php echo e($activePlan->name); ?></strong> — Hanya lagu gratis
        </div>
        <a href="<?php echo e(route('subscription.index')); ?>" class="btn btn-warning btn-sm">
            <i class="fa fa-crown"></i> Upgrade untuk Akses Premium
        </a>
    <?php endif; ?>
    
    <?php if($canUpload): ?>
        <a href="<?php echo e(route('music.upload')); ?>" class="btn btn-primary btn-sm ms-auto">
            <i class="fa fa-upload"></i> Upload Lagu
            <?php if($activePlan->max_music_uploads !== null && $activePlan->max_music_uploads > 0): ?>
                (<?php echo e($uploadedCount); ?>/<?php echo e($activePlan->max_music_uploads); ?>)
            <?php endif; ?>
        </a>
    <?php elseif($activePlan->max_music_uploads === 0): ?>
        <div class="alert alert-warning py-2 px-3 mb-0 ms-auto">
            <i class="fa fa-info-circle"></i>
            Upload lagu tidak tersedia di paket Free
        </div>
    <?php else: ?>
        <div class="alert alert-warning py-2 px-3 mb-0 ms-auto">
            <i class="fa fa-info-circle"></i>
            Limit upload tercapai (<?php echo e($uploadedCount); ?>/<?php echo e($activePlan->max_music_uploads); ?>)
        </div>
    <?php endif; ?>
</div>

<div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $allSongs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $song): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php 
        $owned = $song->isFree() 
              || in_array($song->id, $myIds) 
              || $song->uploaded_by === auth()->id()
              || ($hasPremiumAccess && $song->type === 'premium');
        $canUse = $owned; // Bisa digunakan di undangan
    ?>
    <div class="col-xl-3 col-md-4 col-sm-6 mt-3">
        <div class="card h-100 <?php echo e(!$owned ? 'border-warning' : ''); ?>">
            <div class="card-body">
                
                <div class="d-flex align-items-center gap-3 mb-3">
                    <?php if($song->cover): ?>
                        <img src="<?php echo e(asset('storage/' . $song->cover)); ?>"
                             class="rounded" style="width:48px;height:48px;object-fit:cover" alt="">
                    <?php else: ?>
                        <div class="rounded d-flex align-items-center justify-content-center bg-light"
                             style="width:48px;height:48px;font-size:1.5rem">🎵</div>
                    <?php endif; ?>
                    <div class="flex-fill min-width-0">
                        <div class="fw-bold text-truncate"><?php echo e($song->title); ?></div>
                        <small class="text-muted"><?php echo e($song->artist ?? '—'); ?></small>
                    </div>
                </div>

                
                <audio controls class="w-100 mb-2" style="height:32px">
                    <source src="<?php echo e($song->audioUrl()); ?>" type="audio/mpeg">
                </audio>

                
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge badge-<?php echo e($song->isFree() ? 'success' : 'warning'); ?>">
                        <?php echo e($song->isFree() ? 'Gratis' : 'Premium'); ?>

                    </span>
                    <?php if($song->isUserUpload() && $song->uploaded_by === auth()->id()): ?>
                        <span class="badge badge-info">Upload Saya</span>
                    <?php endif; ?>
                    <?php if(!$song->isFree() && !$hasPremiumAccess): ?>
                        <span class="small fw-bold"><?php echo e($song->formattedPrice()); ?></span>
                    <?php endif; ?>
                    <?php if($song->duration): ?>
                        <span class="text-muted small ms-auto"><?php echo e($song->duration); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card-footer">
                <?php if($canUse): ?>
                    
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm music-url-input"
                               value="<?php echo e($song->audioUrl()); ?>" readonly>
                        <button class="btn btn-outline-secondary btn-sm btn-copy-music"
                                data-url="<?php echo e($song->audioUrl()); ?>"
                                data-title="<?php echo e($song->title); ?>"
                                data-artist="<?php echo e($song->artist); ?>">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <small class="text-success d-block mt-1">
                        <i class="fa fa-check"></i>
                        <?php if($song->isFree()): ?>
                            Gratis — langsung gunakan
                        <?php elseif($song->uploaded_by === auth()->id()): ?>
                            Upload Saya
                        <?php elseif($hasPremiumAccess && $song->type === 'premium'): ?>
                            Premium gratis (Paket <?php echo e($activePlan->name); ?>)
                        <?php else: ?>
                            Sudah dibeli
                        <?php endif; ?>
                    </small>
                <?php else: ?>
                    
                    <a href="<?php echo e(route('music.buy', $song)); ?>" class="btn btn-warning btn-sm w-100">
                        <i class="fa fa-shopping-cart"></i> Beli — <?php echo e($song->formattedPrice()); ?>

                    </a>
                    <small class="text-muted d-block mt-1 text-center">
                        Atau <a href="<?php echo e(route('subscription.index')); ?>">upgrade paket</a> untuk akses gratis
                    </small>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12">
        <div class="card card-body text-center text-muted">Belum ada lagu tersedia.</div>
    </div>
    <?php endif; ?>
</div>


<div class="modal fade" id="copyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div style="font-size:2.5rem">✅</div>
                <h5 class="mt-2">URL Disalin!</h5>
                <p class="text-muted small mb-0">
                    Tempel URL ini ke field <strong>URL Lagu (mp3)</strong> di form undangan Anda.
                </p>
                <div class="mt-3">
                    <small class="text-muted">Judul:</small>
                    <div class="fw-bold" id="copiedTitle"></div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button class="btn btn-primary btn-sm" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.btn-copy-music').forEach(btn => {
    btn.addEventListener('click', function () {
        const url    = this.dataset.url;
        const title  = this.dataset.title;
        const artist = this.dataset.artist;

        navigator.clipboard.writeText(url).then(() => {
            document.getElementById('copiedTitle').textContent = title + (artist ? ' — ' + artist : '');
            new bootstrap.Modal(document.getElementById('copyModal')).show();
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/music/index.blade.php ENDPATH**/ ?>