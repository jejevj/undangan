@extends('layouts.app')
@section('title', 'Edit Undangan')
@section('page-title', 'Edit Undangan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('invitations.preview', $invitation) }}" class="btn btn-info btn-sm" target="_blank" rel="noopener">
        <i class="fa fa-eye"></i> Preview
    </a>
    <a href="{{ route('invitations.guests.index', $invitation) }}" class="btn btn-secondary btn-sm">
        <i class="fa fa-users"></i> Kelola Tamu
        @if($invitation->guests()->count())
            <span class="badge badge-light ms-1">{{ $invitation->guests()->count() }}</span>
        @endif
    </a>
    <a href="{{ route('invitations.gallery.index', $invitation) }}" class="btn btn-secondary btn-sm">
        <i class="fa fa-image"></i> Galeri Foto
        @php $galleryCount = $invitation->gallery()->count(); @endphp
        @if($galleryCount)
            <span class="badge badge-light ms-1">{{ $galleryCount }}</span>
        @endif
    </a>
    <a href="{{ route('invitations.gift.index', $invitation) }}" class="btn btn-secondary btn-sm">
        <i class="fa fa-gift"></i> Gift Section
        @if($invitation->isGiftActive())
            <span class="badge badge-success ms-1">{{ $invitation->bankAccounts()->count() }}</span>
        @else
            <span class="badge badge-warning ms-1">Locked</span>
        @endif
    </a>
    @if($invitation->status !== 'published')
        <form action="{{ route('invitations.publish', $invitation) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success btn-sm"><i class="fa fa-globe"></i> Publish</button>
        </form>
    @else
        <span class="badge badge-success align-self-center">Published</span>
        <a href="{{ route('invitation.show', $invitation->slug) }}" class="btn btn-outline-success btn-sm" target="_blank">
            <i class="fa fa-external-link-alt"></i> Lihat Link Publik
        </a>
        <form action="{{ route('invitations.unpublish', $invitation) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin mengubah status ke draft? Undangan tidak akan bisa diakses publik.')">
            @csrf
            <button class="btn btn-warning btn-sm"><i class="fa fa-undo"></i> Unpublish (Kembali ke Draft)</button>
        </form>
    @endif
</div>

<form action="{{ route('invitations.update', $invitation) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="card mb-4">
        <div class="card-header"><h4 class="card-title">Judul Undangan</h4></div>
        <div class="card-body">
            <div class="form-group mb-3">
                <label>Judul <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $invitation->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-group mb-0">
                <label>Tampilan Galeri Foto</label>
                <div class="d-flex gap-3 mt-1">
                    <div class="form-check">
                        <input type="radio" name="gallery_display" class="form-check-input" id="gd_grid"
                            value="grid" {{ old('gallery_display', $invitation->gallery_display ?? 'grid') === 'grid' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gd_grid">
                            <i class="fa fa-th"></i> Grid View
                            <small class="text-muted d-block">Foto ditampilkan dalam grid kotak</small>
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="gallery_display" class="form-check-input" id="gd_slideshow"
                            value="slideshow" {{ old('gallery_display', $invitation->gallery_display ?? 'grid') === 'slideshow' ? 'checked' : '' }}>
                        <label class="form-check-label" for="gd_slideshow">
                            <i class="fa fa-play-circle"></i> Slideshow
                            <small class="text-muted d-block">Foto ditampilkan bergantian otomatis</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('invitations._fields', ['fieldsByGroup' => $fieldsByGroup, 'existingData' => $existingData])

    {{-- Cerita Cinta Section --}}
    <div class="card mb-4">
        <div class="card-header">
            <h4 class="card-title mb-0"><i class="fa fa-heart text-danger"></i> Cerita Cinta</h4>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-bold">Pilih Mode Cerita Cinta</label>
                <div class="mode-selector">
                    <div class="mode-card {{ $invitation->love_story_mode === 'longtext' ? 'active' : '' }}" 
                         onclick="selectLoveStoryMode('longtext', {{ $invitation->canUseTimelineMode() ? 'true' : 'false' }})">
                        <h6 class="mb-1"><i class="fa fa-align-left"></i> Long Text</h6>
                        <p class="text-muted small mb-0">Cerita cinta dalam bentuk paragraf panjang</p>
                    </div>
                    
                    <div class="mode-card {{ $invitation->love_story_mode === 'timeline' ? 'active' : '' }} {{ !$invitation->canUseTimelineMode() ? 'disabled' : '' }}" 
                         onclick="selectLoveStoryMode('timeline', {{ $invitation->canUseTimelineMode() ? 'true' : 'false' }})">
                        @if(!$invitation->canUseTimelineMode())
                            <span class="mode-badge">PREMIUM</span>
                        @endif
                        <h6 class="mb-1"><i class="fa fa-comments"></i> Timeline Chat</h6>
                        <p class="text-muted small mb-0">Cerita dalam bentuk chat timeline</p>
                    </div>
                </div>
            </div>

            <div id="loveStoryContent">
                @if($invitation->love_story_mode === 'longtext')
                    {{-- Long Text Mode - sudah ada di form fields --}}
                    <div class="alert alert-info mb-0">
                        <i class="fa fa-info-circle"></i> Cerita cinta dalam mode Long Text dikelola melalui field "Cerita Cinta" di atas.
                    </div>
                @else
                    {{-- Timeline Mode --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Timeline Cerita Cinta</h6>
                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTimelineModal">
                            <i class="fa fa-plus"></i> Tambah Timeline
                        </button>
                    </div>

                    @if($invitation->loveStoryTimeline->isEmpty())
                        <div class="text-center py-4 bg-light rounded">
                            <i class="fa fa-comments fa-2x text-muted mb-2"></i>
                            <p class="text-muted mb-2">Belum ada timeline cerita cinta</p>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTimelineModal">
                                <i class="fa fa-plus"></i> Tambah Timeline
                            </button>
                        </div>
                    @else
                        <div id="timelineList">
                            @php $previousSender = null; @endphp
                            @foreach($invitation->loveStoryTimeline as $item)
                                @if($item->is_timeskip && $item->timeskip_label)
                                    {{-- Timeskip Separator --}}
                                    <div class="timeline-timeskip" data-id="{{ $item->id }}" data-type="timeskip">
                                        <span class="timeskip-drag-handle">⋮⋮</span>
                                        <div class="timeskip-line"></div>
                                        <div class="timeskip-label">{{ $item->timeskip_label }}</div>
                                        <div class="timeskip-line"></div>
                                        <div class="timeline-actions" style="justify-content:center;margin-top:8px;">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    onclick="editTimeline({{ $item->id }}, '{{ $item->sender }}', `{{ addslashes($item->message) }}`, {{ $item->is_timeskip ? 'true' : 'false' }}, `{{ addslashes($item->timeskip_label ?? '') }}`, '{{ $item->event_date ? $item->event_date->format('Y-m-d') : '' }}', '{{ $item->event_time }}')">
                                                <i class="fa fa-edit"></i> Edit
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTimeline({{ $item->id }})">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    @php $previousSender = null; @endphp
                                @else
                                    @php $showAvatar = ($previousSender !== $item->sender); @endphp
                                    <div class="timeline-item {{ $item->isFromBride() ? 'from-bride' : 'from-groom' }} {{ !$showAvatar ? 'no-avatar' : '' }}" data-id="{{ $item->id }}" data-type="message">
                                        <span class="timeline-drag-handle">⋮⋮</span>
                                        @if($showAvatar)
                                            <div class="timeline-avatar">
                                                {{ $item->isFromGroom() ? '♂' : '♀' }}
                                            </div>
                                        @else
                                            <div class="timeline-avatar-spacer"></div>
                                        @endif
                                        <div class="timeline-content">
                                            @if($showAvatar)
                                                <div class="timeline-header">
                                                    <span class="timeline-sender">
                                                        {{ $item->isFromGroom() ? 'Mempelai Pria' : 'Mempelai Wanita' }}
                                                    </span>
                                                    <span class="timeline-date">
                                                        {{ $item->formatted_date_time }}
                                                    </span>
                                                </div>
                                            @endif
                                            <div class="timeline-message">{{ $item->message }}</div>
                                            <div class="timeline-actions">
                                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                                        onclick="editTimeline({{ $item->id }}, '{{ $item->sender }}', `{{ addslashes($item->message) }}`, {{ $item->is_timeskip ? 'true' : 'false' }}, `{{ addslashes($item->timeskip_label ?? '') }}`, '{{ $item->event_date ? $item->event_date->format('Y-m-d') : '' }}', '{{ $item->event_time }}')">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteTimeline({{ $item->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @php $previousSender = $item->sender; @endphp
                                @endif
                            @endforeach
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</form>

{{-- Timeline Modals - Outside main form --}}
<div class="modal fade" id="addTimelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addTimelineForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Timeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_timeskip" id="addIsTimeskip" class="form-check-input" value="1" onchange="toggleTimeskipFields('add')">
                            <label class="form-check-label" for="addIsTimeskip">
                                <strong>Ini adalah Timeskip</strong>
                                <small class="d-block text-muted">Gunakan untuk menandai jeda waktu dalam cerita (contoh: "3 bulan kemudian...")</small>
                            </label>
                        </div>
                    </div>
                    
                    <div id="addTimeskipFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Label Timeskip <span class="text-danger">*</span></label>
                            <input type="text" name="timeskip_label" id="addTimeskipLabelInput" class="form-control" placeholder="Contoh: 3 bulan kemudian..." maxlength="100">
                            <small class="text-muted">Label yang akan ditampilkan sebagai pemisah timeline</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="message" id="addTimeskipMessage" class="form-control" rows="3" maxlength="1000" placeholder="Deskripsi tambahan untuk timeskip ini (tidak akan ditampilkan di undangan)"></textarea>
                            <small class="text-muted">Hanya untuk catatan internal Anda</small>
                        </div>
                    </div>
                    
                    <div id="addNormalFields">
                        <div class="mb-3">
                            <label class="form-label">Pengirim</label>
                            <select name="sender" id="addSender" class="form-select" required>
                                <option value="groom">Mempelai Pria</option>
                                <option value="bride">Mempelai Wanita</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pesan</label>
                            <textarea name="message" id="addMessage" class="form-control" rows="4" required maxlength="1000"></textarea>
                            <small class="text-muted">Maksimal 1000 karakter</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tanggal (Opsional)</label>
                                <input type="date" name="event_date" id="addEventDate" class="form-control">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Jam (Opsional)</label>
                                <input type="time" name="event_time" id="addEventTime" class="form-control">
                            </div>
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

<div class="modal fade" id="editTimelineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editTimelineForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="editTimelineId">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Timeline</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_timeskip" id="editIsTimeskip" class="form-check-input" value="1" onchange="toggleTimeskipFields('edit')">
                            <label class="form-check-label" for="editIsTimeskip">
                                <strong>Ini adalah Timeskip</strong>
                                <small class="d-block text-muted">Gunakan untuk menandai jeda waktu dalam cerita</small>
                            </label>
                        </div>
                    </div>
                    
                    <div id="editTimeskipFields" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Label Timeskip <span class="text-danger">*</span></label>
                            <input type="text" name="timeskip_label" id="editTimeskipLabelInput" class="form-control" placeholder="Contoh: 3 bulan kemudian..." maxlength="100">
                            <small class="text-muted">Label yang akan ditampilkan sebagai pemisah timeline</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi (Opsional)</label>
                            <textarea name="message" id="editTimeskipMessage" class="form-control" rows="3" maxlength="1000" placeholder="Deskripsi tambahan untuk timeskip ini (tidak akan ditampilkan di undangan)"></textarea>
                            <small class="text-muted">Hanya untuk catatan internal Anda</small>
                        </div>
                    </div>
                    
                    <div id="editNormalFields">
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

@push('styles')
<style>
.mode-selector {
    display: flex;
    gap: 12px;
    margin-bottom: 20px;
}
.mode-card {
    flex: 1;
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
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
    border-color: #e5e7eb;
}
.mode-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 2px 8px;
    background: #fbbf24;
    color: #78350f;
    border-radius: 12px;
    font-size: 10px;
    font-weight: 600;
}
.timeline-item {
    display: flex;
    gap: 12px;
    margin-bottom: 16px;
    padding: 12px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #3b82f6;
    position: relative;
}
.timeline-item.from-bride {
    border-left-color: #ec4899;
}
.timeline-item.no-avatar {
    margin-top: -8px;
    padding-top: 8px;
}
.timeline-drag-handle {
    position: absolute;
    left: -8px;
    top: 12px;
    cursor: move;
    color: #9ca3af;
    font-size: 16px;
    opacity: 0;
    transition: opacity 0.2s;
}
.timeline-item:hover .timeline-drag-handle {
    opacity: 1;
}
.timeline-item.sortable-ghost,
.timeline-timeskip.sortable-ghost {
    opacity: 0.4;
    background: #e5e7eb;
}
.timeline-item.sortable-drag,
.timeline-timeskip.sortable-drag {
    opacity: 0.8;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.timeline-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #3b82f6;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
    font-size: 18px;
}
.timeline-avatar-spacer {
    width: 40px;
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
    margin-bottom: 6px;
}
.timeline-sender {
    font-weight: 600;
    color: #111827;
    font-size: 14px;
}
.timeline-date {
    font-size: 12px;
    color: #6b7280;
}
.timeline-message {
    color: #374151;
    line-height: 1.5;
    font-size: 14px;
}
.timeline-actions {
    display: flex;
    gap: 6px;
    margin-top: 8px;
}
.timeline-timeskip {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 24px 0;
    padding: 12px;
    cursor: move;
    position: relative;
}
.timeline-timeskip:hover {
    background: #f9fafb;
    border-radius: 8px;
}
.timeline-timeskip.sortable-ghost {
    opacity: 0.4;
    background: #e5e7eb;
}
.timeline-timeskip.sortable-drag {
    opacity: 0.8;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
.timeskip-drag-handle {
    position: absolute;
    left: -8px;
    top: 50%;
    transform: translateY(-50%);
    cursor: move;
    color: #9ca3af;
    font-size: 18px;
    opacity: 0;
    transition: opacity 0.2s;
}
.timeline-timeskip:hover .timeskip-drag-handle {
    opacity: 1;
}
.timeskip-line {
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, transparent, #d1d5db, transparent);
}
.timeskip-label {
    font-size: 13px;
    color: #6b7280;
    font-style: italic;
    white-space: nowrap;
    padding: 4px 12px;
    background: #f3f4f6;
    border-radius: 12px;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="{{ asset('assets/js/love-story-timeline.js') }}"></script>
<script>
// Initialize timeline handler when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        LoveStoryTimeline.init({{ $invitation->id }});
        initTimelineSortable();
    });
} else {
    LoveStoryTimeline.init({{ $invitation->id }});
    initTimelineSortable();
}

// Initialize Sortable for drag & drop timeline
function initTimelineSortable() {
    var timelineList = document.getElementById('timelineList');
    if (!timelineList) return;
    
    // Destroy existing sortable if any
    if (timelineList.sortableInstance) {
        timelineList.sortableInstance.destroy();
    }
    
    timelineList.sortableInstance = new Sortable(timelineList, {
        animation: 150,
        handle: '.timeline-drag-handle, .timeskip-drag-handle', // Drag handles
        ghostClass: 'sortable-ghost',
        dragClass: 'sortable-drag',
        onEnd: function(evt) {
            // Get all timeline IDs in new order
            var items = timelineList.children;
            var order = [];
            
            for (var i = 0; i < items.length; i++) {
                var id = items[i].getAttribute('data-id');
                if (id) {
                    order.push(id);
                }
            }
            
            // Send AJAX request to update order
            updateTimelineOrder(order);
        }
    });
}

// Update timeline order via AJAX
function updateTimelineOrder(timelineIds) {
    var invitationId = {{ $invitation->id }};
    var csrfToken = '{{ csrf_token() }}';
    
    fetch('/dash/invitations/' + invitationId + '/love-story/update-order', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            timeline_ids: timelineIds
        })
    })
    .then(function(response) {
        return response.json();
    })
    .then(function(result) {
        if (result.success) {
            console.log('Timeline order updated successfully');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: result.message || 'Gagal mengupdate urutan timeline'
            });
            // Reload page to restore original order
            location.reload();
        }
    })
    .catch(function(error) {
        console.error('Error updating timeline order:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Terjadi kesalahan saat mengupdate urutan timeline'
        });
        // Reload page to restore original order
        location.reload();
    });
}

