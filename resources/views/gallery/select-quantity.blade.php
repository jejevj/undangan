@extends('layouts.app')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fa fa-images"></i> Beli Slot Foto Tambahan
                    </h5>
                </div>
                <div class="card-body">
                    {{-- Info Gallery Pool --}}
                    <div class="alert alert-info">
                        <h6 class="alert-heading">
                            <i class="fa fa-info-circle"></i> Gallery Pool Anda
                        </h6>
                        <p class="mb-0">Slot foto yang dibeli akan masuk ke gallery pool Anda dan bisa digunakan di semua undangan.</p>
                    </div>

                    {{-- Slot Info --}}
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-primary mb-0">{{ $currentSlots }}</h3>
                                    <small class="text-muted">Total Slot</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-success mb-0">{{ $usedSlots }}</h3>
                                    <small class="text-muted">Terpakai</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h3 class="text-warning mb-0">{{ $currentSlots - $usedSlots }}</h3>
                                    <small class="text-muted">Tersisa</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Form Pilih Jumlah --}}
                    <form action="{{ route('gallery.checkout') }}" method="GET">
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold">Jumlah Slot Foto Tambahan</label>
                            <div class="input-group input-group-lg">
                                <button type="button" class="btn btn-outline-secondary" onclick="decreaseQty()">
                                    <i class="fa fa-minus"></i>
                                </button>
                                <input type="number" 
                                       name="qty" 
                                       id="qty" 
                                       class="form-control text-center fw-bold" 
                                       value="5" 
                                       min="1" 
                                       max="50" 
                                       required>
                                <button type="button" class="btn btn-outline-secondary" onclick="increaseQty()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                            <small class="text-muted">Minimal 1 slot, maksimal 50 slot</small>
                        </div>

                        {{-- Quick Select --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold">Pilih Cepat:</label>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(5)">5 Slot</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(10)">10 Slot</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(15)">15 Slot</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(20)">20 Slot</button>
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setQty(30)">30 Slot</button>
                            </div>
                        </div>

                        {{-- Price Calculation --}}
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Harga per slot:</span>
                                    <strong>Rp {{ number_format($pricePerPhoto, 0, ',', '.') }}</strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Jumlah slot:</span>
                                    <strong id="displayQty">5</strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">Subtotal:</span>
                                    <strong class="text-primary fs-5" id="displayTotal">Rp {{ number_format(5 * $pricePerPhoto, 0, ',', '.') }}</strong>
                                </div>
                                <small class="text-muted d-block mt-2">
                                    <i class="fa fa-info-circle"></i> Biaya admin akan ditambahkan sesuai metode pembayaran
                                </small>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Kembali
                            </a>
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                <i class="fa fa-shopping-cart"></i> Lanjut ke Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const pricePerPhoto = {{ $pricePerPhoto }};

function updateDisplay() {
    const qty = parseInt(document.getElementById('qty').value) || 1;
    document.getElementById('displayQty').textContent = qty;
    document.getElementById('displayTotal').textContent = 'Rp ' + (qty * pricePerPhoto).toLocaleString('id-ID');
}

function setQty(value) {
    document.getElementById('qty').value = value;
    updateDisplay();
}

function increaseQty() {
    const input = document.getElementById('qty');
    const current = parseInt(input.value) || 1;
    if (current < 50) {
        input.value = current + 1;
        updateDisplay();
    }
}

function decreaseQty() {
    const input = document.getElementById('qty');
    const current = parseInt(input.value) || 1;
    if (current > 1) {
        input.value = current - 1;
        updateDisplay();
    }
}

document.getElementById('qty').addEventListener('input', updateDisplay);
</script>
@endsection
