

<?php $__env->startSection('title', 'Edit Konfigurasi Payment Gateway'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('payment-gateway.index')); ?>">Payment Gateway</a></li>
    <li class="breadcrumb-item active">Edit Konfigurasi</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Konfigurasi Payment Gateway</h4>
            </div>
            <div class="card-body">
                <form action="<?php echo e(route('payment-gateway.update', $config)); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="mb-3">
                        <label class="form-label">Provider <span class="text-danger">*</span></label>
                        <select name="provider" class="form-control <?php $__errorArgs = ['provider'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="doku" <?php echo e($config->provider === 'doku' ? 'selected' : ''); ?>>DOKU</option>
                        </select>
                        <?php $__errorArgs = ['provider'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Environment <span class="text-danger">*</span></label>
                        <select name="environment" class="form-control <?php $__errorArgs = ['environment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="sandbox" <?php echo e($config->environment === 'sandbox' ? 'selected' : ''); ?>>Sandbox (Testing)</option>
                            <option value="production" <?php echo e($config->environment === 'production' ? 'selected' : ''); ?>>Production (Live)</option>
                        </select>
                        <?php $__errorArgs = ['environment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php if($config->environment === 'production'): ?>
                            <div class="alert alert-warning mt-2">
                                <i class="fa fa-exclamation-triangle"></i> Mode Production - Transaksi akan real!
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client ID <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="client_id" 
                               class="form-control <?php $__errorArgs = ['client_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('client_id', $config->client_id)); ?>"
                               required>
                        <?php $__errorArgs = ['client_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Partner Service ID</label>
                        <input type="text" 
                               name="partner_service_id" 
                               class="form-control <?php $__errorArgs = ['partner_service_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('partner_service_id', $config->partner_service_id)); ?>"
                               maxlength="8"
                               placeholder="Contoh: '  888994' (max 7 spasi + 1-8 digit)">
                        <?php $__errorArgs = ['partner_service_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">
                            Partner Service ID untuk Virtual Account (max 7 spasi + 1-8 digit)<br>
                            Format: '  888994' (spasi di depan + digit, total max 8 karakter)<br>
                            Kosongkan untuk auto-generate dari Client ID
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Secret Key</label>
                        <input type="password" 
                               name="secret_key" 
                               class="form-control <?php $__errorArgs = ['secret_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        <?php $__errorArgs = ['secret_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah secret key</small>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">SNAP API Configuration (Optional)</h5>
                    <p class="text-muted small">Field berikut diperlukan untuk menggunakan SNAP API DOKU</p>

                    <div class="mb-3">
                        <label class="form-label">Private Key</label>
                        <textarea name="private_key" 
                                  rows="4"
                                  class="form-control <?php $__errorArgs = ['private_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----&#10;&#10;Kosongkan jika tidak ingin mengubah"><?php echo e(old('private_key')); ?></textarea>
                        <?php $__errorArgs = ['private_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">
                            Private key untuk signature (akan dienkripsi)<br>
                            <?php if($config->private_key): ?>
                                <span class="text-success"><i class="fa fa-check"></i> Private key sudah tersimpan</span>
                            <?php else: ?>
                                <span class="text-muted">Belum ada private key tersimpan</span>
                            <?php endif; ?>
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Public Key</label>
                        <textarea name="public_key" 
                                  rows="4"
                                  class="form-control <?php $__errorArgs = ['public_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----&#10;&#10;Kosongkan jika tidak ingin mengubah"><?php echo e(old('public_key', $config->public_key)); ?></textarea>
                        <?php $__errorArgs = ['public_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">Public key Anda</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">DOKU Public Key</label>
                        <textarea name="doku_public_key" 
                                  rows="4"
                                  class="form-control <?php $__errorArgs = ['doku_public_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                  placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----&#10;&#10;Kosongkan jika tidak ingin mengubah"><?php echo e(old('doku_public_key', $config->doku_public_key)); ?></textarea>
                        <?php $__errorArgs = ['doku_public_key'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">Public key dari DOKU untuk verifikasi callback</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Issuer</label>
                        <input type="text" 
                               name="issuer" 
                               class="form-control <?php $__errorArgs = ['issuer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('issuer', $config->issuer)); ?>"
                               placeholder="Contoh: nama-merchant">
                        <?php $__errorArgs = ['issuer'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">Issuer identifier (optional)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Auth Code</label>
                        <input type="password" 
                               name="auth_code" 
                               class="form-control <?php $__errorArgs = ['auth_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        <?php $__errorArgs = ['auth_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <small class="form-text text-muted">
                            Auth code untuk payment tertentu (akan dienkripsi)<br>
                            <?php if($config->auth_code): ?>
                                <span class="text-success"><i class="fa fa-check"></i> Auth code sudah tersimpan</span>
                            <?php else: ?>
                                <span class="text-muted">Belum ada auth code tersimpan</span>
                            <?php endif; ?>
                        </small>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label">Base URL <span class="text-danger">*</span></label>
                        <input type="url" 
                               name="base_url" 
                               class="form-control <?php $__errorArgs = ['base_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                               value="<?php echo e(old('base_url', $config->base_url)); ?>"
                               required>
                        <?php $__errorArgs = ['base_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   class="form-check-input" 
                                   id="is_active"
                                   <?php echo e(old('is_active', $config->is_active) ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">
                                Aktifkan konfigurasi ini
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="<?php echo e(route('payment-gateway.index')); ?>" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Dibuat:</td>
                        <td><?php echo e($config->created_at->format('d M Y H:i')); ?></td>
                    </tr>
                    <tr>
                        <td>Diupdate:</td>
                        <td><?php echo e($config->updated_at->format('d M Y H:i')); ?></td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            <?php if($config->is_active): ?>
                                <span class="badge badge-success">Aktif</span>
                            <?php else: ?>
                                <span class="badge badge-secondary">Nonaktif</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>

                <hr>

                <h6>SNAP API Status:</h6>
                <ul class="small list-unstyled">
                    <li>
                        <?php if($config->private_key): ?>
                            <i class="fa fa-check text-success"></i> Private Key
                        <?php else: ?>
                            <i class="fa fa-times text-muted"></i> Private Key
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if($config->public_key): ?>
                            <i class="fa fa-check text-success"></i> Public Key
                        <?php else: ?>
                            <i class="fa fa-times text-muted"></i> Public Key
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if($config->doku_public_key): ?>
                            <i class="fa fa-check text-success"></i> DOKU Public Key
                        <?php else: ?>
                            <i class="fa fa-times text-muted"></i> DOKU Public Key
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if($config->issuer): ?>
                            <i class="fa fa-check text-success"></i> Issuer
                        <?php else: ?>
                            <i class="fa fa-times text-muted"></i> Issuer
                        <?php endif; ?>
                    </li>
                    <li>
                        <?php if($config->auth_code): ?>
                            <i class="fa fa-check text-success"></i> Auth Code
                        <?php else: ?>
                            <i class="fa fa-times text-muted"></i> Auth Code
                        <?php endif; ?>
                    </li>
                </ul>

                <hr>

                <div class="alert alert-info small">
                    <i class="fa fa-info-circle"></i> Data sensitif (Secret Key, Private Key, Auth Code) tersimpan terenkripsi di database.
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/payment-gateway/edit.blade.php ENDPATH**/ ?>