// Mode selector
window.selectLoveStoryMode = function(mode, canUseTimeline) {
    if (mode === 'timeline' && !canUseTimeline) {
        Swal.fire({
            icon: 'info',
            title: 'Fitur Premium',
            text: 'Timeline Chat hanya tersedia untuk paket Premium. Upgrade paket Anda untuk menggunakan fitur ini!',
            showCancelButton: true,
            confirmButtonText: 'Upgrade Sekarang',
            cancelButtonText: 'Nanti Saja'
        }).then(function(result) {
            if (result.isConfirmed) {
                window.location.href = '{{ route('subscription.index') }}';
            }
        });
        return;
    }
    
    // Submit form to switch mode
    var form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route('invitations.love-story.switch-mode', $invitation) }}';
    
    var tokenInput = document.createElement('input');
    tokenInput.type = 'hidden';
    tokenInput.name = '_token';
    tokenInput.value = '{{ csrf_token() }}';
    form.appendChild(tokenInput);
    
    var modeInput = document.createElement('input');
    modeInput.type = 'hidden';
    modeInput.name = 'mode';
    modeInput.value = mode;
    form.appendChild(modeInput);
    
    document.body.appendChild(form);
    form.submit();
};

// Toggle timeskip fields
window.toggleTimeskipFields = function(mode) {
    var checkbox = document.getElementById(mode + 'IsTimeskip');
    var timeskipFields = document.getElementById(mode + 'TimeskipFields');
    var normalFields = document.getElementById(mode + 'NormalFields');
    
    if (checkbox.checked) {
        // Show timeskip fields, hide normal fields
        timeskipFields.style.display = 'block';
        normalFields.style.display = 'none';
        
        // Make timeskip label required, normal fields not required
        var timeskipLabelInput = document.getElementById(mode + 'TimeskipLabelInput');
        var timeskipMessageInput = document.getElementById(mode + 'TimeskipMessage');
        var senderSelect = document.getElementById(mode + 'Sender');
        var messageTextarea = document.getElementById(mode + 'Message');
        
        if (timeskipLabelInput) timeskipLabelInput.required = true;
        if (timeskipMessageInput) timeskipMessageInput.required = false;
        if (senderSelect) senderSelect.required = false;
        if (messageTextarea) messageTextarea.required = false;
    } else {
        // Show normal fields, hide timeskip fields
        timeskipFields.style.display = 'none';
        normalFields.style.display = 'block';
        
        // Make normal fields required, timeskip not required
        var timeskipLabelInput = document.getElementById(mode + 'TimeskipLabelInput');
        var timeskipMessageInput = document.getElementById(mode + 'TimeskipMessage');
        var senderSelect = document.getElementById(mode + 'Sender');
        var messageTextarea = document.getElementById(mode + 'Message');
        
        if (timeskipLabelInput) timeskipLabelInput.required = false;
        if (timeskipMessageInput) timeskipMessageInput.required = false;
        if (senderSelect) senderSelect.required = true;
        if (messageTextarea) messageTextarea.required = true;
    }
};
</script>
@endpush
