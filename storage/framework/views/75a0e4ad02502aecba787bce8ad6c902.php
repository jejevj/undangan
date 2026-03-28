

<?php $__env->startSection('title', 'Dashboard'); ?>
<?php $__env->startSection('page-title', 'Dashboard'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Dashboard</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-primary text-primary">
                        <i class="flaticon-381-user-7"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1">Total User</p>
                        <h4 class="mb-0"><?php echo e(\App\Models\User::count()); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-warning text-warning">
                        <i class="flaticon-381-settings-2"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1">Total Role</p>
                        <h4 class="mb-0"><?php echo e(\Spatie\Permission\Models\Role::count()); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-success text-success">
                        <i class="flaticon-381-layer-1"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1">Total Permission</p>
                        <h4 class="mb-0"><?php echo e(\Spatie\Permission\Models\Permission::count()); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-xxl-3 col-sm-6">
        <div class="widget-stat card">
            <div class="card-body p-4">
                <div class="media ai-icon">
                    <span class="me-3 bgl-info text-info">
                        <i class="flaticon-381-internet"></i>
                    </span>
                    <div class="media-body">
                        <p class="mb-1">Total Menu</p>
                        <h4 class="mb-0"><?php echo e(\App\Models\Menu::count()); ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Selamat Datang, <?php echo e(auth()->user()->name); ?></h4>
            </div>
            <div class="card-body">
                <p>Anda login sebagai: <strong><?php echo e(auth()->user()->roles->pluck('name')->join(', ') ?: 'Tidak ada role'); ?></strong></p>
                <p>Gunakan menu di sidebar untuk mengelola sistem undangan.</p>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/dashboard.blade.php ENDPATH**/ ?>