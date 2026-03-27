@extends('layouts.app')
@section('title', 'Gift Section')
@section('page-title', 'Gift Section — Rekening')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invitations.edit', $invitation) }}">{{ Str::limit($invitation->title, 30) }}</a></li>
    <li class="breadcrumb-item active">Gift Section</li>
@endsection

@section('content')

@if($needsPayment)
{{-- ── Locked: perlu bayar ─────────────────────────────────────── --}}
<div class="row justify-content-center">
    <div class="col-lg-5">
        <div class="card text-center">
            <div class="card-body py-5">
                <div style="font-size:3rem">🎁</div>
                <h4 class="mt-3">Gift Section</h4>
                <p class="text-muted">Tambahkan informasi rekening bank agar tamu bisa memberikan hadiah digital.</p>
                <div class="alert alert-warning mt-3">
                    Fitur ini memerlukan biaya aktivasi
                    <strong>Rp {{ number_format($invitation->template->gift_feature_price, 0, ',', '.') }}</strong>
                    untuk template <strong>{{ ucfirst($invitation->template->type) }}</strong>.
                </div>
                <a href="{{ route('invitations.gift.buy', $invitation) }}" class="btn btn-warning btn-lg mt-2">
                    <i class="fa fa-unlock"></i> Aktifkan Gift Section
                </a>
            </div>
        </div>
    </div>
</div>

@else
{{-- ── Aktif: kelola rekening ──────────────────────────────────── --}}
<div class="row">

    {{-- Form Tambah Rekening --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Tambah Rekening</h4></div>
            <div class="card-body">
                <form action="{{ route('invitations.gift.store', $invitation) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Nama Bank <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror"
                            value="{{ old('bank_name') }}" placeholder="BCA, Mandiri, BNI, GoPay..." required>
                        @error('bank_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Nomor Rekening <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror"
                            value="{{ old('account_number') }}" placeholder="1234567890" required>
                        @error('account_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Nama Pemilik <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control @error('account_name') is-invalid @enderror"
                            value="{{ old('account_name') }}" placeholder="Nama sesuai rekening" required>
                        @error('account_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-plus"></i> Tambah Rekening
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Daftar Rekening (Card Visual) --}}
    <div class="col-lg-8">
        <h5 class="mb-3">Preview Kartu Rekening</h5>

        @if($invitation->bankAccounts->isEmpty())
            <div class="card card-body text-center text-muted py-5">
                Belum ada rekening. Tambahkan rekening di form sebelah kiri.
            </div>
        @else
            <div class="row g-3">
                @foreach($invitation->bankAccounts as $account)
                <div class="col-md-6">
                    {{-- Bank Card Visual --}}
                    <div class="bank-card" style="background: {{ $account->bankColor() }}">
                        <div class="bank-card-header">
                            <div class="bank-initial">{{ $account->bankInitial() }}</div>
                            <div class="bank-name-label">{{ strtoupper($account->bank_name) }}</div>
                        </div>
                        <div class="bank-card-number">
                            {{ chunk_split($account->account_number, 4, ' ') }}
                        </div>
                        <div class="bank-card-footer">
                            <div>
                                <div class="bank-card-label">Atas Nama</div>
                                <div class="bank-card-value">{{ $account->account_name }}</div>
                            </div>
                            <div class="bank-card-actions">
                                <button class="btn-card-action btn-edit-account"
                                    data-id="{{ $account->id }}"
                                    data-bank="{{ $account->bank_name }}"
                                    data-number="{{ $account->account_number }}"
                                    data-name="{{ $account->account_name }}"
                                    data-url="{{ route('invitations.gift.update', [$invitation, $account]) }}"
                                    title="Edit">✏️</button>
                                <form action="{{ route('invitations.gift.destroy', [$invitation, $account]) }}"
                                    method="POST" class="d-inline"
                                    data-confirm="Hapus rekening {{ $account->bank_name }} ini?"
                                    data-confirm-title="Hapus Rekening" data-confirm-ok="Hapus">
                                    @csrf @method('DELETE')
                                    <button class="btn-card-action" title="Hapus">🗑️</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Modal Edit Rekening --}}
<div class="modal fade" id="editAccountModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Rekening</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAccountForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Nama Bank</label>
                        <input type="text" name="bank_name" id="editBankName" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nomor Rekening</label>
                        <input type="text" name="account_number" id="editAccountNumber" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Nama Pemilik</label>
                        <input type="text" name="account_name" id="editAccountName" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

@endsection

@push('styles')
<style>
.bank-card {
    border-radius: 16px;
    padding: 20px 22px;
    color: #fff;
    min-height: 160px;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    box-shadow: 0 8px 24px rgba(0,0,0,.2);
    position: relative;
    overflow: hidden;
}

.bank-card::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 120px; height: 120px;
    border-radius: 50%;
    background: rgba(255,255,255,.08);
}

.bank-card::after {
    content: '';
    position: absolute;
    bottom: -40px; right: 20px;
    width: 160px; height: 160px;
    border-radius: 50%;
    background: rgba(255,255,255,.05);
}

.bank-card-header { display: flex; align-items: center; gap: 10px; }
.bank-initial { width: 36px; height: 36px; border-radius: 8px; background: rgba(255,255,255,.2); display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: .85rem; }
.bank-name-label { font-weight: 700; font-size: .9rem; letter-spacing: 1px; opacity: .9; }

.bank-card-number { font-size: 1.2rem; letter-spacing: 3px; font-family: monospace; margin: 14px 0 0; opacity: .95; }

.bank-card-footer { display: flex; justify-content: space-between; align-items: flex-end; margin-top: 12px; }
.bank-card-label  { font-size: .65rem; opacity: .7; text-transform: uppercase; letter-spacing: 1px; }
.bank-card-value  { font-size: .9rem; font-weight: 600; }

.bank-card-actions { display: flex; gap: 6px; }
.btn-card-action {
    background: rgba(255,255,255,.2);
    border: none; border-radius: 8px;
    width: 32px; height: 32px;
    cursor: pointer; font-size: .85rem;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
}
.btn-card-action:hover { background: rgba(255,255,255,.35); }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.btn-edit-account').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('editBankName').value     = this.dataset.bank;
        document.getElementById('editAccountNumber').value = this.dataset.number;
        document.getElementById('editAccountName').value  = this.dataset.name;
        document.getElementById('editAccountForm').action = this.dataset.url;
        new bootstrap.Modal(document.getElementById('editAccountModal')).show();
    });
});
</script>
@endpush
