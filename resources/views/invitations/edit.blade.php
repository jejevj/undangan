@extends('layouts.app')
@section('title', 'Edit Undangan')
@section('page-title', 'Edit Undangan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="d-flex gap-2 mb-3">
    <a href="{{ route('invitations.preview', $invitation) }}" class="btn btn-info btn-sm" target="_blank">
        <i class="fa fa-eye"></i> Preview
    </a>
    @if($invitation->status !== 'published')
        <form action="{{ route('invitations.publish', $invitation) }}" method="POST" class="d-inline">
            @csrf
            <button class="btn btn-success btn-sm"><i class="fa fa-globe"></i> Publish</button>
        </form>
    @else
        <span class="badge badge-success align-self-center">Published</span>
        <a href="{{ route('invitation.show', $invitation->slug) }}" class="btn btn-outline-success btn-sm" target="_blank">
            Lihat Link Publik
        </a>
    @endif
</div>

<form action="{{ route('invitations.update', $invitation) }}" method="POST" enctype="multipart/form-data">
    @csrf @method('PUT')

    <div class="card mb-4">
        <div class="card-header"><h4 class="card-title">Judul Undangan</h4></div>
        <div class="card-body">
            <div class="form-group">
                <label>Judul <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title', $invitation->title) }}" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
