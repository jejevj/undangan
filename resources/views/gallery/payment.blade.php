@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Order Info --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fa fa-shopping-cart"></i> Detail Order</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Order Number</small>
                            <strong>{{ $order->order_number }}</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Status</small>
                            <span class="badge bg-warning">Menunggu Pembayaran</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Jumlah Slot</small>
                            <strong>{{ $order->qty }} slot foto</strong>
                        </div>
                        <div class="col-md-6 mb-3">
                            <small class="text-muted d-block">Total Pembayaran</small>
                            <strong class="text-primary fs-5">Rp {{ number_format($order->amount, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payment Instructions --}}
            @if($order->payment_method === 'qris' || strpos($order->payment_method, 'QRIS') !== false)
                {{-- QRIS Payment --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fa fa-qrcode"></i> Pembayaran QRIS</h5>
                    </div>
                    <div class="card-body text-center">
                        <p class="mb-4">Scan QR Code berikut dengan aplikasi pembayaran Anda:</p>
                        
                        @if($order->qr_string)
                            <div class="mb-4">
                                <div id="qrcode" class="d-inline-block p-3 bg-white border rounded"></div>
                            </div>
                        @elseif($order->qr_url)
                            <img src="{{ $order->qr_url }}" 
                                 alt="QRIS Code" 
                                 class="img-fluid mb-4"
                                 style="max-width: 300px;">
                        @endif

                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> 
                            <strong>Cara Pembayaran:</strong><br>
                            1. Buka aplikasi e-wallet atau mobile banking<br>
                            2. Pilih menu Scan QR atau QRIS<br>
                            3. Scan kode QR di atas<br>
                            4. Konfirmasi pembayaran<br>
                            5. Pembayaran akan diverifikasi otomatis
                        </div>

                        <div class="alert alert-warning">
                            <i class="fa fa-clock"></i> 
                            QR Code berlaku hingga: 
                            <strong>{{ $order->expired_at->format('d M Y H:i') }}</strong>
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="checkPaymentStatus()">
                            <i class="fa fa-sync"></i> Cek Status Pembayaran
                        </button>
                    </div>
                </div>

            @else
                {{-- Virtual Account Payment --}}
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fa fa-university"></i> 
                            Pembayaran {{ $order->paymentChannel->name ?? 'Virtual Account' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-4">
                            <h6 class="alert-heading"><i class="fa fa-info-circle"></i> Nomor Virtual Account</h6>
                            <div class="d-flex align-items-center justify-content-between">
                                <h3 class="mb-0 font-monospace">{{ $order->va_number }}</h3>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="copyVA()">
                                    <i class="fa fa-copy"></i> Salin
                                </button>
                            </div>
                        </div>

                        <h6 class="mb-3">Cara Pembayaran:</h6>
                        <ol class="mb-4">
                            <li>Buka aplikasi mobile banking atau ATM</li>
                            <li>Pilih menu Transfer / Bayar</li>
                            <li>Pilih {{ $order->paymentChannel->name ?? 'Virtual Account' }}</li>
                            <li>Masukkan nomor VA: <strong>{{ $order->va_number }}</strong></li>
                            <li>Masukkan nominal: <strong>Rp {{ number_format($order->amount, 0, ',', '.') }}</strong></li>
                            <li>Konfirmasi dan selesaikan pembayaran</li>
                        </ol>

                        <div class="alert alert-warning">
                            <i class="fa fa-clock"></i> 
                            VA berlaku hingga: <strong>{{ $order->expired_at->format('d M Y H:i') }}</strong>
                        </div>

                        <button type="button" class="btn btn-primary w-100" onclick="checkPaymentStatus()">
                            <i class="fa fa-sync"></i> Cek Status Pembayaran
                        </button>
                    </div>
                </div>
            @endif

            {{-- Back Button --}}
            <div class="text-center mt-4">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Kembali ke Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function copyVA() {
    const va = '{{ $order->va_number }}';
    navigator.clipboard.writeText(va).then(() => {
        alert('Nomor VA berhasil disalin!');
    });
}

function checkPaymentStatus() {
    const btn = event.target.closest('button');
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Mengecek...';

    fetch('{{ route("gallery.check-status", $order) }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'paid') {
                alert(data.message);
                window.location.href = data.redirect;
            } else if (data.status === 'pending') {
                alert(data.message);
                btn.disabled = false;
                btn.innerHTML = originalText;
            } else {
                alert('Gagal mengecek status pembayaran');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan saat mengecek status');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
}

// Auto check every 10 seconds
setInterval(() => {
    fetch('{{ route("gallery.check-status", $order) }}')
        .then(response => response.json())
        .then(data => {
            if (data.status === 'paid') {
                window.location.href = data.redirect;
            }
        })
        .catch(error => console.error('Auto check error:', error));
}, 10000);
</script>

@if($order->qr_string)
<script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
<script>
// Generate QR Code using qrcodejs
document.addEventListener('DOMContentLoaded', function() {
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $order->qr_string }}",
        width: 256,
        height: 256,
        colorDark : "#000000",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
});
</script>
@endif
@endsection
