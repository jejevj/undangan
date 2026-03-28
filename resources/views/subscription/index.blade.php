@extends('layouts.app')
@section('title', 'Paket Langganan')
@section('page-title', 'Paket Langganan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Paket Langganan</li>
@endsection

@section('content')

{{-- Status paket aktif --}}
<div class="card mb-4">
    <div class="card-body d-flex align-items-center gap-3 flex-wrap">
        <div>
            <span class="text-muted small">Paket Aktif Anda:</span>
            <span class="badge badge-{{ $activePlan->badge_color }} ms-2 fs-6">{{ $activePlan->name }}</span>
            @if($activeSub && $activeSub->expires_at)
                <small class="text-muted ms-2">Berlaku hingga {{ $activeSub->expires_at->format('d M Y') }}</small>
            @elseif($activeSub)
                <small class="text-muted ms-2">Selamanya</small>
            @endif
        </div>
        @php $user = auth()->user(); @endphp
        @if(!$user->isAdmin())
        <div class="ms-auto text-end">
            @php
                $used      = $user->invitationCount();
                $max       = $activePlan->max_invitations;
                $remaining = max(0, $max - $used);
            @endphp
            <div class="small text-muted">Undangan: <strong>{{ $used }}</strong> / {{ $max }}</div>
            <div class="progress mt-1" style="height:5px;width:160px">
                <div class="progress-bar bg-{{ $remaining > 0 ? 'success' : 'danger' }}"
                     style="width:{{ min(100, ($used/$max)*100) }}%"></div>
            </div>
            <small class="text-{{ $remaining > 0 ? 'success' : 'danger' }}">
                {{ $remaining > 0 ? "Sisa {$remaining} undangan" : 'Limit tercapai' }}
            </small>
        </div>
        @endif
    </div>
</div>

{{-- Pricing Cards --}}
<div class="row justify-content-center g-4">
    @foreach($plans as $plan)
    @php 
        $isCurrent = $activePlan->id === $plan->id;
        $isLowerTier = $plan->isLowerThan($activePlan);
        $isHigherTier = $plan->isHigherThan($activePlan);
    @endphp
    <div class="col-lg-4 col-md-6">
        <div class="card h-100 {{ $plan->is_popular ? 'border-primary' : '' }} {{ $isCurrent ? 'border-success' : '' }} {{ $isLowerTier ? 'opacity-75' : '' }}"
             style="{{ $plan->is_popular ? 'border-width:2px' : '' }}">

            @if($plan->is_popular && !$isLowerTier)
                <div class="card-header bg-primary text-white text-center py-2">
                    <small class="fw-bold">⭐ PALING POPULER</small>
                </div>
            @endif
            @if($isCurrent)
                <div class="card-header bg-success text-white text-center py-2">
                    <small class="fw-bold">✓ PAKET AKTIF ANDA</small>
                </div>
            @endif
            @if($isLowerTier && !$isCurrent)
                <div class="card-header bg-secondary text-white text-center py-2">
                    <small class="fw-bold">🔒 PAKET LEBIH RENDAH</small>
                </div>
            @endif
            @if($isHigherTier)
                <div class="card-header bg-info text-white text-center py-2">
                    <small class="fw-bold">⬆️ UPGRADE</small>
                </div>
            @endif

            <div class="card-body text-center">
                <h4 class="card-title">
                    <span class="badge badge-{{ $plan->badge_color }} mb-2">{{ $plan->name }}</span>
                </h4>

                <div class="mb-3">
                    @if($plan->price === 0)
                        <span class="display-6 fw-bold text-success">Gratis</span>
                    @else
                        <span class="display-6 fw-bold">Rp {{ number_format($plan->price, 0, ',', '.') }}</span>
                        <small class="text-muted d-block">sekali bayar</small>
                    @endif
                </div>

                <ul class="list-unstyled text-start mb-4">
                    @foreach($plan->features ?? [] as $feature)
                    <li class="mb-2">
                        <i class="fa fa-check-circle text-success me-2"></i>{{ $feature }}
                    </li>
                    @endforeach
                </ul>

                {{-- Batas detail --}}
                <div class="bg-light rounded p-3 text-start small mb-4">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Undangan</span>
                        <strong>{{ $plan->max_invitations }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Foto Galeri</span>
                        <strong>{{ $plan->max_gallery_photos ?? 'Unlimited' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Upload Lagu</span>
                        <strong>{{ $plan->max_music_uploads === null ? 'Unlimited' : ($plan->max_music_uploads === 0 ? 'Tidak bisa' : $plan->max_music_uploads . ' lagu') }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Gift Section</span>
                        <strong class="text-{{ $plan->gift_section_included ? 'success' : 'warning' }}">
                            {{ $plan->gift_section_included ? 'Gratis' : 'Rp 10.000' }}
                        </strong>
                    </div>
                </div>

                @if($isCurrent)
                    <button class="btn btn-success w-100" disabled>Paket Aktif</button>
                @elseif($plan->price === 0)
                    <button class="btn btn-outline-secondary w-100" disabled>Paket Default</button>
                @elseif($plan->isLowerThan($activePlan))
                    <button class="btn btn-outline-secondary w-100" disabled>
                        <i class="fa fa-lock me-1"></i>Paket Lebih Rendah
                    </button>
                    <small class="text-muted d-block mt-2">Anda sudah menggunakan paket {{ $activePlan->name }}</small>
                @else
                    <a href="{{ route('subscription.checkout', $plan) }}" class="btn btn-{{ $plan->badge_color }} w-100">
                        @if($plan->isHigherThan($activePlan))
                            <i class="fa fa-arrow-up me-1"></i>Upgrade ke {{ $plan->name }}
                        @else
                            Pilih Paket {{ $plan->name }}
                        @endif
                    </a>
                @endif
            </div>
        </div>
    </div>
    @endforeach
</div>

@endsection
