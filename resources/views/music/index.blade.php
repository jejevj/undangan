@extends('layouts.app')
@section('title', 'Pilih Musik Undangan')
@section('page-title', 'Musik Undangan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Musik</li>
@endsection

@section('content')
<p class="text-muted mb-4">
    Pilih lagu latar untuk undangan Anda. Lagu gratis langsung bisa digunakan.
    @if(!$hasPremiumAccess)
    Lagu premium perlu dibeli terlebih dahulu.
    @endif
</p>

<div class="d-flex gap-2 mb-4 flex-wrap">
    @php $activePlan = auth()->user()->activePlan(); @endphp
    
    @if($hasPremiumAccess)
        <div class="alert alert-success py-2 px-3 mb-0 flex-fill">
            <i class="fa fa-crown"></i>
            Paket <strong>{{ $activePlan->name }}</strong> — Semua lagu premium gratis!
        </div>
    @else
        <div class="alert alert-info py-2 px-3 mb-0">
            <i class="fa fa-info-circle"></i>
            Paket <strong>{{ $activePlan->name }}</strong> — Hanya lagu gratis
        </div>
        <a href="{{ route('subscription.index') }}" class="btn btn-warning btn-sm">
            <i class="fa fa-crown"></i> Upgrade untuk Akses Premium
        </a>
    @endif
</div>

<div class="row">
    @forelse($allSongs as $song)
    @php 
        $owned = $song->isFree() 
              || in_array($song->id, $myIds) 
              || $song->uploaded_by === auth()->id()
              || ($hasPremiumAccess && $song->type === 'premium');
        $canUse = $owned; // Bisa digunakan di undangan
    @endphp
    <div class="col-xl-3 col-md-4 col-sm-6 mt-3">
        <div class="card h-100 {{ !$owned ? 'border-warning' : '' }}">
            <div class="card-body">
                {{-- Cover / icon --}}
                <div class="d-flex align-items-center gap-3 mb-3">
                    @if($song->cover)
                        <img src="{{ asset('storage/' . $song->cover) }}"
                             class="rounded" style="width:48px;height:48px;object-fit:cover" alt="">
                    @else
                        <div class="rounded d-flex align-items-center justify-content-center bg-light"
                             style="width:48px;height:48px;font-size:1.5rem">🎵</div>
                    @endif
                    <div class="flex-fill min-width-0">
                        <div class="fw-bold text-truncate">{{ $song->title }}</div>
                        <small class="text-muted">{{ $song->artist ?? '—' }}</small>
                    </div>
                </div>

                {{-- Preview audio --}}
                <audio controls class="w-100 mb-2" style="height:32px">
                    <source src="{{ $song->audioUrl() }}" type="audio/mpeg">
                </audio>

                {{-- Badge & harga --}}
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge badge-{{ $song->isFree() ? 'success' : 'warning' }}">
                        {{ $song->isFree() ? 'Gratis' : 'Premium' }}
                    </span>
                    @if($song->isUserUpload() && $song->uploaded_by === auth()->id())
                        <span class="badge badge-info">Upload Saya</span>
                    @endif
                    @if(!$song->isFree() && !$hasPremiumAccess)
                        <span class="small fw-bold">{{ $song->formattedPrice() }}</span>
                    @endif
                    @if($song->duration)
                        <span class="text-muted small ms-auto">{{ $song->duration }}</span>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                @if($canUse)
                    {{-- Sudah punya akses — tampilkan URL untuk disalin --}}
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control form-control-sm music-url-input"
                               value="{{ $song->audioUrl() }}" readonly>
                        <button class="btn btn-outline-secondary btn-sm btn-copy-music"
                                data-url="{{ $song->audioUrl() }}"
                                data-title="{{ $song->title }}"
                                data-artist="{{ $song->artist }}">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <small class="text-success d-block mt-1">
                        <i class="fa fa-check"></i>
                        @if($song->isFree())
                            Gratis — langsung gunakan
                        @elseif($song->uploaded_by === auth()->id())
                            Upload Saya
                        @elseif($hasPremiumAccess && $song->type === 'premium')
                            Premium gratis (Paket {{ $activePlan->name }})
                        @else
                            Sudah dibeli
                        @endif
                    </small>
                @else
                    {{-- Belum punya akses — tampilkan tombol beli atau upgrade --}}
                    <a href="{{ route('music.buy', $song) }}" class="btn btn-warning btn-sm w-100">
                        <i class="fa fa-shopping-cart"></i> Beli — {{ $song->formattedPrice() }}
                    </a>
                    <small class="text-muted d-block mt-1 text-center">
                        Atau <a href="{{ route('subscription.index') }}">upgrade paket</a> untuk akses gratis
                    </small>
                @endif
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-body text-center text-muted">Belum ada lagu tersedia.</div>
    </div>
    @endforelse
</div>

{{-- Modal: URL berhasil disalin --}}
<div class="modal fade" id="copyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div style="font-size:2.5rem">✅</div>
                <h5 class="mt-2">URL Disalin!</h5>
                <p class="text-muted small mb-0">
                    Tempel URL ini ke field <strong>URL Lagu (mp3)</strong> di form undangan Anda.
                </p>
                <div class="mt-3">
                    <small class="text-muted">Judul:</small>
                    <div class="fw-bold" id="copiedTitle"></div>
                </div>
            </div>
            <div class="modal-footer border-0 justify-content-center">
                <button class="btn btn-primary btn-sm" data-bs-dismiss="modal">OK</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.btn-copy-music').forEach(btn => {
    btn.addEventListener('click', function () {
        const url    = this.dataset.url;
        const title  = this.dataset.title;
        const artist = this.dataset.artist;

        navigator.clipboard.writeText(url).then(() => {
            document.getElementById('copiedTitle').textContent = title + (artist ? ' — ' + artist : '');
            new bootstrap.Modal(document.getElementById('copyModal')).show();
        });
    });
});
</script>
@endpush
