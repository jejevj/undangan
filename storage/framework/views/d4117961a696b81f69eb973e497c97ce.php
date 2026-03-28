
<?php $__env->startSection('title', 'Manajemen Musik'); ?>
<?php $__env->startSection('page-title', 'Manajemen Musik'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Manajemen Musik</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('music.admin.create')); ?>" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Upload Lagu
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Judul</th>
                        <th>Artis</th>
                        <th>Tipe</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Pengguna</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $songs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $song): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td>
                            <div class="fw-bold"><?php echo e($song->title); ?></div>
                            <small class="text-muted"><?php echo e(basename($song->file_path)); ?></small>
                        </td>
                        <td><?php echo e($song->artist ?? '—'); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($song->isFree() ? 'success' : 'warning'); ?>">
                                <?php echo e(ucfirst($song->type)); ?>

                            </span>
                        </td>
                        <td><?php echo e($song->formattedPrice()); ?></td>
                        <td><?php echo e($song->duration ?? '—'); ?></td>
                        <td><?php echo e($song->users_count); ?></td>
                        <td>
                            <span class="badge badge-<?php echo e($song->is_active ? 'success' : 'danger'); ?>">
                                <?php echo e($song->is_active ? 'Aktif' : 'Nonaktif'); ?>

                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <form action="<?php echo e(route('music.admin.toggle', $song)); ?>" method="POST">
                                    <?php echo csrf_field(); ?> <?php echo method_field('PATCH'); ?>
                                    <button class="btn btn-<?php echo e($song->is_active ? 'outline-secondary' : 'outline-success'); ?> btn-xs"
                                        title="<?php echo e($song->is_active ? 'Nonaktifkan' : 'Aktifkan'); ?>">
                                        <i class="fa fa-<?php echo e($song->is_active ? 'ban' : 'check'); ?>"></i>
                                    </button>
                                </form>
                                <form action="<?php echo e(route('music.admin.destroy', $song)); ?>" method="POST"
                                    data-confirm="Hapus lagu '<?php echo e($song->title); ?>'? File audio akan ikut terhapus."
                                    data-confirm-ok="Hapus" data-confirm-title="Hapus Lagu">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada lagu.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/music/admin/index.blade.php ENDPATH**/ ?>