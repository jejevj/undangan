

<?php $__env->startSection('content'); ?>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa fa-music"></i> Beli Slot Upload Musik
                    </h5>
                </div>
                <div class="card-body">
                    
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fa fa-info-circle"></i> Upload Musik Sendiri
                        </h6>
                        <p class="mb-0">Beli slot untuk upload musik sendiri yang bisa digunakan di undangan Anda. Setiap slot berlaku untuk 1 file musik.</p>
                    </div>

                    
                    <form action="<?php echo e(route('music.slots.checkout')); ?>" method="GET">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Jumlah Slot Upload</label>
                            <div class="input-group input-group-lg">
                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQty()">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="number" 
                                       name="qty" 
                                       id="qty" 
                                       class="form-control text-center fw-bold" 
                                       value="1" 
                                       min="1" 
                                       max="10" 
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQty()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 1 slot, maksimal 10 slot</small>
                        </div>

                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Cepat:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(1)">1 Slot</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(3)">3 Slot</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(5)">5 Slot</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(10)">10 Slot</button>
                            </div>
                        </div>

                        
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Harga per slot:</span>
                                    <strong>Rp <?php echo e(number_format($pricePerSlot, 0, ',', '.')); ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Jumlah slot:</span>
                                    <strong id="displayQty">1</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Subtotal:</span>
                                    <strong class="text-primary fs-5" id="displayTotal">Rp <?php echo e(number_format(1 * $pricePerSlot, 0, ',', '.')); ?></strong>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-info-circle"></i> Biaya admin akan ditambahkan sesuai metode pembayaran
                                </small>
                            </div>
                        </div>

                        
                        <div class="d-flex gap-2">
                            <a href="<?php echo e(route('music.upload')); ?>" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fa fa-shopping-cart"></i> Lanjut ke Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const pricePerSlot = <?php echo e($pricePerSlot); ?>;

function updateDisplay() {
    const qty = parseInt(document.getElementById('qty').value) || 1;
    document.getElementById('displayQty').textContent = qty;
    document.getElementById('displayTotal').textContent = 'Rp ' + (qty * pricePerSlot).toLocaleString('id-ID');
}

function setQty(value) {
    document.getElementById('qty').value = value;
    updateDisplay();
}

function increaseQty() {
    const input = document.getElementById('qty');
    const current = parseInt(input.value) || 1;
    if (current < 10) {
        input.value = current + 1;
        updateDisplay();
    }
}

function decreaseQty() {
    const input = document.getElementById('qty');
    const current = parseInt(input.value) || 1;
    if (current > 1) {
        input.value = current - 1;
        updateDisplay();
    }
}

document.getElementById('qty').addEventListener('input', updateDisplay);
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/music/select-quantity.blade.php ENDPATH**/ ?>