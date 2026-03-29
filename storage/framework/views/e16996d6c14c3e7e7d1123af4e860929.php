
<?php $__env->startSection('title', 'Checkout Slot Upload Musik'); ?>
<?php $__env->startSection('page-title', 'Checkout Slot Upload Musik'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('music.slots.buy')); ?>">Beli Slot Upload Musik</a></li>
    <li class="breadcrumb-item active">Checkout</li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.payment-method-tabs .nav-link {
    border-radius: 8px 8px 0 0;
    font-weight: 500;
}
.payment-method-tabs .nav-link.active {
    background-color: #0d6efd;
    color: white;
    border-color: #0d6efd;
}
.payment-method-tabs .nav-link.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    pointer-events: none;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-center bg-primary text-white">
                <h4 class="card-title mb-0">Pembayaran Slot Upload Musik</h4>
            </div>
            <div class="card-body">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="text-center mb-3">
                            <span class="badge bg-info text-white" style="font-size:1.1rem;padding:8px 20px">
                                <i class="fa fa-music"></i> Music Upload
                            </span>
                        </div>

                        <table class="table table-sm">
                            <tr><td class="text-muted">No. Order</td><td class="fw-bold"><?php echo e($order->order_number); ?></td></tr>
                            <tr><td class="text-muted">Item</td><td>Slot Upload Musik</td></tr>
                            <tr><td class="text-muted">Jumlah</td><td class="fw-bold"><?php echo e($qty); ?> slot</td></tr>
                            <tr><td class="text-muted">Harga per slot</td><td>Rp <?php echo e(number_format($pricePerSlot, 0, ',', '.')); ?></td></tr>
                            <tr class="table-active">
                                <td class="fw-bold">Total Pembayaran</td>
                                <td class="fw-bold text-success fs-5">Rp <?php echo e(number_format($subtotal, 0, ',', '.')); ?></td>
                            </tr>
                        </table>

                        <div class="alert alert-info small">
                            <i class="fa fa-info-circle"></i>
                            Slot upload musik akan ditambahkan ke akun Anda dan bisa digunakan untuk upload lagu sendiri.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <?php
                            $hasActiveVA = isset($virtualAccount) && $virtualAccount;
                            $hasActiveQris = isset($qrisPayment) && $qrisPayment;
                            $hasAnyActivePayment = $hasActiveVA || $hasActiveQris;
                        ?>

                        <?php if($hasAnyActivePayment): ?>
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-info-circle"></i>
                            <strong>Perhatian:</strong> Anda sudah memiliki pembayaran yang aktif. Selesaikan pembayaran tersebut terlebih dahulu.
                        </div>
                        <?php endif; ?>

                        
                        <ul class="nav nav-tabs payment-method-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo e($hasActiveVA || !$hasAnyActivePayment ? 'active' : ''); ?> <?php echo e($hasAnyActivePayment && !$hasActiveVA ? 'disabled' : ''); ?>" 
                                        id="va-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#va-content" 
                                        type="button" 
                                        role="tab"
                                        <?php echo e($hasAnyActivePayment && !$hasActiveVA ? 'disabled' : ''); ?>>
                                    <i class="fa fa-university"></i> Virtual Account
                                </button>
                            </li>
                            <?php if(isset($qrisEnabled) && $qrisEnabled): ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo e($hasActiveQris ? 'active' : ''); ?> <?php echo e($hasAnyActivePayment && !$hasActiveQris ? 'disabled' : ''); ?>" 
                                        id="qris-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#qris-content" 
                                        type="button" 
                                        role="tab"
                                        <?php echo e($hasAnyActivePayment && !$hasActiveQris ? 'disabled' : ''); ?>>
                                    <i class="fa fa-qrcode"></i> QRIS
                                </button>
                            </li>
                            <?php else: ?>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link disabled" type="button" disabled>
                                    <i class="fa fa-qrcode"></i> QRIS (Belum Aktif)
                                </button>
                            </li>
                            <?php endif; ?>
                        </ul>

                        <div class="tab-content">
                            
                            <div class="tab-pane fade <?php echo e($hasActiveVA || !$hasAnyActivePayment ? 'show active' : ''); ?>" id="va-content" role="tabpanel">
                                <?php if(isset($virtualAccount) && $virtualAccount): ?>
                                
                                <div class="alert alert-success">
                                    <h6 class="alert-heading mb-3"><i class="fa fa-check-circle"></i> Virtual Account Berhasil Dibuat</h6>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Bank</label>
                                        <div class="fw-bold fs-5">
                                            <?php
                                                $bankName = match($virtualAccount->channel) {
                                                    'VIRTUAL_ACCOUNT_CIMB' => 'CIMB Niaga',
                                                    'VIRTUAL_ACCOUNT_MANDIRI' => 'Mandiri',
                                                    'VIRTUAL_ACCOUNT_BRI' => 'BRI',
                                                    'VIRTUAL_ACCOUNT_BNI' => 'BNI',
                                                    'VIRTUAL_ACCOUNT_PERMATA' => 'Permata',
                                                    default => 'Bank'
                                                };
                                            ?>
                                            <?php echo e($bankName); ?>

                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Nomor Virtual Account</label>
                                        <div class="input-group input-group-lg">
                                            <input type="text" class="form-control fw-bold text-center" 
                                                   value="<?php echo e($virtualAccount->display_va_number); ?>" 
                                                   id="vaNumber" readonly 
                                                   style="font-size: 1.25rem; letter-spacing: 2px;">
                                            <button class="btn btn-outline-success" type="button" onclick="copyVA()">
                                                <i class="fa fa-copy"></i> Salin
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Jumlah</label>
                                        <div class="fw-bold text-success" style="font-size: 1.5rem;">
                                            Rp <?php echo e(number_format($virtualAccount->amount, 0, ',', '.')); ?>

                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="text-muted small d-block mb-1">Berlaku Sampai</label>
                                        <div class="fw-bold text-danger fs-6">
                                            <?php echo e($virtualAccount->expired_at->format('d M Y H:i')); ?> WIB
                                        </div>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="d-grid gap-2 mb-3">
                                        <button type="button" class="btn btn-primary btn-lg" onclick="checkPaymentStatus()" id="checkStatusBtn">
                                            <i class="fa fa-refresh"></i> Cek Status Pembayaran
                                        </button>
                                    </div>
                                    
                                    <div class="small mb-0">
                                        <i class="fa fa-info-circle"></i>
                                        Transfer sesuai nominal <strong>exact</strong>. Pembayaran akan diverifikasi otomatis dalam 1-5 menit.
                                    </div>
                                </div>
                                <?php else: ?>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Buat Virtual Account</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?php echo e(route('music.slots.create-va', $order)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       name="phone" 
                                                       class="form-control <?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                                       value="<?php echo e(old('phone', auth()->user()->phone)); ?>"
                                                       placeholder="Contoh: 081234567890"
                                                       required>
                                                <?php $__errorArgs = ['phone'];
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
                                                    Format: 08xxx atau 628xxx (9-15 digit)
                                                </small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Bank Virtual Account <span class="text-danger">*</span></label>
                                                <select name="channel" class="form-select" required>
                                                    <option value="">-- Pilih Bank --</option>
                                                    <?php $__currentLoopData = \App\Services\DokuVirtualAccountService::getAvailableChannels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <option value="<?php echo e($code); ?>"><?php echo e($name); ?></option>
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                </select>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary w-100">
                                                <i class="fa fa-credit-card"></i> Buat Virtual Account
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            
                            <div class="tab-pane fade <?php echo e($hasActiveQris ? 'show active' : ''); ?>" id="qris-content" role="tabpanel">
                                <?php if(isset($qrisPayment) && $qrisPayment): ?>
                                
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white">
                                        <h6 class="mb-0"><i class="fa fa-qrcode"></i> Pembayaran QRIS</h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <?php if($qrisPayment->qr_content): ?>
                                        <div class="mb-3">
                                            <div id="qrcode" class="d-inline-block p-3 bg-white border rounded"></div>
                                        </div>
                                        <?php endif; ?>
                                        
                                        <div class="mb-3">
                                            <label class="text-muted small">Jumlah Pembayaran</label>
                                            <div class="fw-bold text-success" style="font-size: 1.75rem;">
                                                <?php echo e($qrisPayment->formatted_amount); ?>

                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="text-muted small">Status</label>
                                            <div>
                                                <?php if($qrisPayment->status === 'pending'): ?>
                                                    <span class="badge bg-warning">Menunggu Pembayaran</span>
                                                <?php elseif($qrisPayment->status === 'paid'): ?>
                                                    <span class="badge bg-success">Berhasil</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo e(ucfirst($qrisPayment->status)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="alert alert-info small">
                                            <i class="fa fa-info-circle"></i>
                                            <strong>Cara Pembayaran:</strong><br>
                                            1. Buka aplikasi e-wallet atau mobile banking<br>
                                            2. Pilih menu Scan QR atau QRIS<br>
                                            3. Scan kode QR di atas<br>
                                            4. Konfirmasi pembayaran<br>
                                            5. Pembayaran akan diverifikasi otomatis
                                        </div>
                                        
                                        <div class="alert alert-warning small mb-3">
                                            <i class="fa fa-clock"></i>
                                            Berlaku sampai: <strong><?php echo e($qrisPayment->expired_at->format('d M Y H:i')); ?> WIB</strong>
                                        </div>
                                        
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-primary" onclick="checkQrisStatus()" id="checkQrisBtn">
                                                <i class="fa fa-refresh"></i> Cek Status Pembayaran
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Buat Pembayaran QRIS</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info small mb-3">
                                            <i class="fa fa-qrcode"></i>
                                            <strong>QRIS</strong> adalah metode pembayaran yang dapat digunakan di semua aplikasi e-wallet dan mobile banking yang mendukung QRIS.
                                        </div>
                                        
                                        <form id="qris-form">
                                            <?php echo csrf_field(); ?>
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       name="phone" 
                                                       id="qris-phone"
                                                       class="form-control" 
                                                       value="<?php echo e(old('phone', auth()->user()->phone)); ?>"
                                                       placeholder="Contoh: 081234567890"
                                                       required>
                                                <small class="form-text text-muted">
                                                    Nomor telepon untuk notifikasi pembayaran
                                                </small>
                                            </div>
                                            
                                            <button type="submit" class="btn btn-primary w-100" id="qris-submit-btn">
                                                <i class="fa fa-qrcode"></i> Buat Kode QRIS
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($virtualAccount) && $virtualAccount): ?>
<script>
function copyVA() {
    const vaInput = document.getElementById('vaNumber');
    vaInput.select();
    document.execCommand('copy');
    
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-outline-success');
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-success');
    }, 2000);
}

