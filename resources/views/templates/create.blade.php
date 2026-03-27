@extends('layouts.app')
@section('title', 'Tambah Template')
@section('page-title', 'Tambah Template')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Template</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection
@section('content')
<div class="row">
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Form Tambah Template</h4></div>
            <div class="card-body">
                <form action="{{ route('templates.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Nama Template <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Premium White 1" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Blade View <span class="text-danger">*</span></label>
                        <input type="text" name="blade_view" class="form-control @error('blade_view') is-invalid @enderror" value="{{ old('blade_view') }}" placeholder="invitation-templates.premium-white-1" required>
                        <small class="text-muted">Path blade view, contoh: <code>invitation-templates.premium-white-1</code></small>
                        @error('blade_view')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Aktif</label>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('templates.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
