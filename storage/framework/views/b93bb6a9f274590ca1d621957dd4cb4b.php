

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Manajemen Kampanye</h4>
                    <a href="<?php echo e(route('admin.campaigns.create')); ?>" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Buat Kampanye Baru
                    </a>
                </div>
                <div class="card-body">
                    <?php if(session('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo e(session('success')); ?>

                    </div>
                    <?php endif; ?>

                    <?php if(session('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo e(session('error')); ?>

                    </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Kampanye</th>
                                    <th>Kode</th>
                                    <th>Plan</th>
                                    <th>Kuota</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $campaigns; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campaign): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e($campaign->name); ?></strong>
                                        <?php if($campaign->description): ?>
                                        <br><small class="text-muted"><?php echo e(Str::limit($campaign->description, 50)); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code><?php echo e($campaign->code); ?></code>
                                        <br>
                                        <small class="text-muted">
                                            <a href="<?php echo e(route('register')); ?>?ref=<?php echo e($campaign->code); ?>" target="_blank" class="text-primary">
                                                <i class="fa fa-link"></i> Lihat URL
                                            </a>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info"><?php echo e($campaign->pricingPlan->name); ?></span>
                                    </td>
                                    <td>
                                        <?php if($campaign->max_users > 0): ?>
                                            <strong><?php echo e($campaign->used_count); ?></strong> / <?php echo e($campaign->max_users); ?>

                                            <br>
                                            <small class="text-muted">Sisa: <?php echo e($campaign->getRemainingSlots()); ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">Unlimited</span>
                                            <br>
                                            <small>Terpakai: <?php echo e($campaign->used_count); ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($campaign->start_date): ?>
                                            <small><?php echo e($campaign->start_date->format('d M Y')); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">-</small>
                                        <?php endif; ?>
                                        <br>
                                        <?php if($campaign->end_date): ?>
                                            <small><?php echo e($campaign->end_date->format('d M Y')); ?></small>
                                        <?php else: ?>
                                            <small class="text-muted">Tanpa batas</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo e($campaign->getStatusBadgeClass()); ?>">
                                            <?php echo e($campaign->getStatusLabel()); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="<?php echo e(route('admin.campaigns.show', $campaign)); ?>" class="btn btn-info" title="Detail">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="<?php echo e(route('admin.campaigns.edit', $campaign)); ?>" class="btn btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="<?php echo e(route('admin.campaigns.toggle-status', $campaign)); ?>" method="POST" class="d-inline">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('PATCH'); ?>
                                                <button type="submit" class="btn btn-<?php echo e($campaign->is_active ? 'secondary' : 'success'); ?>" title="<?php echo e($campaign->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                                    <i class="fa fa-<?php echo e($campaign->is_active ? 'ban' : 'check'); ?>"></i>
                                                </button>
                                            </form>
                                            <form action="<?php echo e(route('admin.campaigns.destroy', $campaign)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kampanye ini?')">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger" title="Hapus" <?php echo e($campaign->users()->count() > 0 ? 'disabled' : ''); ?>>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Belum ada kampanye. <a href="<?php echo e(route('admin.campaigns.create')); ?>">Buat kampanye pertama</a>
                                    </td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php echo e($campaigns->links()); ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/admin/campaigns/index.blade.php ENDPATH**/ ?>