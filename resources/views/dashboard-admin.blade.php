@extends('layouts.app')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')

@push('styles')
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
@endpush

@section('content')
{{-- Statistics Cards --}}
<div class="row">
    {{-- Total Users --}}
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Total Users</h6>
                    <h3 class="text-white mb-0">{{ number_format($totalUsers) }}</h3>
                    <small class="text-white-50">+{{ $newUsersThisMonth }} bulan ini</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-users"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Subscriptions --}}
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card bg-success text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Subscription Aktif</h6>
                    <h3 class="text-white mb-0">{{ number_format($activeSubscriptions) }}</h3>
                    <small class="text-white-50">Paket berbayar</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-check-circle"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Invitations --}}
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card bg-info text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Total Undangan</h6>
                    <h3 class="text-white mb-0">{{ number_format($totalInvitations) }}</h3>
                    <small class="text-white-50">{{ $publishedInvitations }} published</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-envelope"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Total Revenue --}}
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="stat-card bg-warning text-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-white mb-1">Total Revenue</h6>
                    <h3 class="text-white mb-0">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                    <small class="text-white-50">Rp {{ number_format($revenueThisMonth, 0, ',', '.') }} bulan ini</small>
                </div>
                <div class="stat-icon">
                    <i class="fa fa-money"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Charts Row 1 --}}
<div class="row">
    {{-- Monthly Revenue Chart --}}
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

    {{-- Payment Methods Chart --}}
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

{{-- Charts Row 2 --}}
<div class="row">
    {{-- User Growth Chart --}}
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

    {{-- Subscription by Plan Chart --}}
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

{{-- VA Channel Usage --}}
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

    {{-- Recent Subscriptions --}}
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
                            @forelse($recentSubscriptions as $sub)
                            <tr>
                                <td>
                                    <div class="font-weight-bold">{{ $sub->user->name }}</div>
                                    <small class="text-muted">{{ $sub->user->email }}</small>
                                </td>
                                <td><span class="badge badge-primary">{{ $sub->plan->name }}</span></td>
                                <td>Rp {{ number_format($sub->amount, 0, ',', '.') }}</td>
                                <td><small>{{ $sub->paid_at?->format('d M Y H:i') }}</small></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">Belum ada subscription</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
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
        labels: {!! json_encode($monthlyRevenue->keys()) !!},
        datasets: [{
            label: 'Revenue (Rp)',
            data: {!! json_encode($monthlyRevenue->values()) !!},
            borderColor: 'rgb(255, 193, 7)',
            backgroundColor: 'rgba(255, 193, 7, 0.1)',
            tension: 0.4,
            fill: true
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
            data: [{{ $paymentMethods['va'] }}, {{ $paymentMethods['ewallet'] }}, {{ $paymentMethods['qris'] }}],
            backgroundColor: [
                'rgb(13, 110, 253)',
                'rgb(25, 135, 84)',
                'rgb(220, 53, 69)'
            ]
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
        labels: {!! json_encode($userGrowth->keys()) !!},
        datasets: [{
            label: 'New Users',
            data: {!! json_encode($userGrowth->values()) !!},
            backgroundColor: 'rgba(13, 110, 253, 0.8)',
            borderColor: 'rgb(13, 110, 253)',
            borderWidth: 1
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
        labels: {!! json_encode($subscriptionsByPlan->keys()) !!},
        datasets: [{
            data: {!! json_encode($subscriptionsByPlan->values()) !!},
            backgroundColor: [
                'rgb(13, 110, 253)',
                'rgb(25, 135, 84)',
                'rgb(255, 193, 7)',
                'rgb(220, 53, 69)',
                'rgb(13, 202, 240)'
            ]
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
        labels: {!! json_encode($vaChannels->keys()->map(function($key) {
            return str_replace(['VIRTUAL_ACCOUNT_', 'VIRTUAL_ACCOUNT_BANK_'], '', $key);
        })) !!},
        datasets: [{
            label: 'Transaksi',
            data: {!! json_encode($vaChannels->values()) !!},
            backgroundColor: 'rgba(25, 135, 84, 0.8)',
            borderColor: 'rgb(25, 135, 84)',
            borderWidth: 1
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
@endpush
