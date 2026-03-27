@extends('layouts.app')
@section('title', 'Pilih Musik Undangan')
@section('page-title', 'Musik Undangan')

@section('breadcrumb')
    <li class="breadcrumb-item active">Musik</li>
@endsection

@section('content')
<p class="text-muted mb-4">
    Pilih lagu latar untuk undangan Anda. Lagu gratis langsung bisa digunakan.
    Lagu premium perlu dibeli terlebih dahulu.
</p>

<div class="d-flex gap-2 mb-4">
    <a href="{{ route('music.upload') }}" class="btn btn-outline-primary btn-sm">
        <i class="fa fa-upload"></i> Upload Lagu Saya
    </a>
</div>

<div class="row">
    @forelse($songs as $song)
    @php $owned = $song->isFree() || in_array($song->id, $myIds); @endphp
    <div class="col-xl-3 col-md-4 col-sm-6">
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
                <div class="d-flex align-items-center gap-2">
                    <span class="badge badge-{{ $song->isFree() ? 'success' : 'warning' }}">
                        {{ $song->isFree() ? 'Gratis' : 'Premium' }}
                    </span>
                    @if(!$song->isFree())
                        <span class="small fw-bold">{{ $song->formattedPrice() }}</span>
                    @endif
                    @if($song->duration)
                        <span class="text-muted small ms-auto">{{ $song->duration }}</span>
                    @endif
                </div>
            </div>

            <div class="card-footer">
                @if($owned)
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
                        {{ $song->isFree() ? 'Gratis — langsung gunakan' : 'Sudah dibeli' }}
                    </small>
                @else
                    <a href="{{ route('music.buy', $song) }}" class="btn btn-warning btn-sm w-100">
                        <i class="fa fa-shopping-cart"></i> Beli — {{ $song->formattedPrice() }}
                    </a>
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
