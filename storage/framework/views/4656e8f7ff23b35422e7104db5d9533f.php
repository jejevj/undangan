<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">

            <?php $__currentLoopData = \App\Models\Menu::getMenuTree(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $hasVisibleChildren = $menu->children->filter(fn($c) =>
                        !$c->permission_name || auth()->user()->can($c->permission_name)
                    )->count() > 0;

                    // Parent dengan children: tampil jika ada child yang bisa diakses
                    // Parent tanpa children: tampil jika tidak ada permission, atau user punya permission
                    $canSeeMenu = $menu->children->count() > 0
                        ? $hasVisibleChildren
                        : (!$menu->permission_name || auth()->user()->can($menu->permission_name));
                ?>

                <?php if($canSeeMenu): ?>

                    <?php if($menu->children->count() > 0): ?>
                        
                        <?php if($hasVisibleChildren): ?>
                            <li class="<?php echo e(request()->routeIs(rtrim($menu->slug, '/') . '.*') ? 'mm-active' : ''); ?>">
                                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                                    <i class="<?php echo e($menu->icon); ?>"></i>
                                    <span class="nav-text"><?php echo e($menu->name); ?></span>
                                </a>
                                <ul aria-expanded="false">
                                    <?php $__currentLoopData = $menu->children; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php if(!$child->permission_name || auth()->user()->can($child->permission_name)): ?>
                                            <li class="<?php echo e(request()->is(ltrim($child->url ?? '', '/') . '*') ? 'mm-active' : ''); ?>">
                                                <a href="<?php echo e($child->url ? url($child->url) : 'javascript:void(0)'); ?>"><?php echo e($child->name); ?></a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </ul>
                            </li>
                        <?php endif; ?>
                    <?php else: ?>
                        
                        <li class="<?php echo e(request()->is(ltrim($menu->url ?? '/', '/')) || ($menu->url === '/' && request()->is('/')) ? 'mm-active' : ''); ?>">
                            <a href="<?php echo e($menu->url ? url($menu->url) : 'javascript:void(0)'); ?>" class="ai-icon" aria-expanded="false">
                                <i class="<?php echo e($menu->icon); ?>"></i>
                                <span class="nav-text"><?php echo e($menu->name); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </ul>
        <div class="copyright">
            <p><strong><?php echo e(config('app.name')); ?></strong> © <?php echo e(date('Y')); ?> All Rights Reserved</p>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>