@extends('layouts.app')
@section('title', 'Edit Template — ' . $template->name)
@section('page-title', 'Edit Template')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('templates.index') }}">Manajemen Template</a></li>
    <li class="breadcrumb-item active">{{ $template->name }}</li>
@endsection

@section('content')

{{-- Header info --}}
<div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
    <div>
        <span class="badge badge-{{ $template->type === 'free' ? 'success' : ($template->type === 'premium' ? 'warning' : 'info') }} fs-6">
            {{ ucfirst($template->type) }}
        </span>
        <span class="badge badge-{{ $template->is_active ? 'success' : 'danger' }} ms-1 fs-6">
            {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
        </span>
    </div>
    <span class="text-muted small">
        <code>{{ $template->asset_folder ?? $template->slug }}</code>
        &nbsp;·&nbsp; v{{ $template->version }}
        &nbsp;·&nbsp; {{ $template->invitations()->count() }} undangan menggunakan template ini
    </span>
    {{-- Toggle aktif/nonaktif langsung --}}
    <form action="{{ route('templates.toggle', $template) }}" method="POST" class="ms-auto">
        @csrf @method('PATCH')
        <button type="submit" class="btn btn-{{ $template->is_active ? 'outline-danger' : 'outline-success' }} btn-sm">
            <i class="fa fa-{{ $template->is_active ? 'ban' : 'check' }}"></i>
            {{ $template->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
        </button>
    </form>
</div>

<div class="row">

    {{-- ── Kolom Kiri: Info Template ─────────────────────────────────── --}}
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header"><h4 class="card-title">Informasi Template</h4></div>
            <div class="card-body">
                <form action="{{ route('templates.update', $template) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    {{-- Nama --}}
                    <div class="form-group mb-3">
                        <label>Nama Template <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                            value="{{ old('name', $template->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Tipe & Harga --}}
                    <div class="row">
                        <div class="col-6 form-group mb-3">
                            <label>Tipe <span class="text-danger">*</span></label>
                            <select name="type" class="form-control" id="typeSelect">
                                @foreach(['free' => 'Gratis', 'premium' => 'Premium', 'custom' => 'Custom Order'] as $val => $label)
                                    <option value="{{ $val }}" {{ old('type', $template->type) === $val ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 form-group mb-3" id="priceGroup">
                            <label>Harga (Rp)</label>
                            <input type="number" name="price" class="form-control"
                                value="{{ old('price', $template->price) }}" min="0" step="1000">
                            <small class="text-muted">0 = gratis</small>
                        </div>
                    </div>

                    {{-- Folder & Versi --}}
                    <div class="row">
                        <div class="col-7 form-group mb-3">
                            <label>Folder Assets <span class="text-danger">*</span></label>
                            <input type="text" name="asset_folder" class="form-control @error('asset_folder') is-invalid @enderror"
                                value="{{ old('asset_folder', $template->asset_folder) }}" required>
                            <small class="text-muted"><code>public/invitation-assets/{folder}/</code></small>
                            @error('asset_folder')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-5 form-group mb-3">
                            <label>Versi</label>
                            <input type="text" name="version" class="form-control"
                                value="{{ old('version', $template->version) }}" placeholder="1.0.0">
                        </div>
                    </div>

                    {{-- Blade View --}}
                    <div class="form-group mb-3">
                        <label>Blade View <span class="text-danger">*</span></label>
                        <input type="text" name="blade_view" class="form-control @error('blade_view') is-invalid @enderror"
                            value="{{ old('blade_view', $template->blade_view) }}" required>
                        <small class="text-muted">Contoh: <code>invitation-templates.premium-white-1.index</code></small>
                        @error('blade_view')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Preview URL --}}
                    <div class="form-group mb-3">
                        <label>URL Preview / Demo</label>
                        <input type="url" name="preview_url" class="form-control"
                            value="{{ old('preview_url', $template->preview_url) }}" placeholder="https://...">
                        <small class="text-muted">Link demo atau screenshot template</small>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="form-group mb-3">
                        <label>Deskripsi</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $template->description) }}</textarea>
                    </div>

                    {{-- Thumbnail --}}
                    <div class="form-group mb-3">
                        <label>Thumbnail</label>
                        <input type="file" name="thumbnail" class="form-control" accept="image/*">
                        @if($template->thumbnail)
                            <div class="mt-2 d-flex align-items-center gap-2">
                                <img src="{{ asset('storage/' . $template->thumbnail) }}"
                                    class="img-thumbnail" style="height:70px">
                                <small class="text-muted">Upload baru untuk mengganti</small>
                            </div>
                        @endif
                    </div>

                    {{-- Status Aktif --}}
                    <div class="form-check form-switch mb-3">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" class="form-check-input" id="is_active"
                            value="1" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Template Aktif
                            <small class="text-muted d-block">Template nonaktif tidak bisa dipilih user</small>
                        </label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan Perubahan
                        </button>
                        <a href="{{ route('templates.index') }}" class="btn btn-secondary">Kembali</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Info Struktur Folder --}}
        <div class="card mt-3">
            <div class="card-header"><h4 class="card-title">Struktur Folder</h4></div>
            <div class="card-body p-3">
                <pre class="mb-0 small bg-light p-3 rounded" style="font-size:.8rem">public/invitation-assets/
  {{ $template->asset_folder ?? $template->slug }}/
    css/style.css
    js/app.js
    images/
    fonts/

