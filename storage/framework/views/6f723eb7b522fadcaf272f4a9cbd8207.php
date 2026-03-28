
<?php $__env->startSection('title', 'Payment Channels'); ?>
<?php $__env->startSection('page-title', 'Payment Channels'); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Dashboard</a></li>
    <li class="breadcrumb-item active">Payment Channels</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Payment Channel Configuration</h4>
                <form action="<?php echo e(route('admin.payment-channels.check-all')); ?>" method="POST" class="d-inline">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-sm btn-info">
                        <i class="fa fa-refresh"></i> Check All Availability
                    </button>
                </form>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Note:</strong> Only active and available channels will be shown to users during checkout.
                    Use "Check Availability" to test if a channel is working with your DOKU configuration.
                </div>

                
                <h5 class="mt-4 mb-3">Virtual Account Channels</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Channel</th>
                                <th width="15%">Code</th>
                                <th width="25%">Description</th>
                                <th width="10%" class="text-center">Active</th>
                                <th width="10%" class="text-center">Available</th>
                                <th width="15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $channels->where('type', 'virtual_account'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($channel->sort_order); ?></td>
                                <td>
                                    <strong><?php echo e($channel->name); ?></strong>
                                    <?php if($channel->last_checked_at): ?>
                                    <br><small class="text-muted">Last checked: <?php echo e($channel->last_checked_at->diffForHumans()); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo e($channel->code); ?></code></td>
                                <td>
                                    <?php echo e($channel->description); ?>

                                    <?php if($channel->partner_service_id): ?>
                                    <br><small class="text-muted"><strong>PSID:</strong> <?php echo e($channel->partner_service_id); ?></small>
                                    <?php endif; ?>
                                    <?php if($channel->last_error): ?>
                                    <br><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo e(Str::limit($channel->last_error, 50)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form action="<?php echo e(route('admin.payment-channels.toggle', $channel)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-sm btn-<?php echo e($channel->is_active ? 'success' : 'secondary'); ?>">
                                            <i class="fa fa-<?php echo e($channel->is_active ? 'check' : 'times'); ?>"></i>
                                            <?php echo e($channel->is_active ? 'Active' : 'Inactive'); ?>

                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <?php if($channel->is_available): ?>
                                        <span class="badge bg-success"><i class="fa fa-check"></i> Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="fa fa-times"></i> Not Available</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($channel->id); ?>" title="Edit Configuration">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="<?php echo e(route('admin.payment-channels.check', $channel)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-info" title="Check Availability">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            
                            
                            <div class="modal fade" id="editModal<?php echo e($channel->id); ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?php echo e(route('admin.payment-channels.update', $channel)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit <?php echo e($channel->name); ?> Configuration</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Channel Name</label>
                                                    <input type="text" class="form-control" value="<?php echo e($channel->name); ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Channel Code</label>
                                                    <input type="text" class="form-control" value="<?php echo e($channel->code); ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Partner Service ID / BIN <span class="text-danger">*</span></label>
                                                    <input type="text" name="partner_service_id" class="form-control" value="<?php echo e($channel->partner_service_id); ?>" placeholder="e.g., 13925, 98829172" required>
                                                    <small class="form-text text-muted">Get this from DOKU Dashboard. Can be 5-8 digits.</small>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">BIN Notes</label>
                                                    <textarea name="bin_notes" class="form-control" rows="2" placeholder="Optional notes about this BIN"><?php echo e($channel->bin_notes); ?></textarea>
                                                </div>
                                                <div class="alert alert-info small mb-0">
                                                    <i class="fa fa-info-circle"></i>
                                                    <strong>Note:</strong> Partner Service ID will be automatically padded to 8 characters with spaces when sent to DOKU API.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No virtual account channels found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                
                <h5 class="mt-5 mb-3">E-Wallet Channels</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="20%">Channel</th>
                                <th width="15%">Code</th>
                                <th width="25%">Description</th>
                                <th width="10%" class="text-center">Active</th>
                                <th width="10%" class="text-center">Available</th>
                                <th width="15%" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $channels->where('type', 'ewallet'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $channel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($channel->sort_order); ?></td>
                                <td>
                                    <strong><?php echo e($channel->name); ?></strong>
                                    <?php if($channel->last_checked_at): ?>
                                    <br><small class="text-muted">Last checked: <?php echo e($channel->last_checked_at->diffForHumans()); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td><code><?php echo e($channel->code); ?></code></td>
                                <td>
                                    <?php echo e($channel->description); ?>

                                    <?php if($channel->last_error): ?>
                                    <br><small class="text-danger"><i class="fa fa-exclamation-triangle"></i> <?php echo e(Str::limit($channel->last_error, 50)); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <form action="<?php echo e(route('admin.payment-channels.toggle', $channel)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('PATCH'); ?>
                                        <button type="submit" class="btn btn-sm btn-<?php echo e($channel->is_active ? 'success' : 'secondary'); ?>">
                                            <i class="fa fa-<?php echo e($channel->is_active ? 'check' : 'times'); ?>"></i>
                                            <?php echo e($channel->is_active ? 'Active' : 'Inactive'); ?>

                                        </button>
                                    </form>
                                </td>
                                <td class="text-center">
                                    <?php if($channel->is_available): ?>
                                        <span class="badge bg-success"><i class="fa fa-check"></i> Available</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger"><i class="fa fa-times"></i> Not Available</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editModal<?php echo e($channel->id); ?>" title="Edit Configuration">
                                        <i class="fa fa-edit"></i>
                                    </button>
                                    <form action="<?php echo e(route('admin.payment-channels.check', $channel)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-info" title="Check Availability">
                                            <i class="fa fa-refresh"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            
                            
                            <div class="modal fade" id="editModal<?php echo e($channel->id); ?>" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="<?php echo e(route('admin.payment-channels.update', $channel)); ?>" method="POST">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('PATCH'); ?>
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit <?php echo e($channel->name); ?> Configuration</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Channel Name</label>
                                                    <input type="text" class="form-control" value="<?php echo e($channel->name); ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Channel Code</label>
                                                    <input type="text" class="form-control" value="<?php echo e($channel->code); ?>" disabled>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Description</label>
                                                    <textarea name="description" class="form-control" rows="2"><?php echo e($channel->description); ?></textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Sort Order</label>
                                                    <input type="number" name="sort_order" class="form-control" value="<?php echo e($channel->sort_order); ?>">
                                                </div>
                                                <div class="alert alert-info small mb-0">
                                                    <i class="fa fa-info-circle"></i>
                                                    <strong>Note:</strong> E-Wallet channels don't require BIN/Partner Service ID configuration.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted">No e-wallet channels found</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="alert alert-warning mt-4">
                    <h6><i class="fa fa-exclamation-triangle"></i> Important Notes:</h6>
                    <ul class="mb-0">
                        <li><strong>Active:</strong> Admin-controlled. Toggle to enable/disable channel for users.</li>
                        <li><strong>Available:</strong> Auto-detected. Indicates if channel is working with DOKU configuration.</li>
                        <li><strong>Check Availability:</strong> Tests if channel can be used with current DOKU settings.</li>
                        <li>Only channels that are both <strong>Active</strong> and <strong>Available</strong> will be shown to users.</li>
                        <li>If all channels are unavailable, contact DOKU support to activate payment features.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/admin/payment-channels/index.blade.php ENDPATH**/ ?>