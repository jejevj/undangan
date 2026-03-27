@extends('layouts.app')
@section('title', 'Aktifkan Gift Section')
@section('page-title', 'Aktifkan Gift Section')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.gift.index', $invitation) }}">Gift Section</a></li>
    <li class="breadcrumb-item active">Aktivasi</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header text-center"><h4 class="card-title mb-0">Konfirmasi Aktivasi</h4></div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <div style="font-size:3rem">🎁</div>
                    <h5 class="mt-2">Gift Section</h5>
                    <p class="text-muted small">Tambahkan rekening bank agar tamu bisa memberikan hadiah digital kepada mempelai.</p>
                </div>

                <table class="table table-sm mb-4">
                    <tr><td class="text-muted">No. Order</td><td class="fw-bold">{{ $order->order_number }}</td></tr>
                    <tr><td class="text-muted">Fitur</td><td>Gift Section (Rekening Bank)</td></tr>
                    <tr><td class="text-muted">Template</td><td>{{ $invitation->template->name }}</td></tr>
                    <tr>
                        <td class="text-muted">Biaya Aktivasi</td>
                        <td class="fw-bold text-warning fs-5">
                            Rp {{ number_format($order->amount, 0, ',', '.') }}
                        </td>
                    </tr>
                </table>

                <div class="alert alert-info mb-4">
                    <i class="fa fa-info-circle"></i>
                    <strong>Mode Simulasi</strong> — Klik tombol di bawah untuk mensimulasikan pembayaran.
                </div>

                <form action="{{ route('gift.feature.pay', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="fa fa-check-circle"></i>
                        Simulasi Bayar Rp {{ number_format($order->amount, 0, ',', '.') }}
                    </button>
                </form>
                <a href="{{ route('invitations.edit', $invitation) }}" class="btn btn-link w-100 mt-2 text-muted">Batal</a>
            </div>
        </div>
    </div>
</div>
@endsection
