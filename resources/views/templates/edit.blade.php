@extends('layouts.app')
@section('title', 'Edit Template')
@section('page-title', 'Edit Template')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Template</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection
@section('content')
<div class="row">
    {{-- Form Edit Template --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Info Template</h4></div>
            <div class="card-body">
                <form action="{{ route('templates.update', $template) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')
                    <div class="form-group mb-3">
                        <label>Nama Template</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Blade View</label>
                        <input type="text" name="blade_view" class="form-control" value="{{ old('blade_view', $template->blade_view) }}" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $template->description) }}</textarea>
                    </div>
                    <div class="form-group mb-3">
                        <label>Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        @if($template->thumbnail)
                            <img src="{{ asset('storage/' . $template->thumbnail) }}" class="mt-2 img-thumbnail" style="height:80px">
                        @endif
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active" value="1" {{ $template->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm">Update</button>
                </form>
            </div>
        </div>
    </div>

    {{-- Field Management --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Fields Template</h4></div>
            <div class="card-body">
                {{-- Tambah Field --}}
                <form action="{{ route('templates.fields.store', $template) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-2">
                        <div class="col-md-4">
                            <input type="text" name="key" class="form-control form-control-sm" placeholder="key (snake_case)" required>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="label" class="form-control form-control-sm" placeholder="Label" required>
                        </div>
                        <div class="col-md-3">
                            <select name="type" class="form-control form-control-sm">
                                @foreach(['text','textarea','date','time','datetime','image','url','number','select'] as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="text" name="group" class="form-control form-control-sm" placeholder="group (opsional)">
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="order" class="form-control form-control-sm" placeholder="urutan" value="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <div class="form-check">
                                <input type="checkbox" name="required" class="form-check-input" id="req" value="1">
                                <label class="form-check-label" for="req">Wajib</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-sm w-100">+ Tambah</button>
                        </div>
                    </div>
                </form>

                {{-- Daftar Fields --}}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead><tr><th>Key</th><th>Label</th><th>Type</th><th>Group</th><th>Wajib</th><th></th></tr></thead>
                        <tbody>
                            @forelse($template->fields as $field)
                            <tr>
                                <td><code>{{ $field->key }}</code></td>
                                <td>{{ $field->label }}</td>
                                <td><span class="badge badge-info">{{ $field->type }}</span></td>
                                <td>{{ $field->group ?? '-' }}</td>
                                <td>{{ $field->required ? '✓' : '-' }}</td>
                                <td>
                                    <form action="{{ route('templates.fields.destroy', [$template, $field]) }}" method="POST" onsubmit="return confirm('Hapus field ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="6" class="text-center text-muted">Belum ada field.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
