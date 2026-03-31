
<?php $__env->startSection('title', 'Checkout Paket ' . $plan->name); ?>
<?php $__env->startSection('page-title', 'Checkout Paket'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('subscription.index')); ?>">Paket Langganan</a></li>
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
.ewallet-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 15px 20px;
    border: 2px solid #e0e0e0;
    border-radius: 8px;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
    margin-bottom: 10px;
}
.ewallet-btn:hover {
    border-color: #0d6efd;
    background-color: #f8f9fa;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
.ewallet-btn img {
    height: 30px;
    margin-right: 10px;
}
.ewallet-btn .ewallet-name {
    font-weight: 600;
    font-size: 16px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header text-center bg-primary text-white">
                <h4 class="card-title mb-0">Pembayaran Paket <?php echo e($plan->name); ?></h4>
            </div>
            <div class="card-body">
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="text-center mb-3">
                            <span class="badge badge-<?php echo e($plan->badge_color); ?>" style="font-size:1.1rem;padding:8px 20px">
                                <?php echo e($plan->name); ?>

                            </span>
                            <?php if($plan->is_popular): ?>
                                <div class="mt-1"><small class="text-primary">⭐ Paling Populer</small></div>
                            <?php endif; ?>
                        </div>

                        <table class="table table-sm">
                            <tr><td class="text-muted">No. Order</td><td class="fw-bold"><?php echo e($order->order_number); ?></td></tr>
                            <tr><td class="text-muted">Paket</td><td><?php echo e($plan->name); ?></td></tr>
                            <tr><td class="text-muted">Undangan</td><td><?php echo e($plan->max_invitations); ?> undangan</td></tr>
                            <tr><td class="text-muted">Foto Galeri</td><td><?php echo e($plan->max_gallery_photos ?? 'Unlimited'); ?></td></tr>
                            <tr><td class="text-muted">Upload Lagu</td><td><?php echo e($plan->max_music_uploads === null ? 'Unlimited' : $plan->max_music_uploads . ' lagu'); ?></td></tr>
                            <tr><td class="text-muted">Gift Section</td><td><?php echo e($plan->gift_section_included ? 'Gratis' : 'Berbayar'); ?></td></tr>
                            <tr class="table-active">
                                <td class="fw-bold">Total Pembayaran</td>
                                <td class="fw-bold text-success fs-5">Rp <?php echo e(number_format($plan->price, 0, ',', '.')); ?></td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-md-6">
                        <?php
                            // Cek apakah ada pembayaran aktif
                            $hasActiveVA = isset($virtualAccount) && $virtualAccount;
                            $hasActiveEWallet = isset($ewalletPayment) && $ewalletPayment;
                            $hasActiveQris = isset($qrisPayment) && $qrisPayment;
                            $hasAnyActivePayment = $hasActiveVA || $hasActiveEWallet || $hasActiveQris;
                        ?>

                        <?php if($hasAnyActivePayment): ?>
                        <div class="alert alert-warning mb-3">
                            <i class="fa fa-info-circle"></i>
                            <strong>Perhatian:</strong> Anda sudah memiliki pembayaran yang aktif. Selesaikan pembayaran tersebut terlebih dahulu atau tunggu hingga kadaluarsa sebelum membuat pembayaran baru dengan metode lain.
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
                            <li class="nav-item" role="presentation">
                                <button class="nav-link <?php echo e($hasActiveEWallet ? 'active' : ''); ?> <?php echo e($hasAnyActivePayment && !$hasActiveEWallet ? 'disabled' : ''); ?>" 
                                        id="ewallet-tab" 
                                        data-bs-toggle="tab" 
                                        data-bs-target="#ewallet-content" 
                                        type="button" 
                                        role="tab"
                                        <?php echo e($hasAnyActivePayment && !$hasActiveEWallet ? 'disabled' : ''); ?>>
                                    <i class="fa fa-mobile"></i> E-Wallet
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
                                                    // Legacy format support
                                                    'VIRTUAL_ACCOUNT_BANK_CIMB' => 'CIMB Niaga',
                                                    'VIRTUAL_ACCOUNT_BANK_MANDIRI' => 'Mandiri',
                                                    'VIRTUAL_ACCOUNT_BANK_BRI' => 'BRI',
                                                    'VIRTUAL_ACCOUNT_BANK_BNI' => 'BNI',
                                                    'VIRTUAL_ACCOUNT_BANK_PERMATA' => 'Permata',
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
                                        <div class="alert alert-info small">
                                            <i class="fa fa-info-circle"></i>
                                            <strong>Partner Service ID belum dikonfigurasi dengan benar.</strong><br>
                                            Silakan hubungi admin atau gunakan metode pembayaran E-Wallet.
                                        </div>
                                        <form action="<?php echo e(route('subscription.create-va', $order)); ?>" method="POST">
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
                                                       placeholder="Contoh: 081234567890 atau 628123456789"
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

                            
                            <div class="tab-pane fade <?php echo e($hasActiveEWallet ? 'show active' : ''); ?>" id="ewallet-content" role="tabpanel">
                                <?php if(isset($ewalletPayment) && $ewalletPayment): ?>
                                
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h6 class="mb-0"><i class="fa fa-mobile"></i> Pembayaran E-Wallet</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <label class="text-muted small">Metode</label>
                                            <div class="fw-bold"><?php echo e($ewalletPayment->channel_name); ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small">Jumlah</label>
                                            <div class="fw-bold text-success fs-5"><?php echo e($ewalletPayment->formatted_amount); ?></div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="text-muted small">Status</label>
                                            <div>
                                                <?php if($ewalletPayment->status === 'processing'): ?>
                                                    <span class="badge bg-warning">Menunggu Pembayaran</span>
                                                <?php elseif($ewalletPayment->status === 'success'): ?>
                                                    <span class="badge bg-success">Berhasil</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?php echo e(ucfirst($ewalletPayment->status)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <?php if($ewalletPayment->web_redirect_url): ?>
                                        <a href="<?php echo e($ewalletPayment->web_redirect_url); ?>" class="btn btn-primary w-100 mb-2" target="_blank">
                                            <i class="fa fa-external-link"></i> Lanjutkan Pembayaran
                                        </a>
                                        <?php else: ?>
                                        <div class="alert alert-warning small mb-2">
                                            <i class="fa fa-exclamation-triangle"></i>
                                            Link pembayaran belum tersedia. Silakan coba buat pembayaran baru atau hubungi admin.
                                        </div>
                                        <a href="<?php echo e(route('subscription.checkout', $order->plan)); ?>" class="btn btn-secondary w-100 mb-2">
                                            <i class="fa fa-refresh"></i> Coba Lagi
                                        </a>
                                        <?php endif; ?>
                                        <div class="alert alert-info small mt-3 mb-0">
                                            <i class="fa fa-clock"></i>
                                            Berlaku sampai: <strong><?php echo e($ewalletPayment->expired_at->format('d M Y H:i')); ?> WIB</strong>
                                        </div>
                                    </div>
                                </div>
                                <?php else: ?>
                                
                                <div class="card">
                                    <div class="card-header">
                                        <h6 class="mb-0">Pilih E-Wallet</h6>
                                    </div>
                                    <div class="card-body">
                                        <form action="<?php echo e(route('subscription.create-ewallet', $order)); ?>" method="POST" id="ewallet-form">
                                            <?php echo csrf_field(); ?>
                                            <input type="hidden" name="channel" id="ewallet-channel">
                                            
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
                                                    Nomor telepon untuk notifikasi pembayaran
                                                </small>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label">Pilih Metode Pembayaran</label>
                                                
                                                <?php $__currentLoopData = \App\Services\DokuEWalletService::getAvailableChannels(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <div class="ewallet-btn" onclick="selectEWallet('<?php echo e($code); ?>')">
                                                    <div style="width: 50px; height: 30px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center; margin-right: 10px;">
                                                        <span style="font-size: 12px; font-weight: bold;">
                                                            <?php if($code === 'EMONEY_SHOPEE_PAY_SNAP'): ?>
                                                                🛒
                                                            <?php elseif($code === 'EMONEY_DANA_SNAP'): ?>
                                                                💰
                                                            <?php else: ?>
                                                                💳
                                                            <?php endif; ?>
                                                        </span>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <div class="ewallet-name"><?php echo e($info['name']); ?></div>
                                                        <small class="text-muted"><?php echo e($info['description']); ?></small>
                                                    </div>
                                                    <i class="fa fa-chevron-right text-muted"></i>
                                                </div>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
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
                                            1. Buka aplikasi e-wallet atau mobile banking Anda<br>
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
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Kode Pos (Opsional)</label>
                                                <input type="text" 
                                                       name="postal_code" 
                                                       id="qris-postal-code"
                                                       class="form-control" 
                                                       value="<?php echo e(old('postal_code', '12345')); ?>"
                                                       placeholder="12345"
                                                       maxlength="5">
                                                <small class="form-text text-muted">
                                                    5 digit kode pos (default: 12345)
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

                <?php if(isset($virtualAccount) && $virtualAccount): ?>
                
                <div class="card bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3"><i class="fa fa-list"></i> Cara Pembayaran</h6>
                        <ol class="mb-0 ps-3">
                            <li>Buka aplikasi mobile banking atau ATM</li>
                            <li>Pilih menu <strong>Transfer</strong></li>
                            <li>Pilih <strong>Bank 
                                <?php
                                    $bankName = match($virtualAccount->channel) {
                                        'VIRTUAL_ACCOUNT_CIMB' => 'CIMB Niaga',
                                        'VIRTUAL_ACCOUNT_MANDIRI' => 'Mandiri',
                                        'VIRTUAL_ACCOUNT_BRI' => 'BRI',
                                        'VIRTUAL_ACCOUNT_BNI' => 'BNI',
                                        'VIRTUAL_ACCOUNT_PERMATA' => 'Permata',
                                        // Legacy format support
                                        'VIRTUAL_ACCOUNT_BANK_CIMB' => 'CIMB Niaga',
                                        'VIRTUAL_ACCOUNT_BANK_MANDIRI' => 'Mandiri',
                                        'VIRTUAL_ACCOUNT_BANK_BRI' => 'BRI',
                                        'VIRTUAL_ACCOUNT_BANK_BNI' => 'BNI',
                                        'VIRTUAL_ACCOUNT_BANK_PERMATA' => 'Permata',
                                        default => 'Bank'
                                    };
                                ?>
                                <?php echo e($bankName); ?></strong></li>
                            <li>Masukkan nomor VA: <strong><?php echo e($virtualAccount->display_va_number); ?></strong></li>
                            <li>Masukkan jumlah: <strong>Rp <?php echo e(number_format($virtualAccount->amount, 0, ',', '.')); ?></strong></li>
                            <li>Konfirmasi pembayaran</li>
                            <li>Simpan bukti transfer</li>
                        </ol>
                        <div class="alert alert-info mt-3 mb-0">
                            <i class="fa fa-clock"></i>
                            Pembayaran akan diverifikasi otomatis dalam <strong>1-5 menit</strong> setelah transfer berhasil.
                            Paket akan langsung aktif setelah pembayaran terverifikasi.
                        </div>
                    </div>
                </div>
                <?php endif; ?>

               
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
    
    // Show feedback
    const btn = event.target.closest('button');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fa fa-check"></i> Copied!';
    btn.classList.add('btn-success');
    btn.classList.remove('btn-outline-secondary');
    
    setTimeout(() => {
        btn.innerHTML = originalHTML;
        btn.classList.remove('btn-success');
        btn.classList.add('btn-outline-secondary');
    }, 2000);
}

function checkPaymentStatus() {
    const btn = document.getElementById('checkStatusBtn');
    const originalHTML = btn.innerHTML;
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengecek...';
    
    // Call API
    fetch('<?php echo e(route("api.payment.check-va-status")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            va_id: <?php echo e($virtualAccount->id); ?>

        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Payment status:', data);
        
        if (data.success) {
            if (data.status === 'paid') {
                // Payment successful
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    html: data.message + '<br><br>Halaman akan di-refresh dalam 3 detik...',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.reload();
                });
            } else if (data.status === 'expired') {
                // Payment expired
                Swal.fire({
                    icon: 'error',
                    title: 'VA Kadaluarsa',
                    text: data.message,
                    confirmButtonText: 'Buat VA Baru',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                // Still pending
                Swal.fire({
                    icon: 'info',
                    title: 'Belum Ada Pembayaran',
                    text: data.message,
                    confirmButtonText: 'OK',
                });
            }
        } else {
            // Error
            Swal.fire({
                icon: 'error',
                title: 'Gagal Cek Status',
                text: data.message || 'Terjadi kesalahan saat mengecek status pembayaran',
                confirmButtonText: 'OK',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat mengecek status pembayaran',
            confirmButtonText: 'OK',
        });
    })
    .finally(() => {
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}

// Auto check status every 30 seconds
let autoCheckInterval = setInterval(() => {
    console.log('Auto checking payment status...');
    checkPaymentStatus();
}, 30000); // 30 seconds

// Clear interval when page is hidden
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(autoCheckInterval);
    } else {
        autoCheckInterval = setInterval(() => {
            checkPaymentStatus();
        }, 30000);
    }
});
</script>
<?php endif; ?>

<script>
function selectEWallet(channel) {
    document.getElementById('ewallet-channel').value = channel;
    document.getElementById('ewallet-form').submit();
}

<?php if(isset($qrisPayment) && $qrisPayment && $qrisPayment->qr_content): ?>
// Generate QR Code using qrcodejs
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
    
    fetch('<?php echo e(route("api.payment.check-qris-status")); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            qris_id: <?php echo e($qrisPayment->id); ?>

        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('QRIS status:', data);
        
        if (data.success) {
            if (data.status === 'paid') {
                Swal.fire({
                    icon: 'success',
                    title: 'Pembayaran Berhasil!',
                    html: data.message + '<br><br>Halaman akan di-refresh dalam 3 detik...',
                    timer: 3000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                }).then(() => {
                    window.location.reload();
                });
            } else if (data.status === 'expired') {
                Swal.fire({
                    icon: 'error',
                    title: 'QRIS Kadaluarsa',
                    text: data.message,
                    confirmButtonText: 'Buat QRIS Baru',
                }).then(() => {
                    window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'info',
                    title: 'Belum Ada Pembayaran',
                    text: data.message,
                    confirmButtonText: 'OK',
                });
            }
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal Cek Status',
                text: data.message || 'Terjadi kesalahan saat mengecek status pembayaran',
                confirmButtonText: 'OK',
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat mengecek status pembayaran',
            confirmButtonText: 'OK',
        });
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
}

// Auto check QRIS status every 30 seconds
let autoCheckQrisInterval = setInterval(() => {
    console.log('Auto checking QRIS status...');
    checkQrisStatus();
}, 30000);

document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        clearInterval(autoCheckQrisInterval);
    } else {
        autoCheckQrisInterval = setInterval(() => {
            checkQrisStatus();
        }, 30000);
    }
});
<?php endif; ?>
</script>

