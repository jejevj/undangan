@extends('layouts.app')

@section('title', 'Tambah Menu')
@section('page-title', 'Tambah Menu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('menus.index') }}">Manajemen Menu</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Form Tambah Menu</h4></div>
            <div class="card-body">
                <form action="{{ route('menus.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label>Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}" required>
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>URL</label>
                            <input type="text" name="url" class="form-control" value="{{ old('url') }}" placeholder="/contoh/url">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Icon Class</label>
                            <input type="text" name="icon" class="form-control" value="{{ old('icon', 'flaticon-381-networking') }}" placeholder="flaticon-381-networking">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Parent Menu</label>
                            <select name="parent_id" class="form-control">
                                <option value="">-- Tidak ada (menu utama) --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label>Urutan</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', 0) }}" min="0">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Permission (opsional)</label>
                            <select name="permission_name" class="form-control">
                                <option value="">-- Tidak ada --</option>
                                @foreach($permissions as $perm)
                                    <option value="{{ $perm->name }}" {{ old('permission_name') == $perm->name ? 'selected' : '' }}>{{ $perm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
