@extends('layouts.app')
@section('title', 'Manajemen Tamu')
@section('page-title', 'Manajemen Tamu')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invitations.edit', $invitation) }}">{{ Str::limit($invitation->title, 30) }}</a></li>
    <li class="breadcrumb-item active">Daftar Tamu</li>
@endsection

@section('content')

{{-- Pesan Pengantar --}}
<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h4 class="card-title mb-0">Pesan Pengantar WhatsApp</h4>
        <small class="text-muted">Gunakan <code>{nama_tamu}</code> dan <code>{link}</code> sebagai placeholder</small>
    </div>
    <div class="card-body">
        <form action="{{ route('invitations.update', $invitation) }}" method="POST">
            @csrf @method('PUT')
            <input type="hidden" name="title" value="{{ $invitation->title }}">
            <div class="form-group">
                <textarea name="greeting" class="form-control" rows="5"
                    placeholder="Kepada Yth. {nama_tamu},&#10;&#10;Kami mengundang Anda untuk hadir di hari bahagia kami.&#10;Silakan buka undangan: {link}">{{ old('greeting', $invitation->greeting) }}</textarea>
            </div>
            <div class="mt-2 d-flex gap-2">
                <button type="submit" class="btn btn-success btn-sm">
                    <i class="fa fa-save"></i> Simpan Pesan
                </button>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="btnResetGreeting">
                    <i class="fa fa-refresh"></i> Reset ke Default
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row">
    {{-- Form Tambah Tamu --}}
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Tambah Tamu</h4></div>
            <div class="card-body">
                @php
                    $guestLimit   = $invitation->template->guest_limit;
                    $guestCount   = $guests->count();
                    $guestFull    = $guestLimit !== null && $guestCount >= $guestLimit;
                @endphp

                @if($guestLimit !== null)
                <div class="mb-3">
                    <div class="d-flex justify-content-between small text-muted mb-1">
                        <span>Tamu: <strong>{{ $guestCount }}</strong> / {{ $guestLimit }}</span>
                        @if($guestFull)
                            <span class="text-danger fw-bold">Penuh</span>
                        @else
                            <span class="text-success">Sisa {{ $guestLimit - $guestCount }}</span>
                        @endif
                    </div>
                    <div class="progress" style="height:5px">
                        <div class="progress-bar bg-{{ $guestFull ? 'danger' : 'success' }}"
                             style="width:{{ min(100, ($guestCount/$guestLimit)*100) }}%"></div>
                    </div>
                    <small class="text-muted">Batas template {{ $invitation->template->name }}: {{ $guestLimit }} tamu</small>
                </div>
                @endif

                @if($guestFull)
                    <div class="alert alert-danger py-2 small">
                        <i class="fa fa-exclamation-triangle"></i>
                        Batas maksimal {{ $guestLimit }} tamu sudah tercapai.
                    </div>
                @else
                <form action="{{ route('invitations.guests.store', $invitation) }}" method="POST">
                    @csrf
                    <div class="form-group mb-3">
                        <label>Nama Tamu <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name') }}" placeholder="J & Pasangan" required>
                        <small class="text-muted">Contoh: Keluarga Budi, J & Pasangan</small>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="form-group mb-3">
                        <label>Nomor WhatsApp</label>
                        <div class="input-group">
                            <select name="phone_code" class="form-select" style="max-width:110px">
                                @foreach(config('phone_codes', [
                                    '+62' => '🇮🇩 +62',
                                    '+60' => '🇲🇾 +60',
                                    '+65' => '🇸🇬 +65',
                                    '+63' => '🇵🇭 +63',
                                    '+66' => '🇹🇭 +66',
                                    '+84' => '🇻🇳 +84',
                                    '+1'  => '🇺🇸 +1',
                                    '+44' => '🇬🇧 +44',
                                    '+61' => '🇦🇺 +61',
                                    '+966'=> '🇸🇦 +966',
                                    '+971'=> '🇦🇪 +971',
                                ]) as $code => $label)
                                    <option value="{{ $code }}" {{ old('phone_code', '+62') === $code ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="phone" class="form-control"
                                value="{{ old('phone') }}" placeholder="08123456789">
                        </div>
                        <small class="text-muted">Untuk kirim undangan via WhatsApp</small>
                    </div>
                    <div class="form-group mb-3">
                        <label>Catatan</label>
                        <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="Opsional">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fa fa-plus"></i> Tambah Tamu
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Daftar Tamu --}}
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">
                    Daftar Tamu
                    <span class="badge badge-primary ms-1">{{ $guests->count() }}</span>
                </h4>
                @if($guests->count())
                <button class="btn btn-outline-success btn-sm" id="btnSendAll">
                    <i class="fa fa-whatsapp"></i> Kirim Semua via WA
                </button>
                @endif
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="guestTable">
                        <thead class="table-light">
                            <tr>
                                <th width="30">#</th>
                                <th>Nama Tamu</th>
                                <th>WhatsApp</th>
                                <th>Link & Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guests as $i => $guest)
                            @php
                                $guestLink = route('invitation.show', $invitation->slug) . '?to=' . urlencode($guest->slug);
                                $waNumber  = $guest->getWhatsappNumber();
                            @endphp
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-bold">{{ $guest->name }}</div>
                                    @if($guest->notes)
                                        <small class="text-muted">{{ $guest->notes }}</small>
                                    @endif
                                    @if($guest->is_attending === true)
                                        <span class="badge badge-success d-block mt-1" style="width:fit-content">Hadir ✓</span>
                                    @elseif($guest->is_attending === false)
                                        <span class="badge badge-danger d-block mt-1" style="width:fit-content">Tidak Hadir</span>
                                    @endif
                                </td>
                                <td>
                                    @if($waNumber)
                                        <span class="text-success small">
                                            <i class="fa fa-whatsapp"></i>
                                            {{ $guest->phone_code }} {{ $guest->phone }}
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    {{-- Link undangan --}}
                                    <div class="input-group input-group-sm mb-1">
                                        <input type="text" class="form-control form-control-sm"
                                            value="{{ $guestLink }}" readonly>
                                        <button class="btn btn-outline-secondary btn-sm btn-copy"
                                            data-link="{{ $guestLink }}" title="Salin link">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                    </div>
                                    {{-- Action buttons --}}
                                    <div class="d-flex gap-1 flex-wrap">
                                        {{-- Preview & Share Pesan --}}
                                        <button class="btn btn-info btn-xs btn-share"
                                            data-url="{{ route('invitations.guests.greeting', [$invitation, $guest]) }}"
                                            data-name="{{ $guest->name }}"
                                            title="Preview & Share">
                                            <i class="fa fa-share-alt"></i> Share
                                        </button>
                                        {{-- Edit --}}
                                        <button class="btn btn-warning btn-xs btn-edit-guest"
                                            data-id="{{ $guest->id }}"
                                            data-name="{{ $guest->name }}"
                                            data-phone-code="{{ $guest->phone_code ?? '+62' }}"
                                            data-phone="{{ $guest->phone }}"
                                            data-notes="{{ $guest->notes }}"
                                            data-url="{{ route('invitations.guests.update', [$invitation, $guest]) }}"
                                            title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        {{-- Hapus --}}
                                        <form action="{{ route('invitations.guests.destroy', [$invitation, $guest]) }}"
                                            method="POST" class="d-inline"
                                            data-confirm="Hapus tamu '{{ $guest->name }}'?" data-confirm-ok="Hapus" data-confirm-title="Hapus Tamu">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-xs" title="Hapus">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">
                                    Belum ada tamu. Tambahkan tamu di form sebelah kiri.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ===== Modal Edit Tamu ===== --}}
