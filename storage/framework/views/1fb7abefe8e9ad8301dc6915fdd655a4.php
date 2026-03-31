
<?php $__env->startSection('title', 'Paket Langganan'); ?>
<?php $__env->startSection('page-title', 'Paket Langganan'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Paket Langganan</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>


<div class="card mb-4">
    <div class="card-body d-flex align-items-center gap-3 flex-wrap">
        <div>
            <span class="text-muted small">Paket Aktif Anda:</span>
            <span class="badge badge-<?php echo e($activePlan->badge_color); ?> ms-2 fs-6"><?php echo e($activePlan->name); ?></span>
            <?php if($activeSub && $activeSub->expires_at): ?>
                <small class="text-muted ms-2">Berlaku hingga <?php echo e($activeSub->expires_at->format('d M Y')); ?></small>
            <?php elseif($activeSub): ?>
                <small class="text-muted ms-2">Selamanya</small>
            <?php endif; ?>
        </div>
        <?php $user = auth()->user(); ?>
        <?php if(!$user->isAdmin()): ?>
        <div class="ms-auto text-end">
            <?php
                $used      = $user->invitationCount();
                $max       = $activePlan->max_invitations;
                $remaining = max(0, $max - $used);
            ?>
            <div class="small text-muted">Undangan: <strong><?php echo e($used); ?></strong> / <?php echo e($max); ?></div>
            <div class="progress mt-1" style="height:5px;width:160px">
                <div class="progress-bar bg-<?php echo e($remaining > 0 ? 'success' : 'danger'); ?>"
                     style="width:<?php echo e(min(100, ($used/$max)*100)); ?>%"></div>
            </div>
            <small class="text-<?php echo e($remaining > 0 ? 'success' : 'danger'); ?>">
                <?php echo e($remaining > 0 ? "Sisa {$remaining} undangan" : 'Limit tercapai'); ?>

            </small>
        </div>
        <?php endif; ?>
    </div>
</div>


<div class="row justify-content-center g-4">
    <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php 
        $isCurrent = $activePlan->id === $plan->id;
        $isLowerTier = $plan->isLowerThan($activePlan);
        $isHigherTier = $plan->isHigherThan($activePlan);
    ?>
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 <?php echo e($plan->is_popular ? 'border-primary' : ''); ?> <?php echo e($isCurrent ? 'border-success' : ''); ?> <?php echo e($isLowerTier ? 'opacity-75' : ''); ?>"
             style="<?php echo e($plan->is_popular ? 'border-width:2px' : ''); ?>">

            <?php if($plan->is_popular && !$isLowerTier): ?>
                <div class="card-header bg-primary text-white text-center py-2">
                    <small class="fw-bold">⭐ PALING POPULER</small>
                </div>
            <?php endif; ?>
            <?php if($isCurrent): ?>
                <div class="card-header bg-success text-white text-center py-2">
                    <small class="fw-bold">✓ PAKET AKTIF ANDA</small>
                </div>
            <?php endif; ?>
            <?php if($isLowerTier && !$isCurrent): ?>
                <div class="card-header bg-secondary text-white text-center py-2">
                    <small class="fw-bold">🔒 PAKET LEBIH RENDAH</small>
                </div>
            <?php endif; ?>
            <?php if($isHigherTier): ?>
                <div class="card-header bg-info text-white text-center py-2">
                    <small class="fw-bold">⬆️ UPGRADE</small>
                </div>
            <?php endif; ?>

            <div class="card-body text-center">
                <h4 class="card-title">
                    <span class="badge badge-<?php echo e($plan->badge_color); ?> mb-2"><?php echo e($plan->name); ?></span>
                </h4>

                <div class="mb-3">
                    <?php if($plan->price === 0): ?>
                        <span class="display-6 fw-bold text-success">Gratis</span>
                    <?php else: ?>
                        <span class="display-6 fw-bold">Rp <?php echo e(number_format($plan->price, 0, ',', '.')); ?></span>
                        <small class="text-muted d-block">sekali bayar</small>
                    <?php endif; ?>
                </div>

                <ul class="list-unstyled text-start mb-4">
                    <?php $__currentLoopData = $plan->features ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="mb-2">
                        <i class="fa fa-check-circle text-success me-2"></i><?php echo e($feature); ?>

                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                
                <div class="bg-light rounded p-3 text-start small mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Undangan</span>
                        <strong><?php echo e($plan->max_invitations); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Foto Galeri</span>
                        <strong><?php echo e($plan->max_gallery_photos ?? 'Unlimited'); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Upload Lagu</span>
                        <strong><?php echo e($plan->max_music_uploads === null ? 'Unlimited' : ($plan->max_music_uploads === 0 ? 'Tidak bisa' : $plan->max_music_uploads . ' lagu')); ?></strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Gift Section</span>
                        <strong class="text-<?php echo e($plan->gift_section_included ? 'success' : 'warning'); ?>">
                            <?php echo e($plan->gift_section_included ? 'Gratis' : 'Rp 10.000'); ?>

                        </strong>
                    </div>
                </div>

                <?php if($isCurrent): ?>
                    <button class="btn btn-success w-100" disabled>Paket Aktif</button>
                <?php elseif($plan->price === 0): ?>
                    <button class="btn btn-outline-secondary w-100" disabled>Paket Default</button>
                <?php elseif($plan->isLowerThan($activePlan)): ?>
                    <button class="btn btn-outline-secondary w-100" disabled>
                        <i class="fa fa-lock me-1"></i>Paket Lebih Rendah
                    </button>
                    <small class="text-muted d-block mt-2">Anda sudah menggunakan paket <?php echo e($activePlan->name); ?></small>
                <?php else: ?>
                    <a href="<?php echo e(route('subscription.checkout', $plan)); ?>" class="btn btn-<?php echo e($plan->badge_color); ?> w-100">
                        <?php if($plan->isHigherThan($activePlan)): ?>
                            <i class="fa fa-arrow-up me-1"></i>Upgrade ke <?php echo e($plan->name); ?>

                        <?php else: ?>
                            Pilih Paket <?php echo e($plan->name); ?>

                        <?php endif; ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/subscription/index.blade.php ENDPATH**/ ?>