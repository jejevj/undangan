@extends('layouts.app')
@section('title', 'Manajemen Paket Pricing')
@section('page-title', 'Manajemen Paket Pricing')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Manajemen Paket Pricing</li>
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('pricing-plans.create') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> Tambah Paket Baru
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Slug</th>
                        <th>Harga</th>
                        <th>Max Undangan</th>
                        <th>Max Foto</th>
                        <th>Max Musik</th>
                        <th>Fitur Gift</th>
                        <th>Subscribers</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $plan)
                    <tr>
                        <td>
                            <strong>{{ $plan->name }}</strong>
                            @if($plan->is_popular)
                                <span class="badge badge-warning ms-1">Popular</span>
                            @endif
                        </td>
                        <td><code>{{ $plan->slug }}</code></td>
                        <td>{{ $plan->formattedPrice() }}</td>
                        <td>{{ $plan->max_invitations }}</td>
                        <td>{{ $plan->max_gallery_photos }}</td>
                        <td>{{ $plan->max_music_uploads }}</td>
                        <td>
                            @if($plan->gift_section_included)
                                <span class="badge badge-success">Ya</span>
                            @else
                                <span class="badge badge-secondary">Tidak</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-info">{{ $plan->subscriptions_count }}</span>
                        </td>
                        <td>
                            @if($plan->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('pricing-plans.edit', $plan) }}" class="btn btn-warning btn-xs">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                <form action="{{ route('pricing-plans.toggle', $plan) }}" method="POST" class="d-inline">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-{{ $plan->is_active ? 'secondary' : 'success' }} btn-xs" 
                                            title="{{ $plan->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fa fa-{{ $plan->is_active ? 'eye-slash' : 'eye' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('pricing-plans.destroy', $plan) }}" method="POST" class="d-inline"
                                    data-confirm="Hapus paket '{{ $plan->name }}'?" 
                                    data-confirm-ok="Hapus" 
                                    data-confirm-title="Hapus Paket">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center">Belum ada paket pricing.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