<div class="modal fade" id="editGuestModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Tamu</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editGuestForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Nama Tamu <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="editGuestName" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Nomor WhatsApp</label>
                        <div class="input-group">
                            <select name="phone_code" id="editPhoneCode" class="form-select" style="max-width:110px">
                                @foreach(['+62'=>'🇮🇩 +62','+60'=>'🇲🇾 +60','+65'=>'🇸🇬 +65','+63'=>'🇵🇭 +63','+66'=>'🇹🇭 +66','+84'=>'🇻🇳 +84','+1'=>'🇺🇸 +1','+44'=>'🇬🇧 +44','+61'=>'🇦🇺 +61','+966'=>'🇸🇦 +966','+971'=>'🇦🇪 +971'] as $code => $label)
                                    <option value="{{ $code }}">{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="phone" id="editPhone" class="form-control" placeholder="08123456789">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Catatan</label>
                        <input type="text" name="notes" id="editGuestNotes" class="form-control">
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

{{-- ===== Modal Share / Preview Pesan ===== --}}
<div class="modal fade" id="shareModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fa fa-share-alt text-primary"></i>
                    Share Undangan — <span id="shareGuestName" class="text-primary"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Pesan yang bisa diedit --}}
                <div class="form-group mb-3">
                    <label class="fw-bold">Pesan WhatsApp</label>
                    <small class="text-muted ms-2">— dapat diedit sebelum dikirim</small>
                    <textarea id="shareMessage" class="form-control mt-1" rows="7"></textarea>
                </div>

                {{-- Link undangan --}}
                <div class="form-group mb-4">
                    <label class="fw-bold">Link Undangan</label>
                    <div class="input-group mt-1">
                        <input type="text" id="shareLink" class="form-control" readonly>
                        <button class="btn btn-outline-secondary" id="btnCopyShareLink">
                            <i class="fa fa-copy"></i> Salin
                        </button>
                    </div>
                </div>

                {{-- Tombol Share --}}
                <div class="d-flex gap-2 flex-wrap">
                    {{-- WhatsApp --}}
                    <a href="#" id="btnWhatsapp" target="_blank" rel="noopener"
                        class="btn btn-success flex-fill" id="btnWa">
                        <i class="fa fa-whatsapp"></i> Kirim via WhatsApp
                    </a>
                    {{-- Salin Pesan --}}
                    <button class="btn btn-outline-primary" id="btnCopyMessage">
                        <i class="fa fa-copy"></i> Salin Pesan
                    </button>
                    {{-- Web Share API (mobile) --}}
                    <button class="btn btn-outline-secondary d-none" id="btnNativeShare">
                        <i class="fa fa-share"></i> Bagikan
                    </button>
                </div>

                <div id="noPhoneWarning" class="alert alert-warning mt-3 d-none">
                    <i class="fa fa-exclamation-triangle"></i>
                    Tamu ini belum memiliki nomor WhatsApp. Tombol WA akan membuka chat baru tanpa nomor tujuan.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const DEFAULT_GREETING = `Kepada Yth.\n{nama_tamu}\n\nDengan penuh kebahagiaan, kami mengundang Anda untuk hadir dan memberikan doa restu di hari pernikahan kami.\n\nSilakan buka undangan digital kami melalui tautan berikut:\n{link}\n\nMerupakan suatu kehormatan bagi kami apabila Bapak/Ibu/Saudara/i berkenan hadir.\n\nHormat kami,\nMempelai & Keluarga`;

