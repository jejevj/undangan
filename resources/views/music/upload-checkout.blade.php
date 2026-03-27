@extends('layouts.app')
@section('title', 'Pembayaran Upload Musik')
@section('page-title', 'Pembayaran Upload Musik')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('music.index') }}">Musik</a></li>
    <li class="breadcrumb-item"><a href="{{ route('music.upload') }}">Upload</a></li>
    <li class="breadcrumb-item active">Pembayaran</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">Konfirmasi Pembayaran</h4>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h5>Detail Upload</h5>
                    <table class="table table-borderless">
                        <tr>
                            <td width="120">Judul Lagu</td>
                            <td><strong>{{ $order->temp_title }}</strong></td>
                        </tr>
                        @if($order->temp_artist)
                        <tr>
                            <td>Artis</td>
                            <td>{{ $order->temp_artist }}</td>
                        </tr>
                        @endif
                        <tr>
                            <td>Nomor Order</td>
                            <td><code>{{ $order->order_number }}</code></td>
                        </tr>
                        <tr>
                            <td>Status</td>
                            <td>
                                <span class="badge badge-warning">{{ ucfirst($order->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="alert alert-light border mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-muted small">Total Pembayaran</div>
                            <h3 class="mb-0 text-primary">{{ $order->formattedAmount() }}</h3>
                        </div>
                        <div class="text-end">
                            <i class="fa fa-music" style="font-size: 2rem; opacity: 0.3"></i>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info">
                    <i class="fa fa-info-circle"></i>
                    <strong>Simulasi Pembayaran</strong><br>
                    Klik tombol di bawah untuk mensimulasikan pembayaran berhasil.
                    Pada implementasi real, ini akan terintegrasi dengan payment gateway.
                </div>

                <form action="{{ route('music.upload.pay', $order) }}" method="POST">
                    @csrf
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-lg flex-fill">
                            <i class="fa fa-check-circle"></i> Bayar Sekarang
                        </button>
                        <a href="{{ route('music.index') }}" class="btn btn-secondary btn-lg">
                            Batal
                        </a>
                    </div>
                </form>

                <div class="mt-3 text-center">
                    <small class="text-muted">
                        Dengan melakukan pembayaran, lagu Anda akan langsung tersedia di library musik.
                    </small>
                </div>
            </div>
        </div>

        {{-- Preview Audio --}}
        @if($order->temp_file_path)
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title mb-0">Preview Lagu</h5>
            </div>
            <div class="card-body">
                <audio controls class="w-100">
                    <source src="{{ asset('storage/' . $order->temp_file_path) }}" type="audio/mpeg">
                    Browser Anda tidak mendukung audio player.
                </audio>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
