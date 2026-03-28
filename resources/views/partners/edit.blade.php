@extends('layouts.app')
@section('title', 'Edit Partner')
@section('page-title', 'Edit Partner')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('partners.index') }}">Manajemen Partner</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Form Edit Partner</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('partners.update', $partner) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Nama Partner <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $partner->name) }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Website URL</label>
                        <input type="url" name="site_url" class="form-control @error('site_url') is-invalid @enderror" 
                               value="{{ old('site_url', $partner->site_url) }}" placeholder="https://example.com">
                        @error('site_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">URL website partner (opsional)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Logo Partner</label>
                        
                        @if($partner->logo)
                        <div class="mb-2">
                            <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" class="img-thumbnail" style="max-height: 100px;">
                            <p class="text-muted small mt-1">Logo saat ini</p>
                        </div>
                        @endif
                        
                        <input type="file" name="logo" class="form-control @error('logo') is-invalid @enderror" 
                               accept="image/*">
                        @error('logo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Format: JPG, PNG, GIF, SVG. Maksimal 2MB. Kosongkan jika tidak ingin mengubah logo.</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="order" class="form-control @error('order') is-invalid @enderror" 
                               value="{{ old('order', $partner->order) }}" min="0">
                        @error('order')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Urutan tampilan di landing page (semakin kecil semakin di depan)</small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" id="is_active" 
                                   value="1" {{ old('is_active', $partner->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktif (tampilkan di landing page)
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="{{ route('partners.index') }}" class="btn btn-secondary">
                            <i class="fa fa-arrow-left"></i> Kembali
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
