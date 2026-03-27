@extends('layouts.app')
@section('title', 'Checkout Paket ' . $plan->name)
@section('page-title', 'Checkout Paket')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('subscription.index') }}">Paket Langganan</a></li>
    <li class="breadcrumb-item active">Checkout</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header text-center">
                <h4 class="card-title mb-0">Konfirmasi Pembelian Paket</h4>
            </div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <span class="badge badge-{{ $plan->badge_color }}" style="font-size:1.1rem;padding:8px 20px">
                        {{ $plan->name }}
                    </span>
                    @if($plan->is_popular)
                        <div class="mt-1"><small class="text-primary">⭐ Paling Populer</small></div>
                    @endif
                </div>

                <table class="table table-sm mb-4">
                    <tr><td class="text-muted">No. Order</td><td class="fw-bold">{{ $order->order_number }}</td></tr>
                    <tr><td class="text-muted">Paket</td><td>{{ $plan->name }}</td></tr>
                    <tr><td class="text-muted">Undangan</td><td>{{ $plan->max_invitations }} undangan</td></tr>
                    <tr><td class="text-muted">Foto Galeri</td><td>{{ $plan->max_gallery_photos ?? 'Unlimited' }}</td></tr>
                    <tr><td class="text-muted">Upload Lagu</td><td>{{ $plan->max_music_uploads === null ? 'Unlimited' : $plan->max_music_uploads . ' lagu' }}</td></tr>
                    <tr><td class="text-muted">Gift Section</td><td>{{ $plan->gift_section_included ? 'Gratis' : 'Berbayar' }}</td></tr>
                    <tr>
                        <td class="text-muted">Total</td>
                        <td class="fw-bold text-warning fs-5">{{ $plan->formattedPrice() }}</td>
                    </tr>
                </table>

                <div class="alert alert-info mb-4">
                    <i class="fa fa-info-circle"></i>
                    <strong>Mode Simulasi</strong> — Klik tombol di bawah untuk mensimulasikan pembayaran.
                    Integrasi payment gateway akan ditambahkan kemudian.
                </div>

                <form action="{{ route('subscription.pay', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success w-100 btn-lg">
                        <i class="fa fa-check-circle"></i>
                        Simulasi Bayar {{ $plan->formattedPrice() }}
                    </button>
                </form>
                <a href="{{ route('subscription.index') }}" class="btn btn-link w-100 mt-2 text-muted">Batal</a>
            </div>
        </div>
    </div>
</div>
@endsection
