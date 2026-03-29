

<?php $__env->startSection('title', 'Konfigurasi Payment Gateway'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item active">Konfigurasi Payment Gateway</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Konfigurasi Payment Gateway</h4>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment-gateway.create')): ?>
                <a href="<?php echo e(route('payment-gateway.create')); ?>" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Tambah Konfigurasi
                </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if($configs->isEmpty()): ?>
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i> Belum ada konfigurasi payment gateway. 
                        <a href="<?php echo e(route('payment-gateway.create')); ?>">Tambah sekarang</a>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Provider</th>
                                    <th>Environment</th>
                                    <th>Client ID</th>
                                    <th>SNAP API</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $configs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $config): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td>
                                        <strong><?php echo e(strtoupper($config->provider)); ?></strong>
                                    </td>
                                    <td>
                                        <?php if($config->environment === 'sandbox'): ?>
                                            <span class="badge badge-warning">Sandbox</span>
                                        <?php else: ?>
                                            <span class="badge badge-success">Production</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <code class="small"><?php echo e(Str::limit($config->client_id, 20)); ?></code>
                                    </td>
                                    <td>
                                        <?php if($config->private_key && $config->public_key && $config->doku_public_key): ?>
                                            <span class="badge badge-success" title="SNAP API configured">
                                                <i class="fa fa-check"></i> Configured
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary" title="SNAP API not configured">
                                                <i class="fa fa-times"></i> Not Set
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($config->is_active): ?>
                                            <span class="badge badge-success">
                                                <i class="fa fa-check"></i> Aktif
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button type="button" 
                                                    class="btn btn-info test-connection" 
                                                    data-id="<?php echo e($config->id); ?>"
                                                    title="Test Koneksi">
                                                <i class="fa fa-plug"></i>
                                            </button>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment-gateway.edit')): ?>
                                            <a href="<?php echo e(route('payment-gateway.edit', $config)); ?>" 
                                               class="btn btn-primary"
                                               title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <?php endif; ?>
                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('payment-gateway.delete')): ?>
                                            <form action="<?php echo e(route('payment-gateway.destroy', $config)); ?>" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  data-confirm="Hapus konfigurasi ini?"
                                                  data-confirm-title="Konfirmasi Hapus"
                                                  data-confirm-ok="Hapus"
                                                  data-confirm-type="danger">
                                                <?php echo csrf_field(); ?>
                                                <?php echo method_field('DELETE'); ?>
                                                <button type="submit" class="btn btn-danger" title="Hapus">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    $('.test-connection').click(function() {
        const btn = $(this);
        const configId = btn.data('id');
        const originalHtml = btn.html();
        
        btn.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: `/dash/payment-gateway/${configId}/test-connection`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                alert('✅ ' + response.message);
            },
            error: function(xhr) {
                const message = xhr.responseJSON?.message || 'Gagal test koneksi';
                alert('❌ ' + message);
            },
            complete: function() {
                btn.prop('disabled', false).html(originalHtml);
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/payment-gateway/index.blade.php ENDPATH**/ ?>