@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Manajemen Kampanye</h4>
                    <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary btn-sm">
                        <i class="fa fa-plus"></i> Buat Kampanye Baru
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('success') }}
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        {{ session('error') }}
                    </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nama Kampanye</th>
                                    <th>Kode</th>
                                    <th>Plan</th>
                                    <th>Kuota</th>
                                    <th>Periode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                <tr>
                                    <td>
                                        <strong>{{ $campaign->name }}</strong>
                                        @if($campaign->description)
                                        <br><small class="text-muted">{{ Str::limit($campaign->description, 50) }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <code>{{ $campaign->code }}</code>
                                        <br>
                                        <small class="text-muted">
                                            <a href="{{ route('register') }}?ref={{ $campaign->code }}" target="_blank" class="text-primary">
                                                <i class="fa fa-link"></i> Lihat URL
                                            </a>
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">{{ $campaign->pricingPlan->name }}</span>
                                    </td>
                                    <td>
                                        @if($campaign->max_users > 0)
                                            <strong>{{ $campaign->used_count }}</strong> / {{ $campaign->max_users }}
                                            <br>
                                            <small class="text-muted">Sisa: {{ $campaign->getRemainingSlots() }}</small>
                                        @else
                                            <span class="text-muted">Unlimited</span>
                                            <br>
                                            <small>Terpakai: {{ $campaign->used_count }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($campaign->start_date)
                                            <small>{{ $campaign->start_date->format('d M Y') }}</small>
                                        @else
                                            <small class="text-muted">-</small>
                                        @endif
                                        <br>
                                        @if($campaign->end_date)
                                            <small>{{ $campaign->end_date->format('d M Y') }}</small>
                                        @else
                                            <small class="text-muted">Tanpa batas</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge {{ $campaign->getStatusBadgeClass() }}">
                                            {{ $campaign->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.campaigns.show', $campaign) }}" class="btn btn-info" title="Detail">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.campaigns.edit', $campaign) }}" class="btn btn-warning" title="Edit">
                                                <i class="fa fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.campaigns.toggle-status', $campaign) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-{{ $campaign->is_active ? 'secondary' : 'success' }}" title="{{ $campaign->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                                    <i class="fa fa-{{ $campaign->is_active ? 'ban' : 'check' }}"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.campaigns.destroy', $campaign) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus kampanye ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="Hapus" {{ $campaign->users()->count() > 0 ? 'disabled' : '' }}>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        Belum ada kampanye. <a href="{{ route('admin.campaigns.create') }}">Buat kampanye pertama</a>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $campaigns->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
