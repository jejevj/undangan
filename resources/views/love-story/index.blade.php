@extends('layouts.app')
@section('title', 'Cerita Cinta')
@section('page-title', 'Cerita Cinta Undangan')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item"><a href="{{ route('invitations.edit', $invitation) }}">{{ Str::limit($invitation->title, 30) }}</a></li>
    <li class="breadcrumb-item active">Cerita Cinta</li>
@endsection

@push('styles')
<style>
.mode-selector {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}
.mode-card {
    flex: 1;
    padding: 20px;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
}
.mode-card:hover {
    border-color: #3b82f6;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}
.mode-card.active {
    border-color: #3b82f6;
    background: #eff6ff;
}
.mode-card.disabled {
    opacity: 0.5;
    cursor: not-allowed;
}
.mode-card.disabled:hover {
    transform: none;
    box-shadow: none;
}
.mode-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    padding: 4px 12px;
    background: #fbbf24;
    color: #78350f;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
}
.timeline-item {
    display: flex;
    gap: 16px;
    margin-bottom: 20px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 4px solid #3b82f6;
}
.timeline-item.from-bride {
    border-left-color: #ec4899;
}
.timeline-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}
.timeline-item.from-bride .timeline-avatar {
    background: #ec4899;
}
.timeline-content {
    flex: 1;
}
.timeline-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}
.timeline-sender {
    font-weight: 600;
    color: #111827;
}
.timeline-date {
    font-size: 13px;
    color: #6b7280;
}
.timeline-message {
    color: #374151;
    line-height: 1.6;
}
.timeline-actions {
    display: flex;
    gap: 8px;
    margin-top: 12px;
}
</style>
@endpush

@section('content')

<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title mb-3">Pilih Mode Cerita Cinta</h5>
        
        <form action="{{ route('invitations.love-story.switch-mode', $invitation) }}" method="POST" id="switchModeForm">
            @csrf
            <input type="hidden" name="mode" id="selectedMode" value="{{ $invitation->love_story_mode }}">
            
            <div class="mode-selector">
                <div class="mode-card {{ $invitation->love_story_mode === 'longtext' ? 'active' : '' }}" 
                     onclick="selectMode('longtext')">
                    <h6><i class="fa fa-align-left"></i> Long Text</h6>
                    <p class="text-muted small mb-0">Cerita cinta dalam bentuk paragraf panjang. Cocok untuk cerita yang mengalir.</p>
                </div>
                
                <div class="mode-card {{ $invitation->love_story_mode === 'timeline' ? 'active' : '' }} {{ !$canUseTimeline ? 'disabled' : '' }}" 
                     onclick="{{ $canUseTimeline ? 'selectMode(\'timeline\')' : 'showUpgradeAlert()' }}">
                    @if(!$canUseTimeline)
                        <span class="mode-badge">PREMIUM</span>
                    @endif
                    <h6><i class="fa fa-comments"></i> Timeline Chat</h6>
                    <p class="text-muted small mb-0">Cerita cinta dalam bentuk chat timeline dengan tanggal dan jam. Lebih interaktif!</p>
                </div>
            </div>
        </form>
    </div>
</div>

@if($invitation->love_story_mode === 'longtext')
    {{-- Long Text Mode --}}
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Cerita Cinta (Long Text)</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">Cerita cinta dalam mode Long Text dikelola melalui field "Cerita Cinta" di halaman edit undangan atau Live Edit.</p>
            <a href="{{ route('invitations.edit', $invitation) }}" class="btn btn-primary">
                <i class="fa fa-edit"></i> Edit Undangan
            </a>
        </div>
    </div>
