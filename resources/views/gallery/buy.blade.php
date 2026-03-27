@extends('layouts.app')
@section('title', 'Beli Slot Foto')
@section('page-title', 'Beli Slot Foto Tambahan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.gallery.index', $invitation) }}">Galeri</a></li>
    <li class="breadcrumb-item active">Beli Slot</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header text-center"><h4 class="card-title mb-0">Konfirmasi Pembelian</h4></div>
            <div class="card-body">

                <table class="table table-sm mb-4">
                    <tr><td class="text-muted">No. Order</td><td class="fw-bold">{{ $order->order_number }}</td></tr>
                    <tr><td class="text-muted">Undangan</td><td>{{ $invitation->title }}</td></tr>
                    <tr><td class="text-muted">Jumlah Slot</td><td><strong>{{ $order->qty }} foto</strong></td></tr>
                    <tr><td class="text-muted">Harga/foto</td><td>Rp {{ number_format($order->price_per_photo, 0, ',', '.') }}</td></tr>
                    <tr>
                        <td class="text-muted">Total</td>
                        <td class="fw-bold text-warning fs-5">Rp {{ number_format($order->amount, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <div class="alert alert-info mb-4">
                    <i class="fa fa-info-circle"></i>
                    <strong>Mode Simulasi</strong> — Klik tombol di bawah untuk mensimulasikan pembayaran.
                </div>

                <form action="{{ route('gallery.pay', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="fa fa-check-circle"></i>
                        Simulasi Bayar Rp {{ number_format($order->amount, 0, ',', '.') }}
                    </button>
                </form>
                <a href="{{ route('invitations.gallery.index', $invitation) }}" class="btn btn-link w-100 mt-2 text-muted">Batal</a>
            </div>
        </div>
    </div>
</div>
@endsection
