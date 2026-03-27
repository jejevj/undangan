@extends('layouts.app')
@section('title', 'Galeri Foto')
@section('page-title', 'Galeri Foto Undangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invitations.edit', $invitation) }}">{{ Str::limit($invitation->title, 30) }}</a></li>
    <li class="breadcrumb-item active">Galeri</li>
@endsection

@section('content')

{{-- Info Quota --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div>
                @if($total === null)
                    <span class="badge badge-success fs-6">Unlimited Foto</span>
                    <span class="text-muted ms-2">Template Premium — tidak ada batas foto</span>
                @else
                    <span class="fw-bold fs-5">{{ $used }} / {{ $total }}</span>
                    <span class="text-muted ms-1">foto terpakai</span>
                    <div class="progress mt-2" style="height:6px;width:200px">
                        <div class="progress-bar bg-{{ $remaining > 0 ? 'success' : 'danger' }}"
                             style="width:{{ $total > 0 ? min(100, ($used/$total)*100) : 0 }}%"></div>
                    </div>
                    <small class="text-muted d-block mt-1">
                        @if($remaining > 0)
                            Sisa <strong>{{ $remaining }}</strong> slot foto
                        @else
                            <span class="text-danger">Slot foto habis</span>
                        @endif
                        &nbsp;·&nbsp; Gratis: {{ $limit }} foto
                        &nbsp;·&nbsp; Dibeli: {{ $total - $limit }} slot
                    </small>
                @endif
            </div>

            @if($total !== null)
            <div class="d-flex gap-2 align-items-center">
                <span class="text-muted small">
                    Tambah slot: <strong>{{ $invitation->template->formattedExtraPhotoPrice() }}/foto</strong>
                </span>
                <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#buyModal">
                    <i class="fa fa-plus"></i> Beli Slot Foto
                </button>
            </div>
            @endif
        </div>
    </div>
</div>

<div class="row">
    {{-- Upload Form --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Upload Foto</h4></div>
            <div class="card-body">
                @if($remaining !== null && $remaining <= 0)
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle"></i>
                        Slot foto habis. Beli slot tambahan untuk upload lebih banyak foto.
                    </div>
                    <button class="btn btn-warning w-100" data-bs-toggle="modal" data-bs-target="#buyModal">
                        <i class="fa fa-shopping-cart"></i> Beli Slot Foto
                    </button>
                @else
                    <form action="{{ route('invitations.gallery.store', $invitation) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label>Pilih Foto</label>
                            <input type="file" name="photos[]" class="form-control" accept="image/*"
                                multiple {{ $remaining !== null ? "max=\"{$remaining}\"" : '' }}>
                            <small class="text-muted">
                                Format: JPG/PNG/WebP, maks 5MB per foto.
                                @if($remaining !== null)
                                    Bisa upload maks {{ $remaining }} foto sekaligus.
                                @endif
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa fa-upload"></i> Upload
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Daftar Foto --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Foto Galeri ({{ $used }})</h4>
            </div>
            <div class="card-body">
                @if($invitation->gallery->isEmpty())
                    <p class="text-muted text-center py-4">Belum ada foto. Upload foto di form sebelah kiri.</p>
                @else
                    <div class="row g-2">
                        @foreach($invitation->gallery as $photo)
                        <div class="col-4 col-md-3">
                            <div class="position-relative">
                                <img src="{{ $photo->url() }}" class="img-fluid rounded"
                                     style="height:100px;width:100%;object-fit:cover" alt="">
                                @if($photo->caption)
                                    <div class="small text-muted text-truncate mt-1">{{ $photo->caption }}</div>
                                @endif
                                <form action="{{ route('invitations.gallery.destroy', [$invitation, $photo]) }}"
                                    method="POST"
                                    data-confirm="Hapus foto ini?"
                                    data-confirm-title="Hapus Foto"
                                    data-confirm-ok="Hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-danger btn-xs position-absolute top-0 end-0 m-1"
                                        style="padding:2px 6px;font-size:.7rem">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- Riwayat pembelian slot --}}
        @if($invitation->galleryOrders->count())
        <div class="card mt-3">
            <div class="card-header"><h4 class="card-title">Riwayat Pembelian Slot</h4></div>
            <div class="card-body p-0">
                <table class="table table-sm mb-0">
                    <thead><tr><th>No. Order</th><th>Qty</th><th>Total</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach($invitation->galleryOrders as $ord)
                        <tr>
                            <td><small>{{ $ord->order_number }}</small></td>
                            <td>{{ $ord->qty }} foto</td>
                            <td>Rp {{ number_format($ord->amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge badge-{{ $ord->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($ord->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Modal Beli Slot --}}
@if($total !== null)
<div class="modal fade" id="buyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Beli Slot Foto Tambahan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('invitations.gallery.buy-slots.post', $invitation) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2">
                        <strong>{{ $invitation->template->formattedExtraPhotoPrice() }}</strong> per foto
                    </div>
                    <div class="form-group">
                        <label>Jumlah Slot Foto</label>
                        <input type="number" name="qty" class="form-control" value="1" min="1" max="20" id="qtyInput">
                    </div>
                    <div class="mt-2 text-muted small">
                        Total: <strong id="totalPrice">Rp {{ number_format($invitation->template->extra_photo_price, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning btn-sm">Lanjut ke Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
const qtyInput   = document.getElementById('qtyInput');
const totalPrice = document.getElementById('totalPrice');
const pricePerPhoto = {{ $invitation->template->extra_photo_price ?? 5000 }};

qtyInput?.addEventListener('input', function () {
    const total = parseInt(this.value || 1) * pricePerPhoto;
    totalPrice.textContent = 'Rp ' + total.toLocaleString('id-ID');
});
</script>
@endpush