@else
    {{-- Timeline Mode --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0">Timeline Cerita Cinta</h5>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTimelineModal">
                <i class="fa fa-plus"></i> Tambah Timeline
            </button>
        </div>
        <div class="card-body">
            @if($timeline->isEmpty())
                <div class="text-center py-5">
                    <i class="fa fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">Belum ada timeline cerita cinta. Tambahkan timeline pertama Anda!</p>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTimelineModal">
                        <i class="fa fa-plus"></i> Tambah Timeline
                    </button>
                </div>
            @else
                <div id="timelineList">
                    @foreach($timeline as $item)
                        <div class="timeline-item {{ $item->isFromBride() ? 'from-bride' : 'from-groom' }}" data-id="{{ $item->id }}">
                            <div class="timeline-avatar">
                                {{ $item->isFromGroom() ? '♂' : '♀' }}
                            </div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <span class="timeline-sender">
                                        {{ $item->isFromGroom() ? 'Mempelai Pria' : 'Mempelai Wanita' }}
                                    </span>
                                    <span class="timeline-date">
                                        {{ $item->formatted_date_time }}
                                    </span>
                                </div>
                                <div class="timeline-message">{{ $item->message }}</div>
                                <div class="timeline-actions">
                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                            onclick="editTimeline({{ $item->id }}, '{{ $item->sender }}', '{{ addslashes($item->message) }}', '{{ $item->event_date ? $item->event_date->format('Y-m-d') : '' }}', '{{ $item->event_time }}')">
                                        <i class="fa fa-edit"></i> Edit
                                    </button>
                                    <form action="{{ route('invitations.love-story.destroy', [$invitation, $item]) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Hapus timeline item ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="fa fa-trash"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

{{-- Add Timeline Modal --}}
<div class="modal fade" id="addTimelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('invitations.love-story.store', $invitation) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Timeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pengirim</label>
                        <select name="sender" class="form-select" required>
                            <option value="groom">Mempelai Pria</option>
                            <option value="bride">Mempelai Wanita</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea name="message" class="form-control" rows="4" required maxlength="1000"></textarea>
                        <small class="text-muted">Maksimal 1000 karakter</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal (Opsional)</label>
                            <input type="date" name="event_date" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam (Opsional)</label>
                            <input type="time" name="event_time" class="form-control">
                        </div>
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

{{-- Edit Timeline Modal --}}
<div class="modal fade" id="editTimelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTimelineForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit Timeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Pengirim</label>
                        <select name="sender" id="editSender" class="form-select" required>
                            <option value="groom">Mempelai Pria</option>
                            <option value="bride">Mempelai Wanita</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pesan</label>
                        <textarea name="message" id="editMessage" class="form-control" rows="4" required maxlength="1000"></textarea>
                        <small class="text-muted">Maksimal 1000 karakter</small>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal (Opsional)</label>
                            <input type="date" name="event_date" id="editEventDate" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jam (Opsional)</label>
                            <input type="time" name="event_time" id="editEventTime" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function selectMode(mode) {
    @if(!$canUseTimeline)
    if (mode === 'timeline') {
        showUpgradeAlert();
        return;
    }
    @endif
    
    document.getElementById('selectedMode').value = mode;
    document.getElementById('switchModeForm').submit();
}

function showUpgradeAlert() {
    Swal.fire({
        icon: 'info',
        title: 'Fitur Premium',
        text: 'Timeline Chat hanya tersedia untuk paket Premium. Upgrade paket Anda untuk menggunakan fitur ini!',
        showCancelButton: true,
        confirmButtonText: 'Upgrade Sekarang',
        cancelButtonText: 'Nanti Saja'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route('subscription.index') }}';
        }
    });
}

function editTimeline(id, sender, message, eventDate, eventTime) {
    document.getElementById('editTimelineForm').action = `/dash/invitations/{{ $invitation->id }}/love-story/${id}`;
    document.getElementById('editSender').value = sender;
    document.getElementById('editMessage').value = message;
    document.getElementById('editEventDate').value = eventDate;
    document.getElementById('editEventTime').value = eventTime;
    
    new bootstrap.Modal(document.getElementById('editTimelineModal')).show();
}
</script>
@endpush
