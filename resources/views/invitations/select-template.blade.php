@extends('layouts.app')
@section('title', 'Pilih Template')
@section('page-title', 'Pilih Template Undangan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item active">Pilih Template</li>
@endsection
@section('content')
<p class="text-muted mb-4">Pilih template yang ingin Anda gunakan untuk undangan Anda.</p>
<div class="row">
    @forelse($templates as $template)
    <div class="col-xl-4 col-md-6">
        <div class="card h-100">
            @if($template->thumbnail)
                <img src="{{ asset('storage/' . $template->thumbnail) }}" class="card-img-top" style="height:200px;object-fit:cover" alt="">
            @else
                <div class="bg-gradient-primary d-flex align-items-center justify-content-center" style="height:200px;background:linear-gradient(135deg,#6f42c1,#e83e8c)">
                    <span class="text-white fw-bold fs-4">{{ $template->name }}</span>
                </div>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $template->name }}</h5>
                <p class="text-muted small">{{ $template->description }}</p>
            </div>
            <div class="card-footer">
                <a href="{{ route('invitations.create', ['template_id' => $template->id]) }}" class="btn btn-primary w-100">
                    Gunakan Template Ini
                </a>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><div class="card card-body text-center">Belum ada template tersedia.</div></div>
    @endforelse
</div>
@endsection
