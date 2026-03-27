@extends('layouts.app')
@section('title', 'Tambah Paket Pricing')
@section('page-title', 'Tambah Paket Pricing')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('pricing-plans.index') }}">Paket Pricing</a></li>
    <li class="breadcrumb-item active">Tambah</li>
@endsection

@section('content')
<div class="card">
    <div class="card-body">
        <form action="{{ route('pricing-plans.store') }}" method="POST">
            @csrf
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nama Paket <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Slug <small class="text-muted">(kosongkan untuk auto-generate)</small></label>
                        <input type="text" name="slug" class="form-control @error('slug') is-invalid @enderror" 
                               value="{{ old('slug') }}">
                        @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Harga (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                               value="{{ old('price', 0) }}" min="0" required>
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <small class="text-muted">Masukkan 0 untuk paket gratis</small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Warna Badge <span class="text-danger">*</span></label>
                        <select name="badge_color" class="form-control @error('badge_color') is-invalid @enderror" required>
                            <option value="primary" {{ old('badge_color') == 'primary' ? 'selected' : '' }}>Primary (Biru)</option>
                            <option value="success" {{ old('badge_color') == 'success' ? 'selected' : '' }}>Success (Hijau)</option>
                            <option value="warning" {{ old('badge_color') == 'warning' ? 'selected' : '' }}>Warning (Kuning)</option>
                            <option value="danger" {{ old('badge_color') == 'danger' ? 'selected' : '' }}>Danger (Merah)</option>
                            <option value="info" {{ old('badge_color') == 'info' ? 'selected' : '' }}>Info (Cyan)</option>
                            <option value="secondary" {{ old('badge_color') == 'secondary' ? 'selected' : '' }}>Secondary (Abu)</option>
                        </select>
                        @error('badge_color')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Undangan <span class="text-danger">*</span></label>
                        <input type="number" name="max_invitations" class="form-control @error('max_invitations') is-invalid @enderror" 
                               value="{{ old('max_invitations', 1) }}" min="1" required>
                        @error('max_invitations')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Foto Gallery <span class="text-danger">*</span></label>
                        <input type="number" name="max_gallery_photos" class="form-control @error('max_gallery_photos') is-invalid @enderror" 
                               value="{{ old('max_gallery_photos', 0) }}" min="0" required>
                        @error('max_gallery_photos')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">Max Upload Musik <span class="text-danger">*</span></label>
                        <input type="number" name="max_music_uploads" class="form-control @error('max_music_uploads') is-invalid @enderror" 
                               value="{{ old('max_music_uploads', 0) }}" min="0" required>
                        @error('max_music_uploads')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Fitur Tambahan</label>
                <div class="form-check">
                    <input type="checkbox" name="gift_section_included" value="1" 
                           class="form-check-input" id="gift_section" {{ old('gift_section_included') ? 'checked' : '' }}>
                    <label class="form-check-label" for="gift_section">Fitur Gift/Amplop Digital</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="can_delete_music" value="1" 
                           class="form-check-input" id="delete_music" {{ old('can_delete_music') ? 'checked' : '' }}>
                    <label class="form-check-label" for="delete_music">Dapat Hapus Musik yang Diupload</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_popular" value="1" 
                           class="form-check-input" id="is_popular" {{ old('is_popular') ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_popular">Tandai sebagai Popular</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="is_active" value="1" 
                           class="form-check-input" id="is_active" {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Aktif</label>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Daftar Fitur <small class="text-muted">(satu per baris)</small></label>
                <div id="features-container">
                    @if(old('features'))
                        @foreach(old('features') as $feature)
                        <div class="input-group mb-2 feature-item">
                            <input type="text" name="features[]" class="form-control" value="{{ $feature }}">
                            <button type="button" class="btn btn-danger btn-remove-feature">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                        @endforeach
                    @else
                        <div class="input-group mb-2 feature-item">
                            <input type="text" name="features[]" class="form-control" placeholder="Contoh: Template Premium">
                            <button type="button" class="btn btn-danger btn-remove-feature">
                                <i class="fa fa-trash"></i>
                            </button>
                        </div>
                    @endif
                </div>
                <button type="button" class="btn btn-sm btn-secondary" id="add-feature">
                    <i class="fa fa-plus"></i> Tambah Fitur
                </button>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> Simpan
                </button>
                <a href="{{ route('pricing-plans.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('add-feature').addEventListener('click', function() {
    const container = document.getElementById('features-container');
    const div = document.createElement('div');
    div.className = 'input-group mb-2 feature-item';
    div.innerHTML = `
        <input type="text" name="features[]" class="form-control" placeholder="Masukkan fitur">
        <button type="button" class="btn btn-danger btn-remove-feature">
            <i class="fa fa-trash"></i>
        </button>
    `;
    container.appendChild(div);
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-remove-feature')) {
        const item = e.target.closest('.feature-item');
        if (document.querySelectorAll('.feature-item').length > 1) {
            item.remove();
        }
    }
});
</script>
@endpush
@endsection