<script>
// Handle QRIS form submission with AJAX
document.getElementById('qris-form')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const btn = document.getElementById('qris-submit-btn');
    const originalHTML = btn.innerHTML;
    
    // Disable button and show loading
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Membuat QRIS...';
    
    // Get form data
    const phone = document.getElementById('qris-phone').value;
    const postalCode = document.getElementById('qris-postal-code').value;
    
    // Make AJAX request
    fetch('<?php echo e(route("subscription.create-qris", $order)); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            phone: phone,
            postal_code: postalCode
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('QRIS creation response:', data);
        
        if (data.success) {
            // Show success message and reload page
            Swal.fire({
                icon: 'success',
                title: 'QRIS Berhasil Dibuat!',
                text: 'Halaman akan di-refresh untuk menampilkan kode QR...',
                timer: 2000,
                timerProgressBar: true,
                showConfirmButton: false,
            }).then(() => {
                window.location.reload();
            });
        } else {
            // Show error message
            Swal.fire({
                icon: 'error',
                title: 'Gagal Membuat QRIS',
                text: data.message || 'Terjadi kesalahan saat membuat QRIS',
                confirmButtonText: 'OK',
            });
            
            // Re-enable button
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
        
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = originalHTML;
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/subscription/checkout.blade.php ENDPATH**/ ?>