@extends('layouts.app')
@section('title', 'Manajemen Template')
@section('page-title', 'Manajemen Template')
@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen Template</li>
@endsection
@section('content')
<div class="row">
    @forelse($templates as $template)
    <div class="col-xl-4 col-md-6">
        <div class="card">
            @if($template->thumbnail)
                <img src="{{ asset('storage/' . $template->thumbnail) }}" class="card-img-top" style="height:180px;object-fit:cover" alt="">
            @else
                <div class="bg-light d-flex align-items-center justify-content-center" style="height:180px">
                    <i class="flaticon-381-notepad" style="font-size:3rem;opacity:.3"></i>
                </div>
            @endif
            <div class="card-body">
                <h5 class="card-title">{{ $template->name }}</h5>
                <p class="text-muted small mb-1">{{ $template->description }}</p>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge badge-{{ $template->type === 'free' ? 'success' : ($template->type === 'premium' ? 'warning' : 'info') }}">
                        {{ ucfirst($template->type) }}
                    </span>
                    <span class="small fw-bold">{{ $template->formattedPrice() }}</span>
                    <span class="badge badge-{{ $template->is_active ? 'success' : 'danger' }} ms-auto">
                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>
                <p class="text-muted small mt-1">
                    <code>{{ $template->asset_folder ?? $template->slug }}</code>
                    &nbsp;·&nbsp; v{{ $template->version }}
                    &nbsp;·&nbsp; {{ $template->invitations_count }} undangan
                </p>
            </div>
            <div class="card-footer d-flex gap-2">
                <a href="{{ route('templates.edit', $template) }}" class="btn btn-warning btn-sm flex-fill">
                    <i class="fa fa-pencil"></i> Edit & Fields
                </a>
                <form action="{{ route('templates.toggle', $template) }}" method="POST">
                    @csrf @method('PATCH')
                    <button type="submit" class="btn btn-{{ $template->is_active ? 'outline-secondary' : 'outline-success' }} btn-sm"
                        title="{{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                        <i class="fa fa-{{ $template->is_active ? 'ban' : 'check' }}"></i>
                    </button>
                </form>
                <form action="{{ route('templates.destroy', $template) }}" method="POST"
                    data-confirm="Hapus template '{{ $template->name }}'? Semua field terkait akan ikut terhapus." data-confirm-ok="Hapus" data-confirm-title="Hapus Template">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm"><i class="fa fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12"><div class="card card-body text-center">Belum ada template.</div></div>
    @endforelse
</div>
<a href="{{ route('templates.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah Template</a>
@endsection