resources/views/invitation-templates/
  {{ $template->asset_folder ?? $template->slug }}/
    index.blade.php</pre>
            </div>
        </div>
    </div>

    {{-- ── Kolom Kanan: Field Management ────────────────────────────── --}}
    <div class="col-lg-7">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Fields Template</h4>
                <span class="badge badge-primary">{{ $template->fields->count() }} field</span>
            </div>
            <div class="card-body">

                {{-- Load Preset --}}
                <div class="d-flex align-items-center gap-2 mb-3 p-3 bg-light rounded">
                    <form action="{{ route('templates.load-preset', $template) }}" method="POST" class="d-flex gap-2 align-items-center flex-fill">
                        @csrf
                        <select name="preset" class="form-control form-control-sm" style="max-width:260px">
                            @foreach($presets as $key => $label)
                                @if($key !== 'empty')
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endif
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-outline-primary btn-sm"
                            data-confirm="Load preset ini? Field yang sudah ada tidak akan ditimpa, hanya field baru yang ditambahkan."
                            data-confirm-title="Load Default Fields"
                            data-confirm-ok="Ya, Load"
                            data-confirm-type="info">
                            <i class="fa fa-download"></i> Load Preset
                        </button>
                    </form>
                    <small class="text-muted">Field yang sudah ada tidak akan ditimpa.</small>
                </div>

                {{-- Form Tambah Field --}}
                <form action="{{ route('templates.fields.store', $template) }}" method="POST" class="mb-4">
                    @csrf
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="small">Key <span class="text-danger">*</span></label>
                            <input type="text" name="key" class="form-control form-control-sm"
                                placeholder="groom_name" required>
                        </div>
                        <div class="col-md-4">
                            <label class="small">Label <span class="text-danger">*</span></label>
                            <input type="text" name="label" class="form-control form-control-sm"
                                placeholder="Nama Mempelai Pria" required>
                        </div>
                        <div class="col-md-2">
                            <label class="small">Tipe</label>
                            <select name="type" class="form-control form-control-sm">
                                @foreach(['text','textarea','date','time','datetime','image','url','number','select'] as $t)
                                    <option value="{{ $t }}">{{ $t }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="small">Group</label>
                            <input type="text" name="group" class="form-control form-control-sm" placeholder="mempelai">
                        </div>
                        <div class="col-md-1">
                            <label class="small">Urutan</label>
                            <input type="number" name="order" class="form-control form-control-sm" value="0">
                        </div>
                        <div class="col-md-2 d-flex align-items-center gap-2">
                            <div class="form-check mb-0">
                                <input type="checkbox" name="required" class="form-check-input" id="req" value="1">
                                <label class="form-check-label small" for="req">Wajib</label>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-success btn-sm w-100">
                                <i class="fa fa-plus"></i> Tambah
                            </button>
                        </div>
                    </div>
                </form>

                {{-- Daftar Fields --}}
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Key</th>
                                <th>Label</th>
                                <th>Type</th>
                                <th>Group</th>
                                <th width="40">Wajib</th>
                                <th width="40">Urutan</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($template->fields as $field)
                            <tr>
                                <td><code class="small">{{ $field->key }}</code></td>
                                <td class="small">{{ $field->label }}</td>
                                <td><span class="badge badge-info">{{ $field->type }}</span></td>
                                <td><span class="text-muted small">{{ $field->group ?? '—' }}</span></td>
                                <td class="text-center">{{ $field->required ? '✓' : '—' }}</td>
                                <td class="text-center text-muted small">{{ $field->order }}</td>
                                <td>
                                    <form action="{{ route('templates.fields.destroy', [$template, $field]) }}" method="POST"
                                        data-confirm="Hapus field '{{ $field->key }}'?"
                                        data-confirm-ok="Hapus"
                                        data-confirm-title="Hapus Field">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-3">
                                    Belum ada field. Tambahkan field di atas.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
// Sembunyikan field harga jika tipe = free
const typeSelect  = document.getElementById('typeSelect');
const priceGroup  = document.getElementById('priceGroup');

function togglePrice() {
    priceGroup.style.opacity = typeSelect.value === 'free' ? '.4' : '1';
}

typeSelect?.addEventListener('change', togglePrice);
togglePrice();
</script>
@endpush
