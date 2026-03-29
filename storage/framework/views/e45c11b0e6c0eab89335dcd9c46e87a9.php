
<?php $__env->startSection('title', 'Undangan Saya'); ?>
<?php $__env->startSection('page-title', 'Undangan Saya'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Undangan Saya</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="mb-3 d-flex align-items-center gap-2 flex-wrap">
    <a href="<?php echo e(route('invitations.select-template')); ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> Buat Undangan Baru
    </a>
    <?php $user = auth()->user(); ?>
    <?php if(!$user->isAdmin()): ?>
    <?php $plan = $user->activePlan(); $remaining = $user->remainingInvitations(); ?>
    <span class="badge badge-<?php echo e($plan->badge_color); ?> ms-1"><?php echo e($plan->name); ?></span>
    <span class="text-muted small">
        <?php echo e($user->invitationCount()); ?> / <?php echo e($plan->max_invitations); ?> undangan
        <?php if($remaining <= 0): ?>
            &nbsp;·&nbsp; <a href="<?php echo e(route('subscription.index')); ?>" class="text-warning">Upgrade untuk lebih banyak</a>
        <?php endif; ?>
    </span>
    <?php endif; ?>
</div>
<div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $invitations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $inv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo e($inv->title); ?></h5>
                <p class="text-muted small mb-1">Template: <strong><?php echo e($inv->template->name); ?></strong></p>
                <p class="mb-2">
                    <span class="badge badge-<?php echo e($inv->status === 'published' ? 'success' : ($inv->status === 'expired' ? 'danger' : 'warning')); ?>">
                        <?php echo e(ucfirst($inv->status)); ?>

                    </span>
                </p>
                <?php if($inv->status === 'published'): ?>
                    <p class="small text-muted">
                        Link: <a href="<?php echo e(route('invitation.show', $inv->slug)); ?>" target="_blank"><?php echo e(route('invitation.show', $inv->slug)); ?></a>
                    </p>
                <?php endif; ?>
            </div>
            <div class="card-footer d-flex gap-1 flex-wrap">
                <a href="<?php echo e(route('invitations.edit', $inv)); ?>" class="btn btn-warning btn-xs">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <a href="<?php echo e(route('invitations.preview', $inv)); ?>" class="btn btn-info btn-xs" target="_blank" rel="noopener">
                    <i class="fa fa-eye"></i> Preview
                </a>
                <?php if($inv->status !== 'published'): ?>
                    <form action="<?php echo e(route('invitations.publish', $inv)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-success btn-xs"><i class="fa fa-globe"></i> Publish</button>
                    </form>
                <?php else: ?>
                    <form action="<?php echo e(route('invitations.unpublish', $inv)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-secondary btn-xs">Unpublish</button>
                    </form>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete-invitations')): ?>
                <form action="<?php echo e(route('invitations.destroy', $inv)); ?>" method="POST" class="d-inline"
                    data-confirm="Hapus undangan '<?php echo e($inv->title); ?>'? Semua data tamu akan ikut terhapus." data-confirm-ok="Hapus" data-confirm-title="Hapus Undangan">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12">
        <div class="card card-body text-center">
            <p>Belum ada undangan. <a href="<?php echo e(route('invitations.select-template')); ?>">Buat sekarang</a></p>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitations/index.blade.php ENDPATH**/ ?>