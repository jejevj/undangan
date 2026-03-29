@extends('layouts.app')
@section('title', 'Funnel Analysis')
@section('page-title', 'Funnel Exploration')

@push('styles')
<style>
.funnel-step {
    background: white;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    transition: all 0.3s;
    position: relative;
}
.funnel-step:hover {
    transform: translateX(5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
}
.funnel-bar {
    height: 40px;
    background: linear-gradient(90deg, #0d6efd 0%, #0a58ca 100%);
    border-radius: 5px;
    display: flex;
    align-items: center;
    padding: 0 15px;
    color: white;
    font-weight: 600;
    position: relative;
    overflow: hidden;
}
.funnel-bar::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    animation: shimmer 2s infinite;
}
@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}
.dropoff-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: #dc3545;
    color: white;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
}
.conversion-badge {
    background: #198754;
    color: white;
    padding: 3px 8px;
    border-radius: 15px;
    font-size: 11px;
    margin-left: 10px;
}
.chart-container {
    position: relative;
    height: 400px;
}
</style>
@endpush

@section('content')
{{-- Date Range Filter --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" 
                               value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" 
                               value="{{ request('end_date', now()->format('Y-m-d')) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary d-block w-100">
                            <i class="fa fa-filter"></i> Apply Filter
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Overall Conversion Rates --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5 class="text-white">Subscription Conversion Rate</h5>
                <h2 class="text-white mb-0">{{ $subscriptionConversion }}%</h2>
                <small class="text-white-50">From viewing plans to payment completed</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5 class="text-white">Invitation Conversion Rate</h5>
                <h2 class="text-white mb-0">{{ $invitationConversion }}%</h2>
                <small class="text-white-50">From viewing templates to publishing</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5 class="text-white">Registration Conversion Rate</h5>
                <h2 class="text-white mb-0">{{ $registrationConversion }}%</h2>
                <small class="text-white-50">From viewing register to success</small>
            </div>
        </div>
    </div>
</div>

{{-- Subscription Funnel --}}
<div class="row">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Subscription Funnel</h4>
            </div>
            <div class="card-body">
                @foreach($subscriptionFunnel as $index => $step)
                <div class="funnel-step">
                    @if($step['dropoff_rate'] > 0)
                    <span class="dropoff-badge">-{{ $step['dropoff_rate'] }}%</span>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $index + 1 }}. {{ $step['label'] }}</strong>
                            <span class="conversion-badge">{{ $step['conversion_rate'] }}%</span>
                        </div>
                        <div class="text-muted">
                            <strong>{{ number_format($step['count']) }}</strong> sessions
                        </div>
                    </div>
                    
                    <div class="funnel-bar" style="width: {{ $step['count'] > 0 ? ($step['count'] / $subscriptionFunnel[0]['count'] * 100) : 0 }}%">
                        {{ number_format($step['count']) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Top Dropoff Points</h4>
            </div>
            <div class="card-body">
                @foreach($subscriptionDropoffs as $dropoff)
                <div class="alert alert-danger">
                    <strong>{{ $dropoff['label'] }}</strong>
                    <div class="mt-2">
                        <span class="badge bg-danger">{{ $dropoff['dropoff_rate'] }}% dropoff</span>
                        <span class="badge bg-secondary">{{ number_format($dropoff['count']) }} users</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Subscription Funnel Chart</h4>
            </div>
            <div class="card-body">
                <canvas id="subscriptionFunnelChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Invitation Funnel --}}
<div class="row mt-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Invitation Funnel</h4>
            </div>
            <div class="card-body">
                @foreach($invitationFunnel as $index => $step)
                <div class="funnel-step">
                    @if($step['dropoff_rate'] > 0)
                    <span class="dropoff-badge">-{{ $step['dropoff_rate'] }}%</span>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $index + 1 }}. {{ $step['label'] }}</strong>
                            <span class="conversion-badge">{{ $step['conversion_rate'] }}%</span>
                        </div>
                        <div class="text-muted">
                            <strong>{{ number_format($step['count']) }}</strong> sessions
                        </div>
                    </div>
                    
                    <div class="funnel-bar" style="width: {{ $step['count'] > 0 ? ($step['count'] / $invitationFunnel[0]['count'] * 100) : 0 }}%; background: linear-gradient(90deg, #198754 0%, #146c43 100%);">
                        {{ number_format($step['count']) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Top Dropoff Points</h4>
            </div>
            <div class="card-body">
                @foreach($invitationDropoffs as $dropoff)
                <div class="alert alert-warning">
                    <strong>{{ $dropoff['label'] }}</strong>
                    <div class="mt-2">
                        <span class="badge bg-warning">{{ $dropoff['dropoff_rate'] }}% dropoff</span>
                        <span class="badge bg-secondary">{{ number_format($dropoff['count']) }} users</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Invitation Funnel Chart</h4>
            </div>
            <div class="card-body">
                <canvas id="invitationFunnelChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>

{{-- Registration Funnel (Anonymous Users) --}}
<div class="row mt-4">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Registration Funnel <span class="badge bg-info">Anonymous Users</span></h4>
            </div>
            <div class="card-body">
                @foreach($registrationFunnel as $index => $step)
                <div class="funnel-step">
                    @if($step['dropoff_rate'] > 0)
                    <span class="dropoff-badge">-{{ $step['dropoff_rate'] }}%</span>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <strong>{{ $index + 1 }}. {{ $step['label'] }}</strong>
                            <span class="conversion-badge">{{ $step['conversion_rate'] }}%</span>
                        </div>
                        <div class="text-muted">
                            <strong>{{ number_format($step['count']) }}</strong> sessions
                        </div>
                    </div>
                    
                    <div class="funnel-bar" style="width: {{ $step['count'] > 0 ? ($step['count'] / $registrationFunnel[0]['count'] * 100) : 0 }}%; background: linear-gradient(90deg, #0dcaf0 0%, #0aa2c0 100%);">
                        {{ number_format($step['count']) }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Top Dropoff Points</h4>
            </div>
            <div class="card-body">
                @forelse($registrationDropoffs as $dropoff)
                <div class="alert alert-info">
                    <strong>{{ $dropoff['label'] }}</strong>
                    <div class="mt-2">
                        <span class="badge bg-info">{{ $dropoff['dropoff_rate'] }}% dropoff</span>
                        <span class="badge bg-secondary">{{ number_format($dropoff['count']) }} users</span>
                    </div>
                </div>
                @empty
                <div class="alert alert-secondary">
                    <small>No significant dropoffs detected</small>
                </div>
                @endforelse
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h4 class="card-title">Registration Funnel Chart</h4>
            </div>
            <div class="card-body">
                <canvas id="registrationFunnelChart" height="300"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Subscription Funnel Chart
const subCtx = document.getElementById('subscriptionFunnelChart').getContext('2d');
new Chart(subCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($subscriptionFunnel)->pluck('label')) !!},
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode(collect($subscriptionFunnel)->pluck('count')) !!},
            backgroundColor: 'rgba(13, 110, 253, 0.8)',
            borderColor: 'rgb(13, 110, 253)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// Invitation Funnel Chart
const invCtx = document.getElementById('invitationFunnelChart').getContext('2d');
new Chart(invCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($invitationFunnel)->pluck('label')) !!},
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode(collect($invitationFunnel)->pluck('count')) !!},
            backgroundColor: 'rgba(25, 135, 84, 0.8)',
            borderColor: 'rgb(25, 135, 84)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});

// Registration Funnel Chart
const regCtx = document.getElementById('registrationFunnelChart').getContext('2d');
new Chart(regCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(collect($registrationFunnel)->pluck('label')) !!},
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode(collect($registrationFunnel)->pluck('count')) !!},
            backgroundColor: 'rgba(13, 202, 240, 0.8)',
            borderColor: 'rgb(13, 202, 240)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            x: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
