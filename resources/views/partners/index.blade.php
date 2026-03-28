@extends('layouts.app')
@section('title', 'Manajemen Partner')
@section('page-title', 'Manajemen Partner')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen Partner</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title">Daftar Partner</h4>
                @can('create-partners')
                <a href="{{ route('partners.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Tambah Partner
                </a>
                @endcan
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                        <strong>Sukses!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polygon points="7.86 2 16.14 2 22 7.86 22 16.14 16.14 22 7.86 22 2 16.14 2 7.86 7.86 2"></polygon><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                        <strong>Error!</strong> {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th width="50">#</th>
                                <th width="100">Logo</th>
                                <th>Nama Partner</th>
                                <th>Website</th>
                                <th width="80">Urutan</th>
                                <th width="100">Status</th>
                                <th width="150">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($partners as $partner)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($partner->logo)
                                        <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="img-thumbnail" style="max-height: 50px; width: auto;">
                                    @else
                                        <span class="badge badge-secondary">No Logo</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $partner->name }}</strong>
                                </td>
                                <td>
                                    @if($partner->site_url)
                                        <a href="{{ $partner->site_url }}" target="_blank" rel="noopener noreferrer">
                                            {{ Str::limit($partner->site_url, 40) }}
                                            <i class="fa fa-external-link-alt fa-xs"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>{{ $partner->order }}</td>
                                <td>
                                    @if($partner->is_active)
                                        <span class="badge badge-success">Aktif</span>
                                    @else
                                        <span class="badge badge-secondary">Nonaktif</span>
                                    @endif
                                </td>
                                <td>
                                    @can('edit-partners')
                                    <a href="{{ route('partners.edit', $partner) }}" class="btn btn-sm btn-warning" title="Edit">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('delete-partners')
                                    <form action="{{ route('partners.destroy', $partner) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus partner ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Hapus">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fa fa-handshake fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Belum ada partner</p>
                                </td>
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
