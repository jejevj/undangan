@extends('layouts.app')
@section('title', 'Beli Lagu')
@section('page-title', 'Beli Lagu Premium')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('music.index') }}">Musik</a></li>
    <li class="breadcrumb-item active">Beli</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="card-title mb-0">Konfirmasi Pembelian</h4>
            </div>
            <div class="card-body">

                {{-- Info lagu --}}
                <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-4">
                    <div class="rounded d-flex align-items-center justify-content-center"
                         style="width:56px;height:56px;background:#c9a96e;font-size:1.8rem;flex-shrink:0">🎵</div>
                    <div>
                        <div class="fw-bold fs-5">{{ $music->title }}</div>
                        <div class="text-muted">{{ $music->artist ?? '—' }}</div>
                        @if($music->duration)
                            <small class="text-muted">Durasi: {{ $music->duration }}</small>
                        @endif
                    </div>
                </div>

                {{-- Preview --}}
                <div class="mb-4">
                    <label class="small text-muted">Preview:</label>
                    <audio controls class="w-100 mt-1">
                        <source src="{{ $music->audioUrl() }}" type="audio/mpeg">
                    </audio>
                </div>

                {{-- Rincian order --}}
                <table class="table table-sm mb-4">
                    <tr>
                        <td class="text-muted">No. Order</td>
                        <td class="fw-bold">{{ $order->order_number }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Lagu</td>
                        <td>{{ $music->title }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Harga</td>
                        <td class="fw-bold text-warning fs-5">{{ $music->formattedPrice() }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Status</td>
                        <td>
                            <span class="badge badge-{{ $order->status === 'paid' ? 'success' : 'warning' }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                    </tr>
                </table>

                {{-- Simulasi bayar --}}
                <div class="alert alert-info mb-4">
                    <i class="fa fa-info-circle"></i>
                    <strong>Mode Simulasi</strong> — Klik tombol di bawah untuk mensimulasikan pembayaran.
                    Integrasi payment gateway akan ditambahkan kemudian.
                </div>

                <form action="{{ route('music.pay', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="fa fa-check-circle"></i>
                        Simulasi Bayar {{ $music->formattedPrice() }}
                    </button>
                </form>

                <a href="{{ route('music.index') }}" class="btn btn-link w-100 mt-2 text-muted">
                    Batal
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
