@extends('layouts.app')
@section('title', 'Upload Lagu')
@section('page-title', 'Upload Lagu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('music.admin.index') }}">Manajemen Musik</a></li>
    <li class="breadcrumb-item active">Upload</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Form Upload Lagu</h4></div>
            <div class="card-body">
                <form action="{{ route('music.admin.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mb-3">
                        <label>Judul Lagu <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Artis</label>
                        <input type="text" name="artist" class="form-control" value="{{ old('artist') }}">
                    </div>

                    <div class="row">
                        <div class="col-6 form-group mb-3">
                            <label>Tipe <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" id="typeSelect">
                                <option value="free"    {{ old('type') === 'free'    ? 'selected' : '' }}>Gratis</option>
                                <option value="premium" {{ old('type') === 'premium' ? 'selected' : '' }}>Premium</option>
                            </select>
                        </div>
                        <div class="col-6 form-group mb-3" id="priceGroup">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price" class="form-control"
                                value="{{ old('price', 10000) }}" min="0" step="1000">
                        </div>
                    </div>

                    <div class="form-group mb-3">
                        <label>File Audio (MP3) <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                            accept=".mp3,.ogg,.wav" required>
                        <small class="text-muted">Format: MP3/OGG/WAV, maks 15MB. Disimpan di <code>public/invitation-assets/music/</code></small>
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Durasi</label>
                        <input type="text" name="duration" class="form-control" value="{{ old('duration') }}" placeholder="3:45">
                    </div>

                    <div class="form-group mb-3">
                        <label>Cover (opsional)</label>
                        <input type="file" name="cover" class="form-control" accept="image/*">
                    </div>

                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" checked>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Upload</button>
                        <a href="{{ route('music.admin.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const typeSelect = document.getElementById('typeSelect');
const priceGroup = document.getElementById('priceGroup');
function togglePrice() {
    priceGroup.style.opacity = typeSelect.value === 'free' ? '.4' : '1';
}
typeSelect?.addEventListener('change', togglePrice);
togglePrice();
</script>
@endpush
