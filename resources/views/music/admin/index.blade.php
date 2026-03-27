@extends('layouts.app')
@section('title', 'Manajemen Musik')
@section('page-title', 'Manajemen Musik')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen Musik</li>
@endsection

@section('content')
<div class="mb-3">
    <a href="{{ route('music.admin.create') }}" class="btn btn-primary btn-sm">
        <i class="fa fa-plus"></i> Upload Lagu
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Judul</th>
                        <th>Artis</th>
                        <th>Tipe</th>
                        <th>Harga</th>
                        <th>Durasi</th>
                        <th>Pengguna</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($songs as $i => $song)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>
                            <div class="fw-bold">{{ $song->title }}</div>
                            <small class="text-muted">{{ basename($song->file_path) }}</small>
                        </td>
                        <td>{{ $song->artist ?? '—' }}</td>
                        <td>
                            <span class="badge badge-{{ $song->isFree() ? 'success' : 'warning' }}">
                                {{ ucfirst($song->type) }}
                            </span>
                        </td>
                        <td>{{ $song->formattedPrice() }}</td>
                        <td>{{ $song->duration ?? '—' }}</td>
                        <td>{{ $song->users_count }}</td>
                        <td>
                            <span class="badge badge-{{ $song->is_active ? 'success' : 'danger' }}">
                                {{ $song->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <form action="{{ route('music.admin.toggle', $song) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button class="btn btn-{{ $song->is_active ? 'outline-secondary' : 'outline-success' }} btn-xs"
                                        title="{{ $song->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                        <i class="fa fa-{{ $song->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('music.admin.destroy', $song) }}" method="POST"
                                    data-confirm="Hapus lagu '{{ $song->title }}'? File audio akan ikut terhapus."
                                    data-confirm-ok="Hapus" data-confirm-title="Hapus Lagu">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="9" class="text-center text-muted py-4">Belum ada lagu.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
