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

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</form>
@endsection
