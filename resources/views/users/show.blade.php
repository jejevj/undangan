@extends('layouts.app')
@section('title', 'Detail User — ' . $user->name)
@section('page-title', 'Detail User')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Manajemen User</a></li>
    <li class="breadcrumb-item active">{{ $user->name }}</li>
@endsection

@section('content')
<div class="row">

    {{-- ── Kolom Kiri: Info User ──────────────────────────────────── --}}
    <div class="col-lg-4">

        {{-- Profil --}}
        <div class="card mb-3">
            <div class="card-body text-center py-4">
                <div class="rounded-circle bg-primary d-inline-flex align-items-center justify-content-center mb-3"
                     style="width:64px;height:64px;font-size:1.5rem;color:#fff">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <h5 class="mb-1">{{ $user->name }}</h5>
                <p class="text-muted small mb-2">{{ $user->email }}</p>
                <div class="d-flex justify-content-center gap-1 flex-wrap">
                    @foreach($user->roles as $role)
                        <span class="badge badge-primary">{{ $role->name }}</span>
                    @endforeach
                </div>
                <div class="mt-3 d-flex gap-2 justify-content-center">
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-pencil"></i> Edit
                    </a>
                </div>
            </div>
        </div>

        {{-- Statistik --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title mb-0">Statistik</h5></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <tr>
                        <td class="text-muted ps-3">Total Undangan</td>
                        <td class="fw-bold">{{ $user->invitations->count() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Published</td>
                        <td class="fw-bold text-success">{{ $user->invitations->where('status', 'published')->count() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Draft</td>
                        <td class="fw-bold text-warning">{{ $user->invitations->where('status', 'draft')->count() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted ps-3">Bergabung</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Info Paket Aktif (Read-only) --}}
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    Paket Aktif
                    @if($user->isAdmin())
                        <span class="badge badge-dark badge-sm ms-1">Admin</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($user->isAdmin())
                    <div class="alert alert-dark mb-0">
                        <i class="fa fa-shield"></i> 
                        <strong>User ini adalah Admin.</strong><br>
                        <small>Admin memiliki akses unlimited ke semua fitur.</small>
                    </div>
                @else
                    @if($activePlan)
                    <div class="text-center mb-3">
                        <span class="badge badge-{{ $activePlan->badge_color }}" style="font-size:1rem;padding:8px 20px">
                            {{ $activePlan->name }}
                        </span>
                        @if($activeSub)
                            <div class="small text-muted mt-1">
                                Sejak {{ $activeSub->starts_at?->format('d M Y') }}
                                @if($activeSub->expires_at)
                                    · Hingga {{ $activeSub->expires_at->format('d M Y') }}
                                @else
                                    · Selamanya
                                @endif
                            </div>
                            <div class="small text-muted">
                                via {{ $activeSub->payment_method === 'admin_assign' ? 'Admin' : ucfirst($activeSub->payment_method) }}
                            </div>
                        @endif
                    </div>

                    {{-- Usage --}}
                    @php
                        $used = $user->invitationCount();
                        $max  = $activePlan->max_invitations;
                    @endphp
                    <div class="small text-muted mb-1">Undangan: {{ $used }} / {{ $max }}</div>
                    <div class="progress mb-3" style="height:5px">
                        <div class="progress-bar bg-{{ $used >= $max ? 'danger' : 'success' }}"
                             style="width:{{ min(100, ($used/$max)*100) }}%"></div>
                    </div>
                    @endif

                    <div class="alert alert-info mb-0">
                        <i class="fa fa-pencil"></i> 
                        <strong>Untuk mengubah paket:</strong><br>
                        <small>Buka halaman <a href="{{ route('users.edit', $user) }}" class="alert-link">Edit User</a></small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Kolom Kanan: Undangan & Riwayat ────────────────────────── --}}
    <div class="col-lg-8">

        {{-- Daftar Undangan --}}
        <div class="card mb-3">
            <div class="card-header"><h5 class="card-title mb-0">Undangan ({{ $user->invitations->count() }})</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Judul</th><th>Template</th><th>Status</th><th>Dibuat</th></tr>
                        </thead>
                        <tbody>
                            @forelse($user->invitations as $inv)
                            <tr>
                                <td>{{ $inv->title }}</td>
                                <td><small>{{ $inv->template?->name }}</small></td>
                                <td>
                                    <span class="badge badge-{{ $inv->status === 'published' ? 'success' : 'warning' }}">
                                        {{ ucfirst($inv->status) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $inv->created_at->format('d M Y') }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center text-muted py-3">Belum ada undangan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Riwayat Subscription --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Riwayat Langganan</h5></div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>No. Order</th><th>Paket</th><th>Nominal</th><th>Metode</th><th>Status</th><th>Tanggal</th></tr>
                        </thead>
                        <tbody>
                            @forelse($user->subscriptions->sortByDesc('created_at') as $sub)
                            <tr>
                                <td><small class="font-monospace">{{ $sub->order_number }}</small></td>
                                <td>
                                    <span class="badge badge-{{ $sub->plan->badge_color }}">{{ $sub->plan->name }}</span>
                                </td>
                                <td>{{ $sub->amount > 0 ? 'Rp ' . number_format($sub->amount, 0, ',', '.') : 'Gratis' }}</td>
                                <td><small>{{ $sub->payment_method }}</small></td>
                                <td>
                                    <span class="badge badge-{{ $sub->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($sub->status) }}
                                    </span>
                                </td>
                                <td><small class="text-muted">{{ $sub->created_at->format('d M Y') }}</small></td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">Belum ada riwayat.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
