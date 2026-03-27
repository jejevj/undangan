@extends('layouts.app')

@section('title', 'Edit Menu')
@section('page-title', 'Edit Menu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('menus.index') }}">Manajemen Menu</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Form Edit Menu: {{ $menu->name }}</h4></div>
            <div class="card-body">
                <form action="{{ route('menus.update', $menu) }}" method="POST">
                    @csrf @method('PUT')
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label>Nama Menu <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $menu->name) }}" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Slug <span class="text-danger">*</span></label>
                            <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug', $menu->slug) }}" required>
                            @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>URL</label>
                            <input type="text" name="url" class="form-control" value="{{ old('url', $menu->url) }}">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Icon Class</label>
                            <input type="text" name="icon" class="form-control" value="{{ old('icon', $menu->icon) }}">
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Parent Menu</label>
                            <select name="parent_id" class="form-control">
                                <option value="">-- Tidak ada (menu utama) --</option>
                                @foreach($parents as $parent)
                                    <option value="{{ $parent->id }}" {{ old('parent_id', $menu->parent_id) == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label>Urutan</label>
                            <input type="number" name="order" class="form-control" value="{{ old('order', $menu->order) }}" min="0">
                        </div>
                        <div class="col-md-3 form-group mb-3">
                            <label>Status</label>
                            <select name="is_active" class="form-control">
                                <option value="1" {{ old('is_active', $menu->is_active ? '1' : '0') == '1' ? 'selected' : '' }}>Aktif</option>
                                <option value="0" {{ old('is_active', $menu->is_active ? '1' : '0') == '0' ? 'selected' : '' }}>Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Permission (opsional)</label>
                            <select name="permission_name" class="form-control">
                                <option value="">-- Tidak ada --</option>
                                @foreach($permissions as $perm)
                                    <option value="{{ $perm->name }}" {{ old('permission_name', $menu->permission_name) == $perm->name ? 'selected' : '' }}>{{ $perm->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="{{ route('menus.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
