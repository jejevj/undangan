@extends('layouts.app')
@section('title', 'Buat Undangan')
@section('page-title', 'Buat Undangan - ' . $template->name)
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item active">Buat Undangan</li>
@endsection
@section('content')
<form action="{{ route('invitations.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="template_id" value="{{ $template->id }}">

    <div class="card mb-4">
        <div class="card-header"><h4 class="card-title">Judul Undangan</h4></div>
        <div class="card-body">
            <div class="form-group">
                <label>Judul <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                    value="{{ old('title') }}" placeholder="Pernikahan Budi & Ani" required>
                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
    </div>

    @include('invitations._fields', ['fieldsByGroup' => $fieldsByGroup, 'existingData' => []])

    <div class="d-flex gap-2 mb-4">
        <button type="submit" class="btn btn-primary">Simpan & Lanjutkan</button>
        <a href="{{ route('invitations.select-template') }}" class="btn btn-secondary">Batal</a>
    </div>
</form>
@endsection
