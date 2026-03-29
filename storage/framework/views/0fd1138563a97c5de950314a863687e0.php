<?php
    $groupLabels = [
        'mempelai' => 'Data Mempelai',
        'acara'    => 'Data Acara',
        'tambahan' => 'Informasi Tambahan',
        'musik'    => 'Musik Latar',
        ''         => 'Lainnya',
        null       => 'Lainnya',
    ];
    // $accessibleMusic dikirim dari controller
    $musicList = $accessibleMusic ?? collect();
?>

<?php $__currentLoopData = $fieldsByGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $fields): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="card mb-4">
    <div class="card-header">
        <h4 class="card-title"><?php echo e($groupLabels[$group] ?? ucfirst($group)); ?></h4>
    </div>
    <div class="card-body">
        <div class="row">
            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $value = old('fields.' . $field->key, $existingData[$field->key] ?? $field->default_value);
                $inputName = 'fields[' . $field->key . ']';
                $hasError = $errors->has('fields.' . $field->key);
            ?>
            <div class="col-md-<?php echo e(in_array($field->type, ['textarea']) ? '12' : '6'); ?> form-group mb-3">
                <label>
                    <?php echo e($field->label); ?>

                    <?php if($field->required): ?> <span class="text-danger">*</span> <?php endif; ?>
                </label>

                <?php if($field->type === 'textarea'): ?>
                    <textarea name="<?php echo e($inputName); ?>" class="form-control <?php echo e($hasError ? 'is-invalid' : ''); ?>"
                        rows="3" <?php echo e($field->required ? 'required' : ''); ?>

                        placeholder="<?php echo e($field->placeholder); ?>"><?php echo e($value); ?></textarea>

                <?php elseif($field->type === 'image'): ?>
                    <?php if($value): ?>
                        <div class="mb-1">
                            <img src="<?php echo e(asset('storage/' . $value)); ?>" class="img-thumbnail" style="height:80px">
                        </div>
                    <?php endif; ?>
                    <input type="file" name="<?php echo e($inputName); ?>" class="form-control <?php echo e($hasError ? 'is-invalid' : ''); ?>"
                        accept="image/*" <?php echo e($field->required && !$value ? 'required' : ''); ?>>

                <?php elseif($field->key === 'music_url'): ?>
                    
                    <?php if($musicList->isEmpty()): ?>
                        <div class="alert alert-warning py-2 mb-1">
                            Belum ada lagu tersedia.
                            <a href="<?php echo e(route('music.index')); ?>" target="_blank">Beli atau upload lagu</a>
                        </div>
                    <?php else: ?>
                        <select name="<?php echo e($inputName); ?>" class="form-control <?php echo e($hasError ? 'is-invalid' : ''); ?>"
                            id="musicSelect" <?php echo e($field->required ? 'required' : ''); ?>>
                            <option value="">— Tidak ada musik —</option>
                            <?php
                                $userUploads = $musicList->filter(fn($m) => $m->isUserUpload());
                                $systemSongs = $musicList->filter(fn($m) => !$m->isUserUpload());
                            ?>
                            <?php if($userUploads->count()): ?>
                                <optgroup label="📁 Upload Saya">
                                    <?php $__currentLoopData = $userUploads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $song): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($song->audioUrl()); ?>"
                                            data-title="<?php echo e($song->title); ?>"
                                            data-artist="<?php echo e($song->artist); ?>"
                                            data-preview="<?php echo e($song->audioUrl()); ?>"
                                            <?php echo e($value === $song->audioUrl() ? 'selected' : ''); ?>>
                                            <?php echo e($song->title); ?><?php echo e($song->artist ? ' — ' . $song->artist : ''); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                            <?php endif; ?>
                            <?php if($systemSongs->count()): ?>
                                <optgroup label="🎵 Lagu Tersedia">
                                    <?php $__currentLoopData = $systemSongs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $song): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($song->audioUrl()); ?>"
                                            data-title="<?php echo e($song->title); ?>"
                                            data-artist="<?php echo e($song->artist); ?>"
                                            data-preview="<?php echo e($song->audioUrl()); ?>"
                                            <?php echo e($value === $song->audioUrl() ? 'selected' : ''); ?>>
                                            <?php echo e($song->title); ?><?php echo e($song->artist ? ' — ' . $song->artist : ''); ?>

                                            (<?php echo e($song->isFree() ? 'Gratis' : 'Premium'); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </optgroup>
                            <?php endif; ?>
                        </select>
                        
                        <div id="musicPreviewWrap" class="mt-2" style="<?php echo e($value ? '' : 'display:none'); ?>">
                            <audio id="musicPreview" controls class="w-100" style="height:32px">
                                <?php if($value): ?><source src="<?php echo e($value); ?>" type="audio/mpeg"><?php endif; ?>
                            </audio>
                        </div>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Pilih lagu dari daftar di atas</small>
                            <a href="<?php echo e(route('music.index')); ?>" target="_blank" class="small">
                                + Beli / Upload lagu
                            </a>
                        </div>
                    <?php endif; ?>

                <?php elseif($field->key === 'music_title' || $field->key === 'music_artist'): ?>
                    
                    <input type="text" name="<?php echo e($inputName); ?>"
                        class="form-control <?php echo e($hasError ? 'is-invalid' : ''); ?> music-meta-field"
                        data-meta="<?php echo e($field->key === 'music_title' ? 'title' : 'artist'); ?>"
                        value="<?php echo e($value); ?>"
                        placeholder="<?php echo e($field->placeholder); ?>">

                <?php elseif($field->type === 'select'): ?>
                    <select name="<?php echo e($inputName); ?>" class="form-control <?php echo e($hasError ? 'is-invalid' : ''); ?>" <?php echo e($field->required ? 'required' : ''); ?>>
                        <option value="">-- Pilih --</option>
                        <?php $__currentLoopData = $field->options ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $opt): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($opt); ?>" <?php echo e($value == $opt ? 'selected' : ''); ?>><?php echo e($opt); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>

                <?php else: ?>
                    <input type="<?php echo e($field->type); ?>" name="<?php echo e($inputName); ?>"
                        class="form-control <?php echo e($hasError ? 'is-invalid' : ''); ?>"
                        value="<?php echo e($value); ?>"
                        placeholder="<?php echo e($field->placeholder); ?>"
                        <?php echo e($field->required ? 'required' : ''); ?>>
                <?php endif; ?>

                <?php $__errorArgs = ['fields.' . $field->key];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback d-block"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php if (! $__env->hasRenderedOnce('ba7d1ba2-d4f5-42c8-b6ea-348c674d67c0')): $__env->markAsRenderedOnce('ba7d1ba2-d4f5-42c8-b6ea-348c674d67c0'); ?>
<?php $__env->startPush('scripts'); ?>
<script>
// Auto-fill judul & artis saat pilih lagu dari dropdown
const musicSelect = document.getElementById('musicSelect');
if (musicSelect) {
    musicSelect.addEventListener('change', function () {
        const opt     = this.options[this.selectedIndex];
        const title   = opt.dataset.title   || '';
        const artist  = opt.dataset.artist  || '';
        const preview = opt.dataset.preview || '';

        // Isi field music_title dan music_artist
        document.querySelectorAll('.music-meta-field').forEach(el => {
            if (el.dataset.meta === 'title')  el.value = title;
            if (el.dataset.meta === 'artist') el.value = artist;
        });

        // Update preview audio
        const wrap    = document.getElementById('musicPreviewWrap');
        const audioEl = document.getElementById('musicPreview');
        if (preview && audioEl) {
            audioEl.src = preview;
            audioEl.load();
            wrap.style.display = '';
        } else if (wrap) {
            wrap.style.display = 'none';
        }
    });
}
</script>
<?php $__env->stopPush(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/invitations/_fields.blade.php ENDPATH**/ ?>