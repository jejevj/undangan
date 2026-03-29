

<?php $__env->startSection('title', 'Manajemen Menu'); ?>
<?php $__env->startSection('page-title', 'Manajemen Menu'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Manajemen Menu</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Menu</h4>
                <a href="<?php echo e(route('menus.create')); ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Tambah Menu
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>URL</th>
                                <th>Icon</th>
                                <th>Parent</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i + 1); ?></td>
                                <td><strong><?php echo e($menu->name); ?></strong></td>
                                <td><?php echo e($menu->url ?? '-'); ?></td>
                                <td><i class="<?php echo e($menu->icon); ?>"></i> <small><?php echo e($menu->icon); ?></small></td>
                                <td>-</td>
                                <td><?php echo e($menu->order); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo e($menu->is_active ? 'success' : 'danger'); ?>">
                                        <?php echo e($menu->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('menus.edit', $menu)); ?>" class="btn btn-warning btn-xs me-1">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                    <form action="<?php echo e(route('menus.destroy', $menu)); ?>" method="POST" class="d-inline"
                                        data-confirm="Hapus menu '<?php echo e($menu->name); ?>'?" data-confirm-ok="Hapus" data-confirm-title="Hapus Menu">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php if($menu->children->count()): ?>
                                <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="table-light">
                                    <td></td>
                                    <td class="ps-4">↳ <?php echo e($child->name); ?></td>
                                    <td><?php echo e($child->url ?? '-'); ?></td>
                                    <td><i class="<?php echo e($child->icon); ?>"></i></td>
                                    <td><?php echo e($menu->name); ?></td>
                                    <td><?php echo e($child->order); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo e($child->is_active ? 'success' : 'danger'); ?>">
                                            <?php echo e($child->is_active ? 'Aktif' : 'Nonaktif'); ?>

                                        </span>
                                    </td>
                                    <td>
                                        <a href="<?php echo e(route('menus.edit', $child)); ?>" class="btn btn-warning btn-xs me-1">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <form action="<?php echo e(route('menus.destroy', $child)); ?>" method="POST" class="d-inline"
                                            data-confirm="Hapus menu '<?php echo e($child->name); ?>'?" data-confirm-ok="Hapus" data-confirm-title="Hapus Menu">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="8" class="text-center">Belum ada menu.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/menus/index.blade.php ENDPATH**/ ?>