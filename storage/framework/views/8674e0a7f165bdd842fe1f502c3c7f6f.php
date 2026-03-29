
<?php $__env->startSection('title', 'Dashboard Admin'); ?>
<?php $__env->startSection('page-title', 'Dashboard Admin'); ?>

<?php $__env->startPush('styles'); ?>
<style>
.stat-card {
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: transform 0.3s;
}
.stat-card:hover {
    transform: translateY(-5px);
}
.stat-icon {
    font-size: 2.5rem;
    opacity: 0.8;
}
.chart-container {
    position: relative;
    height: 300px;
    margin-bottom: 20px;
}
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<div class="row">
    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card text-white" style="background: linear-gradient(135deg, #12a575 0%, #0d8a5f 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Total Users</h6>
                    <h3 class="text-white mb-0"><?php echo e(number_format($totalUsers)); ?></h3>
                    <small class="text-white-50">+<?php echo e($newUsersThisMonth); ?> bulan ini</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card text-white" style="background: linear-gradient(135deg, #20c997 0%, #17a589 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Subscription Aktif</h6>
                    <h3 class="text-white mb-0"><?php echo e(number_format($activeSubscriptions)); ?></h3>
                    <small class="text-white-50">Paket berbayar</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card text-white" style="background: linear-gradient(135deg, #0dcaf0 0%, #0aa2c0 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Total Undangan</h6>
                    <h3 class="text-white mb-0"><?php echo e(number_format($totalInvitations)); ?></h3>
                    <small class="text-white-50"><?php echo e($publishedInvitations); ?> published</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-envelope"></i>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card text-white" style="background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Total Revenue</h6>
                    <h3 class="text-white mb-0">Rp <?php echo e(number_format($totalRevenue, 0, ',', '.')); ?></h3>
                    <small class="text-white-50">Rp <?php echo e(number_format($revenueThisMonth, 0, ',', '.')); ?> bulan ini</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    
    <div class="col-xl-8 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Revenue 6 Bulan Terakhir</h4>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-xl-4 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Metode Pembayaran</h4>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Pertumbuhan User</h4>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="userGrowthChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Subscription per Paket</h4>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="subscriptionPlanChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="row">
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Penggunaan Channel VA</h4>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="vaChannelChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    
    <div class="col-xl-6 col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Subscription Terbaru</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Paket</th>
                                <th>Amount</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $recentSubscriptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sub): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <div class="font-weight-bold"><?php echo e($sub->user->name); ?></div>
                                    <small class="text-muted"><?php echo e($sub->user->email); ?></small>
                                </td>
                                <td><span class="badge" style="background-color: #12a575; color: white;"><?php echo e($sub->plan->name); ?></span></td>
                                <td>Rp <?php echo e(number_format($sub->amount, 0, ',', '.')); ?></td>
                                <td><small><?php echo e($sub->paid_at?->format('d M Y H:i')); ?></small></td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada subscription</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Chart.js default config
Chart.defaults.font.family = 'Poppins, sans-serif';
Chart.defaults.color = '#6c757d';

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode($monthlyRevenue->keys()); ?>,
        datasets: [{
            label: 'Revenue (Rp)',
            data: <?php echo json_encode($monthlyRevenue->values()); ?>,
            borderColor: '#ffc107',
            backgroundColor: 'rgba(255, 193, 7, 0.1)',
            tension: 0.4,
            fill: true,
            borderWidth: 3
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + value.toLocaleString('id-ID');
                    }
                }
            }
        }
    }
});

// Payment Method Chart
const paymentMethodCtx = document.getElementById('paymentMethodChart').getContext('2d');
new Chart(paymentMethodCtx, {
    type: 'doughnut',
    data: {
        labels: ['Virtual Account', 'E-Wallet', 'QRIS'],
        datasets: [{
            data: [<?php echo e($paymentMethods['va']); ?>, <?php echo e($paymentMethods['ewallet']); ?>, <?php echo e($paymentMethods['qris']); ?>],
            backgroundColor: [
                '#12a575',
                '#20c997',
                '#0dcaf0'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// User Growth Chart
const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
new Chart(userGrowthCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($userGrowth->keys()); ?>,
        datasets: [{
            label: 'New Users',
            data: <?php echo json_encode($userGrowth->values()); ?>,
            backgroundColor: 'rgba(18, 165, 117, 0.8)',
            borderColor: '#12a575',
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Subscription by Plan Chart
const subscriptionPlanCtx = document.getElementById('subscriptionPlanChart').getContext('2d');
new Chart(subscriptionPlanCtx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($subscriptionsByPlan->keys()); ?>,
        datasets: [{
            data: <?php echo json_encode($subscriptionsByPlan->values()); ?>,
            backgroundColor: [
                '#12a575',
                '#20c997',
                '#0dcaf0',
                '#ffc107',
                '#17a2b8'
            ],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// VA Channel Chart
const vaChannelCtx = document.getElementById('vaChannelChart').getContext('2d');
new Chart(vaChannelCtx, {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($vaChannels->keys()->map(function($key) {
            return str_replace(['VIRTUAL_ACCOUNT_', 'VIRTUAL_ACCOUNT_BANK_'], '', $key);
        })); ?>,
        datasets: [{
            label: 'Transaksi',
            data: <?php echo json_encode($vaChannels->values()); ?>,
            backgroundColor: 'rgba(32, 201, 151, 0.8)',
            borderColor: '#20c997',
            borderWidth: 2,
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        indexAxis: 'y',
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\idorganizer\undangan\resources\views/dashboard-admin.blade.php ENDPATH**/ ?>