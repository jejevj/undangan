
<?php $__env->startSection('title', 'Galeri Foto'); ?>
<?php $__env->startSection('page-title', 'Galeri Foto Undangan'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('invitations.index')); ?>">Undangan Saya</a></li>
    <li class="breadcrumb-item"><a href="<?php echo e(route('invitations.edit', $invitation)); ?>"><?php echo e(Str::limit($invitation->title, 30)); ?></a></li>
    <li class="breadcrumb-item active">Galeri</li>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.photo-item {
    position: relative;
    cursor: pointer;
    border: 3px solid transparent;
    transition: all 0.3s;
}
.photo-item.selected {
    border-color: #0d6efd;
    box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);
}
.photo-item .selection-badge {
    position: absolute;
    top: 5px;
    right: 5px;
    background: #0d6efd;
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: none;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.photo-item.selected .selection-badge {
    display: flex;
}
.photo-item img {
    width: 100%;
    height: 200px;
    object-fit: cover;
}
.sortable-ghost {
    opacity: 0.4;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>


<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                <h6 class="mb-2"><i class="fa fa-images"></i> Gallery Pool Anda</h6>
                <span class="fw-bold fs-5"><?php echo e($used); ?> / <?php echo e($total); ?></span>
                <span class="text-muted ms-1">foto terpakai</span>
                <div class="progress mt-2" style="height:6px;width:250px">
                    <div class="progress-bar bg-<?php echo e($remaining > 0 ? 'success' : 'danger'); ?>"
                         style="width:<?php echo e($total > 0 ? min(100, ($used/$total)*100) : 0); ?>%"></div>
                </div>
                <small class="text-muted d-block mt-1">
                    <?php if($remaining > 0): ?>
                        Sisa <strong><?php echo e($remaining); ?></strong> slot foto
                    <?php else: ?>
                        <span class="text-danger">Slot foto habis</span>
                    <?php endif; ?>
                </small>
            </div>

            <div class="d-flex gap-2 align-items-center">
                <span class="text-muted small">
                    Harga: <strong>Rp 5.000/slot</strong>
                </span>
                <a href="<?php echo e(route('gallery.select-quantity')); ?>" class="btn btn-warning btn-sm">
                    <i class="fa fa-plus"></i> Beli Slot Foto
                </a>
            </div>
        </div>
    </div>
</div>

<div class="alert alert-info">
    <i class="fa fa-info-circle"></i>
    <strong>Cara Kerja:</strong> Upload foto ke gallery pool Anda, lalu pilih foto mana yang akan ditampilkan di undangan ini. Foto bisa digunakan di banyak undangan.
</div>

<div class="row">
    
    <div class="col-lg-4">
        <div class="card sticky-top" style="top: 20px;">
            <div class="card-header"><h5 class="card-title mb-0">Upload Foto Baru</h5></div>
            <div class="card-body">
                <?php if($remaining <= 0): ?>
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Slot foto habis. Beli slot tambahan untuk upload lebih banyak foto.
                    </div>
                    <a href="<?php echo e(route('gallery.select-quantity')); ?>" class="btn btn-warning w-100">
                        <i class="fa fa-shopping-cart"></i> Beli Slot Foto
                    </a>
                <?php else: ?>
                    <form action="<?php echo e(route('invitations.gallery.store', $invitation)); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>
                        <div class="form-group mb-3">
                            <label>Pilih Foto</label>
                            <input type="file" name="photos[]" class="form-control" accept="image/*" multiple required>
                            <small class="text-muted">
                                Format: JPG/PNG/WebP, maks 5MB per foto.
                                Sisa slot: <?php echo e($remaining); ?> foto.
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-upload"></i> Upload ke Gallery Pool
                        </button>
                    </form>
                <?php endif; ?>

                <hr class="my-4">

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" id="saveSelectionBtn" disabled>
                        <i class="fa fa-check"></i> Simpan Pilihan (<span id="selectedCount">0</span>)
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="clearSelectionBtn">
                        <i class="fa fa-times"></i> Batal
                    </button>
                </div>

                <div class="alert alert-sm alert-info mt-3 mb-0">
                    <small>
                        <i class="fa fa-lightbulb"></i>
                        <strong>Tips:</strong> Klik foto untuk memilih. Urutan klik = urutan tampil di undangan.
                    </small>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Gallery Pool (<?php echo e($allUserPhotos->count()); ?> foto)</h5>
                <span class="badge bg-primary"><?php echo e(count($selectedPhotoIds)); ?> dipilih untuk undangan ini</span>
            </div>
            <div class="card-body">
                <?php if($allUserPhotos->isEmpty()): ?>
                    <p class="text-muted text-center py-5">
                        <i class="fa fa-images fa-3x mb-3 d-block"></i>
                        Belum ada foto di gallery pool Anda. Upload foto terlebih dahulu.
                    </p>
                <?php else: ?>
                    <div class="row g-3" id="photoGrid">
                        <?php $__currentLoopData = $allUserPhotos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $photo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-md-4 col-sm-6">
                            <div class="photo-item <?php echo e(in_array($photo->id, $selectedPhotoIds) ? 'selected' : ''); ?>" 
                                 data-photo-id="<?php echo e($photo->id); ?>"
                                 data-selected="<?php echo e(in_array($photo->id, $selectedPhotoIds) ? 'true' : 'false'); ?>">
                                <img src="<?php echo e($photo->url); ?>" alt="<?php echo e($photo->caption); ?>" class="rounded">
                                <div class="selection-badge"></div>
                                <div class="p-2">
                                    <?php if($photo->caption): ?>
                                        <small class="text-muted d-block"><?php echo e($photo->caption); ?></small>
                                    <?php endif; ?>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <small class="text-muted">
                                            <?php if($photo->is_paid): ?>
                                                <span class="badge bg-warning text-dark">Berbayar</span>
                                            <?php else: ?>
                                                <span class="badge bg-success">Gratis</span>
                                            <?php endif; ?>
                                        </small>
                                        <form action="<?php echo e(route('invitations.gallery.destroy', [$invitation, $photo])); ?>" 
                                              method="POST" 
                                              class="d-inline"
                                              onsubmit="return confirm('Hapus foto ini dari gallery pool? Foto akan dihapus dari semua undangan yang menggunakannya.')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
let selectedPhotos = <?php echo json_encode($selectedPhotoIds, 15, 512) ?>;
let selectionOrder = 1;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize selection order for already selected photos
    updateSelectionBadges();

    // Photo selection handler
    document.querySelectorAll('.photo-item').forEach(item => {
        item.addEventListener('click', function(e) {
            // Don't toggle if clicking delete button
            if (e.target.closest('form')) return;

            const photoId = parseInt(this.dataset.photoId);
            const isSelected = this.dataset.selected === 'true';

            if (isSelected) {
                // Deselect
                const index = selectedPhotos.indexOf(photoId);
                if (index > -1) {
                    selectedPhotos.splice(index, 1);
                }
                this.classList.remove('selected');
                this.dataset.selected = 'false';
            } else {
                // Select
                selectedPhotos.push(photoId);
                this.classList.add('selected');
                this.dataset.selected = 'true';
            }

            updateSelectionBadges();
            updateSaveButton();
        });
    });

    // Save selection button
    document.getElementById('saveSelectionBtn').addEventListener('click', function() {
        if (selectedPhotos.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Tidak Ada Foto Dipilih',
                text: 'Pilih minimal 1 foto untuk undangan ini.',
            });
            return;
        }

        // Send to server
        fetch('<?php echo e(route("invitations.gallery.select", $invitation)); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                photo_ids: selectedPhotos
            })
        })
        .then(response => response.json())
        .then(data => {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: selectedPhotos.length + ' foto dipilih untuk undangan ini.',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.reload();
            });
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Terjadi kesalahan saat menyimpan pilihan foto.',
            });
        });
    });

    // Clear selection button
    document.getElementById('clearSelectionBtn').addEventListener('click', function() {
        window.location.reload();
    });
});

function updateSelectionBadges() {
    document.querySelectorAll('.photo-item').forEach(item => {
        const photoId = parseInt(item.dataset.photoId);
        const badge = item.querySelector('.selection-badge');
        const index = selectedPhotos.indexOf(photoId);
        
        if (index > -1) {
            badge.textContent = index + 1;
        }
    });

    document.getElementById('selectedCount').textContent = selectedPhotos.length;
}

function updateSaveButton() {
    const btn = document.getElementById('saveSelectionBtn');
    btn.disabled = selectedPhotos.length === 0;
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/gallery/index.blade.php ENDPATH**/ ?>