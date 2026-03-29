@extends('layouts.app')
@section('title', 'Funnel Analysis')
@section('page-title', 'Funnel Exploration')

@push('styles')
<style>
.funnel-step {
    background: white;
    border-radius: 8px;
    padding: 15px;
    margin-bottom: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    transition: all 0.2s;
    position: relative;
}
.funnel-step:last-child {
    margin-bottom: 0;
}
.funnel-step:hover {
    transform: translateX(3px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
.funnel-bar {
    height: 35px;
    background: linear-gradient(90deg, #0d6efd 0%, #0a58ca 100%);
    border-radius: 4px;
    display: flex;
    align-items: center;
    padding: 0 12px;
    color: white;
    font-weight: 600;
    font-size: 14px;
}
.funnel-bar.green {
    background: linear-gradient(90deg, #198754 0%, #146c43 100%);
}
.funnel-bar.cyan {
    background: linear-gradient(90deg, #0dcaf0 0%, #0aa2c0 100%);
}
.dropoff-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: #dc3545;
    color: white;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}
.conversion-badge {
    background: #198754;
    color: white;
    padding: 2px 6px;
    border-radius: 10px;
    font-size: 10px;
    margin-left: 8px;
}
.stat-card {
    background: white;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
.stat-card h2 {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 10px 0;
}
.stat-card.primary { border-top: 4px solid #0d6efd; }
.stat-card.success { border-top: 4px solid #198754; }
.stat-card.info { border-top: 4px solid #0dcaf0; }
</style>
@endpush

@section('content')
{{-- Date Range Filter --}}
<div class="card mb-3">
    <div class="card-body py-3">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label mb-1 small">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" 
                       value="{{ request('start_date', now()->subDays(30)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label mb-1 small">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" 
                       value="{{ request('end_date', now()->format('Y-m-d')) }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="fa fa-filter"></i> Apply Filter
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Overall Conversion Rates --}}
<div class="row g-3 mb-3">
    <div class="col-md-4">
        <div class="stat-card primary">
            <div class="small text-muted">Subscription Conversion</div>
            <h2 class="text-primary">{{ $subscriptionConversion }}%</h2>
            <small class="text-muted">Plans → Payment</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card success">
            <div class="small text-muted">Invitation Conversion</div>
            <h2 class="text-success">{{ $invitationConversion }}%</h2>
            <small class="text-muted">Templates → Publish</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card info">
            <div class="small text-muted">Registration Conversion</div>
            <h2 class="text-info">{{ $registrationConversion }}%</h2>
            <small class="text-muted">View → Success</small>
        </div>
    </div>
</div>

{{-- Subscription Funnel --}}
@if(count($subscriptionFunnel) > 0 && $subscriptionFunnel[0]['count'] > 0)
<div class="card mb-3">
    <div class="card-header py-2">
        <h5 class="mb-0">Subscription Funnel</h5>
    </div>
    <div class="card-body">
        @foreach($subscriptionFunnel as $index => $step)
        <div class="funnel-step">
            @if($step['dropoff_rate'] > 0)
            <span class="dropoff-badge">-{{ $step['dropoff_rate'] }}%</span>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong style="font-size: 14px;">{{ $index + 1 }}. {{ $step['label'] }}</strong>
                    <span class="conversion-badge">{{ $step['conversion_rate'] }}%</span>
                </div>
                <div class="text-muted small">
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
@endif

{{-- Invitation Funnel --}}
@if(count($invitationFunnel) > 0 && $invitationFunnel[0]['count'] > 0)
<div class="card mb-3">
    <div class="card-header py-2">
        <h5 class="mb-0">Invitation Funnel</h5>
    </div>
    <div class="card-body">
        @foreach($invitationFunnel as $index => $step)
        <div class="funnel-step">
            @if($step['dropoff_rate'] > 0)
            <span class="dropoff-badge">-{{ $step['dropoff_rate'] }}%</span>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong style="font-size: 14px;">{{ $index + 1 }}. {{ $step['label'] }}</strong>
                    <span class="conversion-badge">{{ $step['conversion_rate'] }}%</span>
                </div>
                <div class="text-muted small">
                    <strong>{{ number_format($step['count']) }}</strong> sessions
                </div>
            </div>
            <div class="funnel-bar green" style="width: {{ $step['count'] > 0 ? ($step['count'] / $invitationFunnel[0]['count'] * 100) : 0 }}%">
                {{ number_format($step['count']) }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Registration Funnel --}}
@if(count($registrationFunnel) > 0 && $registrationFunnel[0]['count'] > 0)
<div class="card mb-3">
    <div class="card-header py-2">
        <h5 class="mb-0">Registration Funnel <span class="badge bg-info">Anonymous Users</span></h5>
    </div>
    <div class="card-body">
        @foreach($registrationFunnel as $index => $step)
        <div class="funnel-step">
            @if($step['dropoff_rate'] > 0)
            <span class="dropoff-badge">-{{ $step['dropoff_rate'] }}%</span>
            @endif
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <strong style="font-size: 14px;">{{ $index + 1 }}. {{ $step['label'] }}</strong>
                    <span class="conversion-badge">{{ $step['conversion_rate'] }}%</span>
                </div>
                <div class="text-muted small">
                    <strong>{{ number_format($step['count']) }}</strong> sessions
                </div>
            </div>
            <div class="funnel-bar cyan" style="width: {{ $step['count'] > 0 ? ($step['count'] / $registrationFunnel[0]['count'] * 100) : 0 }}%">
                {{ number_format($step['count']) }}
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- Empty State --}}
@if((count($subscriptionFunnel) == 0 || $subscriptionFunnel[0]['count'] == 0) && 
    (count($invitationFunnel) == 0 || $invitationFunnel[0]['count'] == 0) && 
    (count($registrationFunnel) == 0 || $registrationFunnel[0]['count'] == 0))
<div class="card">
    <div class="card-body text-center py-5">
        <i class="fa fa-chart-line fa-3x text-muted mb-3"></i>
        <h5>Belum Ada Data Funnel</h5>
        <p class="text-muted">Data akan muncul setelah ada aktivitas user di aplikasi.</p>
        <small class="text-muted">
            Gunakan command <code>php artisan funnel:generate-sample --days=7</code> untuk generate sample data.
        </small>
    </div>
</div>
@endif

@endsection