function checkPaymentStatus() {
    const btn = document.getElementById('checkStatusBtn');
    const originalHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengecek...';
    
    fetch('<?php echo e(route("music.slots.check-status", $order)); ?>', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'paid') {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: data.message || 'Slot upload musik telah ditambahkan ke akun Anda',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
            }).then(() => {
                window.location.href = data.redirect || '<?php echo e(route("music.upload")); ?>';
            });
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Belum Ada Pembayaran',
                text: data.message || 'Pembayaran belum diterima',
                confirmButtonText: 'OK',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat mengecek status',
            confirmButtonText: 'OK',
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}

// Auto check every 30 seconds
setInterval(() => {
    checkPaymentStatus();
}, 30000);
</script>
<?php endif; ?>

<?php if(isset($qrisPayment) && $qrisPayment && $qrisPayment->qr_content): ?>
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new QRCode(document.getElementById("qrcode"), {
        text: "<?php echo e($qrisPayment->qr_content); ?>",
        width: 256,
        height: 256,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
});

function checkQrisStatus() {
    const btn = document.getElementById('checkQrisBtn');
    const originalHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengecek...';
    
    fetch('<?php echo e(route("music.slots.check-status", $order)); ?>', {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'paid') {
            Swal.fire({
                icon: 'success',
                title: 'Pembayaran Berhasil!',
                text: data.message || 'Slot upload musik telah ditambahkan ke akun Anda',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
            }).then(() => {
                window.location.href = data.redirect || '<?php echo e(route("music.upload")); ?>';
            });
        } else {
            Swal.fire({
                icon: 'info',
                title: 'Belum Ada Pembayaran',
                text: data.message || 'Pembayaran belum diterima',
                confirmButtonText: 'OK',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat mengecek status',
            confirmButtonText: 'OK',
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}

// Auto check every 30 seconds
setInterval(() => {
    checkQrisStatus();
}, 30000);
</script>
<?php endif; ?>

<script>
// Handle QRIS form submission with AJAX
document.getElementById('qris-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('qris-submit-btn');
    const originalHTML = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Membuat QRIS...';
    
    const phone = document.getElementById('qris-phone').value;
    
    fetch('<?php echo e(route("music.slots.create-qris", $order)); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            phone: phone
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'QRIS Berhasil Dibuat!',
                text: 'Halaman akan di-refresh untuk menampilkan kode QR...',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
            }).then(() => {
                window.location.href = '<?php echo e(route("music.slots.checkout")); ?>?qty=<?php echo e($order->qty); ?>&order_id=<?php echo e($order->id); ?>';
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat QRIS',
                text: data.message || 'Terjadi kesalahan saat membuat QRIS',
                confirmButtonText: 'OK',
            });
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat membuat QRIS',
            confirmButtonText: 'OK',
        });
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/music/checkout.blade.php ENDPATH**/ ?>