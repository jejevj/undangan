
<?php $__env->startSection('title', 'Manajemen User'); ?>
<?php $__env->startSection('page-title', 'Manajemen User'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Manajemen User</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-3">
    <a href="<?php echo e(route('users.create')); ?>" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Tambah User
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Paket</th>
                        <th>Undangan</th>
                        <th>Bergabung</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $plan = $user->isAdmin() ? null : $user->activePlan();
                    ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td>
                            <a href="<?php echo e(route('users.show', $user)); ?>" class="fw-bold text-dark">
                                <?php echo e($user->name); ?>

                            </a>
                        </td>
                        <td><?php echo e($user->email); ?></td>
                        <td>
                            <?php $__currentLoopData = $user->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <span class="badge badge-primary me-1"><?php echo e($role->name); ?></span>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </td>
                        <td>
                            <?php if($user->isAdmin()): ?>
                                <span class="badge badge-dark">Admin</span>
                            <?php elseif($plan): ?>
                                <span class="badge badge-<?php echo e($plan->badge_color); ?>"><?php echo e($plan->name); ?></span>
                            <?php else: ?>
                                <span class="text-muted small">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge badge-light text-dark"><?php echo e($user->invitations_count); ?></span>
                        </td>
                        <td><small class="text-muted"><?php echo e($user->created_at->format('d M Y')); ?></small></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-info btn-xs" title="Detail">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-warning btn-xs" title="Edit">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <?php if($user->id !== auth()->id()): ?>
                                <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="d-inline"
                                    data-confirm="Hapus user '<?php echo e($user->name); ?>'?"
                                    data-confirm-ok="Hapus" data-confirm-title="Hapus User">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="8" class="text-center text-muted py-4">Belum ada user.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/users/index.blade.php ENDPATH**/ ?>