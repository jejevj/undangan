@extends('layouts.app')
@section('title', 'Undangan Saya')
@section('page-title', 'Undangan Saya')
@section('breadcrumb')
    <li class="breadcrumb-item active">Undangan Saya</li>
@endsection
@section('content')
<div class="mb-3">
    <a href="{{ route('invitations.select-template') }}" class="btn btn-primary">
        <i class="fa fa-plus"></i> Buat Undangan Baru
    </a>
</div>
<div class="row">
    @forelse($invitations as $inv)
    <div class="col-xl-4 col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $inv->title }}</h5>
                <p class="text-muted small mb-1">Template: <strong>{{ $inv->template->name }}</strong></p>
                <p class="mb-2">
                    <span class="badge badge-{{ $inv->status === 'published' ? 'success' : ($inv->status === 'expired' ? 'danger' : 'warning') }}">
                        {{ ucfirst($inv->status) }}
                    </span>
                </p>
                @if($inv->status === 'published')
                    <p class="small text-muted">
                        Link: <a href="{{ route('invitation.show', $inv->slug) }}" target="_blank">{{ route('invitation.show', $inv->slug) }}</a>
                    </p>
                @endif
            </div>
            <div class="card-footer d-flex gap-1 flex-wrap">
                <a href="{{ route('invitations.edit', $inv) }}" class="btn btn-warning btn-xs">
                    <i class="fa fa-pencil"></i> Edit
                </a>
                <a href="{{ route('invitations.preview', $inv) }}" class="btn btn-info btn-xs" target="_blank">
                    <i class="fa fa-eye"></i> Preview
                </a>
                @if($inv->status !== 'published')
                    <form action="{{ route('invitations.publish', $inv) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-success btn-xs"><i class="fa fa-globe"></i> Publish</button>
                    </form>
                @else
                    <form action="{{ route('invitations.unpublish', $inv) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-secondary btn-xs">Unpublish</button>
                    </form>
                @endif
                <form action="{{ route('invitations.destroy', $inv) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus undangan ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="card card-body text-center">
            <p>Belum ada undangan. <a href="{{ route('invitations.select-template') }}">Buat sekarang</a></p>
        </div>
    </div>
    @endforelse
</div>
@endsection
