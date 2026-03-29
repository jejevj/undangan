<?php $__empty_1 = true; $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
<div class="col-xl-3 col-md-3 col-sm-6 col-6">
  <div class="template-card">
    <div class="template-thumbnail">
      <?php if($template->thumbnail): ?>
      <img src="<?php echo e(asset('storage/' . $template->thumbnail)); ?>" alt="<?php echo e($template->name); ?>">
      <?php else: ?>
      <div style="width:100%; height:100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display:flex; align-items:center; justify-content:center;">
        <span style="color:white; font-size:18px; font-weight:600; text-align:center; padding:20px;"><?php echo e($template->name); ?></span>
      </div>
      <?php endif; ?>
    </div>
    <div class="template-info">
      <h6 class="template-name"><?php echo e($template->name); ?></h6>
      <div class="template-price mb-2">
        <span class="badge badge-<?php echo e($template->isFree() ? 'success' : 'warning'); ?>">
          <?php echo e($template->formattedPrice()); ?>

        </span>
      </div>
      <div class="template-actions">
        <?php if($template->preview_url): ?>
        <a href="<?php echo e($template->preview_url); ?>" target="_blank" class="btn btn-sm btn-warning w-75 mx-auto d-block mb-2">
          <i class="fa fa-eye"></i>
          <span>Preview</span>
        </a>
        <?php endif; ?>
        <?php if(auth()->guard()->check()): ?>
        <a href="<?php echo e(route('invitations.create', ['template' => $template->id])); ?>" class="btn btn-sm btn-primary w-75 mx-auto d-block">
          <i class="fa fa-check"></i>
          <span>Gunakan</span>
        </a>
        <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="btn btn-sm btn-primary w-75 mx-auto d-block">
          <i class="fa fa-sign-in-alt"></i>
          <span>Login</span>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
<div class="col-12 text-center py-5">
  <p class="text-muted">Belum ada template tersedia untuk filter ini</p>
</div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/landing/partials/template-grid.blade.php ENDPATH**/ ?>