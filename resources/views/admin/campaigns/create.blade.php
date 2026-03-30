@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Buat Kampanye Baru</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.campaigns.store') }}" method="POST">
                        @csrf

                        <div class="form-group">
                            <label for="name">Nama Kampanye <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Contoh: Promo Tahun Baru 2026</small>
                        </div>

                        <div class="form-group">
                            <label for="code">Kode Kampanye <span class="text-danger">*</span></label>
                            <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" required>
                            @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Hanya huruf, angka, dash, dan underscore. Contoh: NEWYEAR2026</small>
                            <small class="form-text text-info">URL: {{ route('register') }}?ref=<strong id="code-preview">KODE</strong></small>
                        </div>

                        <div class="form-group">
                            <label for="description">Deskripsi</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pricing_plan_id">Plan yang Diberikan <span class="text-danger">*</span></label>
                            <select name="pricing_plan_id" id="pricing_plan_id" class="form-control @error('pricing_plan_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Plan --</option>
                                @foreach($plans as $plan)
                                <option value="{{ $plan->id }}" {{ old('pricing_plan_id') == $plan->id ? 'selected' : '' }}>
                                    {{ $plan->name }} - Rp {{ number_format($plan->price, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </select>
                            @error('pricing_plan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Plan yang akan diberikan gratis kepada user yang mendaftar dengan kode ini</small>
                        </div>

                        <div class="form-group">
                            <label for="max_users">Maksimal User <span class="text-danger">*</span></label>
                            <input type="number" name="max_users" id="max_users" class="form-control @error('max_users') is-invalid @enderror" value="{{ old('max_users', 10) }}" min="0" required>
                            @error('max_users')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Jumlah maksimal user yang bisa menggunakan kampanye ini. Isi 0 untuk unlimited.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="start_date">Tanggal Mulai</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                                    @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan jika langsung aktif</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="end_date">Tanggal Berakhir</label>
                                    <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                                    @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">Kosongkan jika tidak ada batas waktu</small>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="custom-control-label" for="is_active">Aktifkan kampanye</label>
                            </div>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> Simpan Kampanye
                            </button>
                            <a href="{{ route('admin.campaigns.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('code').addEventListener('input', function() {
    document.getElementById('code-preview').textContent = this.value || 'KODE';
});
</script>
@endsection
