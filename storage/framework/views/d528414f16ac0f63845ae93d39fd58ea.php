
<?php $__env->startSection('title', 'Edit Paket Pricing'); ?>
<?php $__env->startSection('page-title', 'Edit Paket Pricing'); ?>
<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('pricing-plans.index')); ?>">Paket Pricing</a></li>
    <li class="breadcrumb-item active">Edit</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body">
        <form action="<?php echo e(route('pricing-plans.update', $pricingPlan)); ?>" method="POST">
            <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Paket <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('name', $pricingPlan->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Slug <small class="text-muted">(kosongkan untuk auto-generate)</small></label>
                        <input type="text" name="slug" class="form-control <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('slug', $pricingPlan->slug)); ?>">
                        <?php $__errorArgs = ['slug'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('price', $pricingPlan->price)); ?>" min="0" required>
                        <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="text-muted">Masukkan 0 untuk paket gratis</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Warna Badge <span class="text-danger">*</span></label>
                        <select name="badge_color" class="form-control <?php $__errorArgs = ['badge_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="primary" <?php echo e(old('badge_color', $pricingPlan->badge_color) == 'primary' ? 'selected' : ''); ?>>Primary (Biru)</option>
                            <option value="success" <?php echo e(old('badge_color', $pricingPlan->badge_color) == 'success' ? 'selected' : ''); ?>>Success (Hijau)</option>
                            <option value="warning" <?php echo e(old('badge_color', $pricingPlan->badge_color) == 'warning' ? 'selected' : ''); ?>>Warning (Kuning)</option>
                            <option value="danger" <?php echo e(old('badge_color', $pricingPlan->badge_color) == 'danger' ? 'selected' : ''); ?>>Danger (Merah)</option>
                            <option value="info" <?php echo e(old('badge_color', $pricingPlan->badge_color) == 'info' ? 'selected' : ''); ?>>Info (Cyan)</option>
                            <option value="secondary" <?php echo e(old('badge_color', $pricingPlan->badge_color) == 'secondary' ? 'selected' : ''); ?>>Secondary (Abu)</option>
                        </select>
                        <?php $__errorArgs = ['badge_color'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Undangan <span class="text-danger">*</span></label>
                        <input type="number" name="max_invitations" class="form-control <?php $__errorArgs = ['max_invitations'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('max_invitations', $pricingPlan->max_invitations)); ?>" min="1" required>
                        <?php $__errorArgs = ['max_invitations'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Foto Gallery <span class="text-danger">*</span></label>
                        <input type="number" name="max_gallery_photos" class="form-control <?php $__errorArgs = ['max_gallery_photos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('max_gallery_photos', $pricingPlan->max_gallery_photos)); ?>" min="0" required>
                        <?php $__errorArgs = ['max_gallery_photos'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Upload Musik <span class="text-danger">*</span></label>
                        <input type="number" name="max_music_uploads" class="form-control <?php $__errorArgs = ['max_music_uploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('max_music_uploads', $pricingPlan->max_music_uploads)); ?>" min="0" required>
                        <?php $__errorArgs = ['max_music_uploads'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Fitur Tambahan</label>
                <div class="form-check">
                    <input type="checkbox" name="gift_section_included" value="1" 
                           class="form-check-input" id="gift_section" 
                           <?php echo e(old('gift_section_included', $pricingPlan->gift_section_included) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="gift_section">Fitur Gift/Amplop Digital</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="can_delete_music" value="1" 
                           class="form-check-input" id="delete_music" 
                           <?php echo e(old('can_delete_music', $pricingPlan->can_delete_music) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="delete_music">Dapat Hapus Musik yang Diupload</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_popular" value="1" 
                           class="form-check-input" id="is_popular" 
                           <?php echo e(old('is_popular', $pricingPlan->is_popular) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_popular">Tandai sebagai Popular</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" 
                           class="form-check-input" id="is_active" 
                           <?php echo e(old('is_active', $pricingPlan->is_active) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Daftar Fitur <small class="text-muted">(satu per baris)</small></label>
                <div id="features-container">
                    <?php $features = old('features', $pricingPlan->features ?? []); ?>
                    <?php if(count($features) > 0): ?>
                        <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="input-group mb-2 feature-item">
                            <input type="text" name="features[]" class="form-control" value="<?php echo e($feature); ?>">
                            <button type="button" class="btn btn-danger btn-remove-feature">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php else: ?>
                        <div class="input-group mb-2 feature-item">
                            <input type="text" name="features[]" class="form-control" placeholder="Contoh: Template Premium">
                            <button type="button" class="btn btn-danger btn-remove-feature">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" id="add-feature">
                    <i class="fa fa-plus"></i> Tambah Fitur
                </button>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Update
                </button>
                <a href="<?php echo e(route('pricing-plans.index')); ?>" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('add-feature').addEventListener('click', function() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 feature-item';
    div.innerHTML = `
        <input type="text" name="features[]" class="form-control" placeholder="Masukkan fitur">
        <button type="button" class="btn btn-danger btn-remove-feature">
            <i class="fa fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-feature')) {
        const item = e.target.closest('.feature-item');
        if (document.querySelectorAll('.feature-item').length > 1) {
            item.remove();
        }
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/pricing-plans/edit.blade.php ENDPATH**/ ?>