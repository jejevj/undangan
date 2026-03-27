@extends('layouts.app')

@section('title', 'Manajemen Menu')
@section('page-title', 'Manajemen Menu')

@section('breadcrumb')
    <li class="breadcrumb-item active">Manajemen Menu</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Daftar Menu</h4>
                <a href="{{ route('menus.create') }}" class="btn btn-primary btn-sm">
                    <i class="fa fa-plus"></i> Tambah Menu
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>URL</th>
                                <th>Icon</th>
                                <th>Parent</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($menus as $i => $menu)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $menu->name }}</strong></td>
                                <td>{{ $menu->url ?? '-' }}</td>
                                <td><i class="{{ $menu->icon }}"></i> <small>{{ $menu->icon }}</small></td>
                                <td>-</td>
                                <td>{{ $menu->order }}</td>
                                <td>
                                    <span class="badge badge-{{ $menu->is_active ? 'success' : 'danger' }}">
                                        {{ $menu->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('menus.edit', $menu) }}" class="btn btn-warning btn-xs me-1">
                                        <i class="fa fa-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('menus.destroy', $menu) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @if($menu->children->count())
                                @foreach($menu->children as $child)
                                <tr class="table-light">
                                    <td></td>
                                    <td class="ps-4">↳ {{ $child->name }}</td>
                                    <td>{{ $child->url ?? '-' }}</td>
                                    <td><i class="{{ $child->icon }}"></i></td>
                                    <td>{{ $menu->name }}</td>
                                    <td>{{ $child->order }}</td>
                                    <td>
                                        <span class="badge badge-{{ $child->is_active ? 'success' : 'danger' }}">
                                            {{ $child->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('menus.edit', $child) }}" class="btn btn-warning btn-xs me-1">
                                            <i class="fa fa-pencil"></i> Edit
                                        </a>
                                        <form action="{{ route('menus.destroy', $child) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus menu ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i> Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                            @empty
                            <tr><td colspan="8" class="text-center">Belum ada menu.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
