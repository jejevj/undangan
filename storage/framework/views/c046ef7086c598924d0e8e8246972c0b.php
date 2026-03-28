
<?php $__env->startSection('title', 'Manajemen Paket Pricing'); ?>
<?php $__env->startSection('page-title', 'Manajemen Paket Pricing'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Manajemen Paket Pricing</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('pricing-plans.create')); ?>" class="btn btn-primary">
        <i class="fa fa-plus"></i> Tambah Paket Baru
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Slug</th>
                        <th>Harga</th>
                        <th>Max Undangan</th>
                        <th>Max Foto</th>
                        <th>Max Musik</th>
                        <th>Fitur Gift</th>
                        <th>Subscribers</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <strong><?php echo e($plan->name); ?></strong>
                            <?php if($plan->is_popular): ?>
                                <span class="badge badge-warning ms-1">Popular</span>
                            <?php endif; ?>
                        </td>
                        <td><code><?php echo e($plan->slug); ?></code></td>
                        <td><?php echo e($plan->formattedPrice()); ?></td>
                        <td><?php echo e($plan->max_invitations); ?></td>
                        <td><?php echo e($plan->max_gallery_photos); ?></td>
                        <td><?php echo e($plan->max_music_uploads); ?></td>
                        <td>
                            <?php if($plan->gift_section_included): ?>
                                <span class="badge badge-success">Ya</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Tidak</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-info"><?php echo e($plan->subscriptions_count); ?></span>
                        </td>
                        <td>
                            <?php if($plan->is_active): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('pricing-plans.edit', $plan)); ?>" class="btn btn-warning btn-xs">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('pricing-plans.toggle', $plan)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button class="btn btn-<?php echo e($plan->is_active ? 'secondary' : 'success'); ?> btn-xs" 
                                            title="<?php echo e($plan->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                        <i class="fa fa-<?php echo e($plan->is_active ? 'eye-slash' : 'eye'); ?>"></i>
                                    </button>
                                </form>
                                <form action="<?php echo e(route('pricing-plans.destroy', $plan)); ?>" method="POST" class="d-inline"
                                    data-confirm="Hapus paket '<?php echo e($plan->name); ?>'?" 
                                    data-confirm-ok="Hapus" 
                                    data-confirm-title="Hapus Paket">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-danger btn-xs">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="10" class="text-center">Belum ada paket pricing.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/pricing-plans/index.blade.php ENDPATH**/ ?>