// ── Reset greeting ke default ──────────────────────────────────────────────
document.getElementById('btnResetGreeting')?.addEventListener('click', () => {
    modalConfirm({
        message  : 'Pesan pengantar akan dikembalikan ke teks default. Lanjutkan?',
        title    : 'Reset Pesan',
        okText   : 'Ya, Reset',
        type     : 'warning',
        icon     : '🔄',
        onConfirm: () => {
            document.querySelector('textarea[name="greeting"]').value = DEFAULT_GREETING;
        },
    });
});

// ── Edit guest modal ───────────────────────────────────────────────────────
document.querySelectorAll('.btn-edit-guest').forEach(btn => {
    btn.addEventListener('click', function () {
        document.getElementById('editGuestName').value  = this.dataset.name;
        document.getElementById('editPhone').value      = this.dataset.phone || '';
        document.getElementById('editGuestNotes').value = this.dataset.notes || '';
        document.getElementById('editGuestForm').action = this.dataset.url;

        const sel = document.getElementById('editPhoneCode');
        [...sel.options].forEach(o => o.selected = (o.value === this.dataset.phoneCode));

        new bootstrap.Modal(document.getElementById('editGuestModal')).show();
    });
});

// ── Copy link ──────────────────────────────────────────────────────────────
document.querySelectorAll('.btn-copy').forEach(btn => {
    btn.addEventListener('click', function () {
        navigator.clipboard.writeText(this.dataset.link).then(() => {
            this.innerHTML = '<i class="fa fa-check"></i>';
            setTimeout(() => this.innerHTML = '<i class="fa fa-copy"></i>', 2000);
        });
    });
});

