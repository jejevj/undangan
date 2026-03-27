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
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label>Nama Template <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="Premium White 1" required>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Kategori Template</label>
                            <select name="category_id" class="form-control @error('category_id') is-invalid @enderror">
                                <option value="">-- Pilih Kategori --</option>
                                @foreach(\App\Models\TemplateCategory::where('is_active', true)->where('slug', '!=', 'all')->orderBy('order')->get() as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label>Tipe <span class="text-danger">*</span></label>
                            <select name="type" class="form-control">
                                <option value="free"    {{ old('type') === 'free'    ? 'selected' : '' }}>Gratis</option>
                                <option value="premium" {{ old('type') === 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="custom"  {{ old('type') === 'custom'  ? 'selected' : '' }}>Custom Order</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Harga Template (Rp)</label>
                            <input type="number" name="price" class="form-control" value="{{ old('price', 0) }}" min="0" step="1000">
                            <small class="text-muted">Isi 0 untuk gratis</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label>Batas Foto Gratis</label>
                            <input type="number" name="free_photo_limit" class="form-control"
                                value="{{ old('free_photo_limit', 2) }}" min="0" placeholder="Kosongkan = unlimited">
                            <small class="text-muted">Kosongkan untuk unlimited. Default 2 untuk free.</small>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Harga Foto Tambahan (Rp)</label>
                            <input type="number" name="extra_photo_price" class="form-control"
                                value="{{ old('extra_photo_price', 5000) }}" min="0" step="1000">
                            <small class="text-muted">Per foto di luar kuota gratis</small>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label>Nama Folder Assets <span class="text-danger">*</span></label>
                            <input type="text" name="asset_folder" class="form-control @error('asset_folder') is-invalid @enderror"
                                value="{{ old('asset_folder') }}" placeholder="premium-white-1" required>
                            <small class="text-muted"><code>public/invitation-assets/{folder}/</code></small>
                            @error('asset_folder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label>Versi</label>
                            <input type="text" name="version" class="form-control" value="{{ old('version', '1.0.0') }}" placeholder="1.0.0">
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label>Blade View <span class="text-danger">*</span></label>
                        <input type="text" name="blade_view" class="form-control @error('blade_view') is-invalid @enderror" value="{{ old('blade_view') }}" placeholder="invitation-templates.premium-white-1.index" required>
                        <small class="text-muted">Otomatis: <code>invitation-templates.{folder}.index</code></small>
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
                        <label>Default Fields</label>
                        <select name="field_preset" class="form-control">
                            @foreach($presets as $key => $label)
                                <option value="{{ $key }}" {{ $key === 'wedding_standard' ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Field akan otomatis dimuat setelah template dibuat. Bisa diedit/ditambah setelahnya.</small>
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
