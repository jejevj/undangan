@extends('layouts.app')

@section('title', 'Manajemen Permission')
@section('page-title', 'Manajemen Permission')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen Permission</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Tambah Permission</h4></div>
            <div class="card-body">
                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Nama Permission <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="contoh: view-users" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Daftar Permission</h4></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr><th>#</th><th>Nama Permission</th><th>Aksi</th></tr>
                        </thead>
                        <tbody>
                            @forelse($permissions as $i => $permission)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $permission->name }}</td>
                                <td>
                                    <form action="{{ route('permissions.destroy', $permission) }}" method="POST" class="d-inline"
                                        data-confirm="Hapus permission '{{ $permission->name }}'?" data-confirm-ok="Hapus" data-confirm-title="Hapus Permission">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center">Belum ada permission.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
