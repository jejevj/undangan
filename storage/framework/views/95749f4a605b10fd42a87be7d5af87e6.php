

<?php $__env->startSection('title', 'Edit Menu'); ?>
<?php $__env->startSection('page-title', 'Edit Menu'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('menus.index')); ?>">Manajemen Menu</a></li>
    <li class="breadcrumb-item active">Edit</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Form Edit Menu: <?php echo e($menu->name); ?></h4></div>
            <div class="card-body">
                <form action="<?php echo e(route('menus.update', $menu)); ?>" method="POST">
                    <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label>Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $menu->name)); ?>" required>
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('slug', $menu->slug)); ?>" required>
                            <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>URL</label>
                            <input type="text" name="url" class="form-control" value="<?php echo e(old('url', $menu->url)); ?>">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Icon Class</label>
                            <input type="text" name="icon" class="form-control" value="<?php echo e(old('icon', $menu->icon)); ?>">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Parent Menu</label>
                            <select name="parent_id" class="form-control">
                                <option value="">-- Tidak ada (menu utama) --</option>
                                <?php $__currentLoopData = $parents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($parent->id); ?>" <?php echo e(old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : ''); ?>><?php echo e($parent->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label>Urutan</label>
                            <input type="number" name="order" class="form-control" value="<?php echo e(old('order', $menu->order)); ?>" min="0">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" <?php echo e(old('is_active', $menu->is_active ? '1' : '0') == '1' ? 'selected' : ''); ?>>Aktif</option>
                                <option value="0" <?php echo e(old('is_active', $menu->is_active ? '1' : '0') == '0' ? 'selected' : ''); ?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Permission (opsional)</label>
                            <select name="permission_name" class="form-control">
                                <option value="">-- Tidak ada --</option>
                                <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($perm->name); ?>" <?php echo e(old('permission_name', $menu->permission_name) == $perm->name ? 'selected' : ''); ?>><?php echo e($perm->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="<?php echo e(route('menus.index')); ?>" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/menus/edit.blade.php ENDPATH**/ ?>