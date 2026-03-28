
<?php $__env->startSection('title', 'Manajemen Template'); ?>
<?php $__env->startSection('page-title', 'Manajemen Template'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Manajemen Template</li>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<div class="row">
    <?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <?php if($template->thumbnail): ?>
                <img src="<?php echo e(asset('storage/' . $template->thumbnail)); ?>" class="card-img-top" style="height:180px;object-fit:cover" alt="">
            <?php else: ?>
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:180px">
                    <i class="flaticon-381-notepad" style="font-size:3rem;opacity:.3"></i>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title"><?php echo e($template->name); ?></h5>
                <p class="text-muted small mb-1"><?php echo e($template->description); ?></p>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge badge-<?php echo e($template->type === 'free' ? 'success' : ($template->type === 'premium' ? 'warning' : 'info')); ?>">
                        <?php echo e(ucfirst($template->type)); ?>

                    </span>
                    <span class="small fw-bold"><?php echo e($template->formattedPrice()); ?></span>
                    <span class="badge badge-<?php echo e($template->is_active ? 'success' : 'danger'); ?> ms-auto">
                        <?php echo e($template->is_active ? 'Aktif' : 'Nonaktif'); ?>

                    </span>
                </div>
                <p class="text-muted small mt-1">
                    <code><?php echo e($template->asset_folder ?? $template->slug); ?></code>
                    &nbsp;·&nbsp; v<?php echo e($template->version); ?>

                    &nbsp;·&nbsp; <?php echo e($template->invitations_count); ?> undangan
                </p>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="<?php echo e(route('templates.edit', $template)); ?>" class="btn btn-warning btn-sm flex-fill">
                    <i class="fa fa-pencil"></i> Edit & Fields
                </a>
                <form action="<?php echo e(route('templates.toggle', $template)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn btn-<?php echo e($template->is_active ? 'outline-secondary' : 'outline-success'); ?> btn-sm"
                        title="<?php echo e($template->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                        <i class="fa fa-<?php echo e($template->is_active ? 'ban' : 'check'); ?>"></i>
                    </button>
                </form>
                <form action="<?php echo e(route('templates.destroy', $template)); ?>" method="POST"
                    data-confirm="Hapus template '<?php echo e($template->name); ?>'? Semua field terkait akan ikut terhapus." data-confirm-ok="Hapus" data-confirm-title="Hapus Template">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <div class="col-12"><div class="card card-body text-center">Belum ada template.</div></div>
    <?php endif; ?>
</div>
<a href="<?php echo e(route('templates.create')); ?>" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Template</a>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/templates/index.blade.php ENDPATH**/ ?>