// ── Share modal ────────────────────────────────────────────────────────────
let currentWaUrl = null;

document.querySelectorAll('.btn-share').forEach(btn => {
    btn.addEventListener('click', function () {
        const url = this.dataset.url;

        fetch(url)
            .then(r => r.json())
            .then(data => {
                document.getElementById('shareGuestName').textContent = data.name;
                document.getElementById('shareMessage').value         = data.message;
                document.getElementById('shareLink').value            = data.link;

                // Update WA button setiap kali pesan diedit
                currentWaUrl = data.wa_url;
                updateWaButton();

                // Warning jika tidak ada nomor
                document.getElementById('noPhoneWarning').classList.toggle('d-none', data.has_phone);

                // Native share (mobile)
                if (navigator.share) {
                    document.getElementById('btnNativeShare').classList.remove('d-none');
                }

                new bootstrap.Modal(document.getElementById('shareModal')).show();
            });
    });
});

// Update WA URL setiap kali pesan diedit di modal
document.getElementById('shareMessage')?.addEventListener('input', updateWaButton);

function updateWaButton() {
    const msg     = document.getElementById('shareMessage').value;
    const waBtn   = document.getElementById('btnWhatsapp');

    if (currentWaUrl) {
        // Ganti text param dengan pesan yang sudah diedit
        const base = currentWaUrl.split('?text=')[0];
        waBtn.href = base + '?text=' + encodeURIComponent(msg);
    } else {
        // Tidak ada nomor → buka wa.me tanpa nomor (pilih kontak manual)
        waBtn.href = 'https://wa.me/?text=' + encodeURIComponent(msg);
    }
}

// Salin pesan
document.getElementById('btnCopyMessage')?.addEventListener('click', function () {
    const msg = document.getElementById('shareMessage').value;
    navigator.clipboard.writeText(msg).then(() => {
        this.innerHTML = '<i class="fa fa-check"></i> Tersalin';
        setTimeout(() => this.innerHTML = '<i class="fa fa-copy"></i> Salin Pesan', 2000);
    });
});

// Salin link
document.getElementById('btnCopyShareLink')?.addEventListener('click', function () {
    navigator.clipboard.writeText(document.getElementById('shareLink').value).then(() => {
        this.innerHTML = '<i class="fa fa-check"></i> Tersalin';
        setTimeout(() => this.innerHTML = '<i class="fa fa-copy"></i> Salin', 2000);
    });
});

// Native share API
document.getElementById('btnNativeShare')?.addEventListener('click', () => {
    const msg  = document.getElementById('shareMessage').value;
    const link = document.getElementById('shareLink').value;
    navigator.share({ title: 'Undangan', text: msg, url: link }).catch(() => {});
});

// ── Kirim semua via WA (buka satu per satu) ────────────────────────────────
document.getElementById('btnSendAll')?.addEventListener('click', function () {
    modalConfirm({
        message  : 'Ini akan membuka tab WhatsApp untuk setiap tamu yang memiliki nomor. Lanjutkan?',
        title    : 'Kirim Semua via WhatsApp',
        okText   : 'Ya, Kirim Semua',
        type     : 'success',
        icon     : '📲',
        onConfirm: async () => {
            const btns = document.querySelectorAll('.btn-share');
            for (const btn of btns) {
                const data = await fetch(btn.dataset.url).then(r => r.json());
                if (data.wa_url) {
                    window.open(data.wa_url, '_blank');
                    await new Promise(r => setTimeout(r, 800));
                }
            }
        },
    });
});
</script>
@endpush
