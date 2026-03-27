@extends('layouts.app')
@section('title', 'Upload Lagu')
@section('page-title', 'Upload Lagu Saya')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('music.index') }}">Musik</a></li>
    <li class="breadcrumb-item active">Upload</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Upload Lagu Sendiri</h4></div>
            <div class="card-body">

                <div class="alert alert-info mb-4">
                    <i class="fa fa-info-circle"></i>
                    Lagu yang Anda upload hanya bisa digunakan untuk undangan Anda sendiri.
                    Format: <strong>MP3, OGG, WAV</strong> — maks <strong>15MB</strong>.
                </div>

                <div class="alert alert-warning mb-4">
                    <i class="fa fa-money"></i>
                    <strong>Biaya Upload: Rp {{ number_format($uploadFee, 0, ',', '.') }}</strong> per lagu.
                    <br>
                    <small>Pembayaran akan dilakukan setelah Anda mengisi form ini.</small>
                </div>

                <form action="{{ route('music.upload.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group mb-3">
                        <label>Judul Lagu <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="form-group mb-3">
                        <label>Artis / Penyanyi</label>
                        <input type="text" name="artist" class="form-control" value="{{ old('artist') }}">
                    </div>

                    <div class="form-group mb-3">
                        <label>File Audio <span class="text-danger">*</span></label>
                        <input type="file" name="file" class="form-control @error('file') is-invalid @enderror"
                            accept=".mp3,.ogg,.wav" required id="audioFileInput">
                        @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        {{-- Preview sebelum upload --}}
                        <div id="localPreviewWrap" class="mt-2" style="display:none">
                            <audio id="localPreview" controls class="w-100" style="height:32px"></audio>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-arrow-right"></i> Lanjut ke Pembayaran
                        </button>
                        <a href="{{ route('music.index') }}" class="btn btn-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview audio sebelum upload
document.getElementById('audioFileInput')?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const url  = URL.createObjectURL(file);
    const wrap = document.getElementById('localPreviewWrap');
    const audio = document.getElementById('localPreview');
    audio.src = url;
    wrap.style.display = '';
});
</script>
@endpush
