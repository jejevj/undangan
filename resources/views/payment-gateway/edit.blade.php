@extends('layouts.app')

@section('title', 'Edit Konfigurasi Payment Gateway')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('payment-gateway.index') }}">Payment Gateway</a></li>
    <li class="breadcrumb-item active">Edit Konfigurasi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Konfigurasi Payment Gateway</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('payment-gateway.update', $config) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Provider <span class="text-danger">*</span></label>
                        <select name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                            <option value="doku" {{ $config->provider === 'doku' ? 'selected' : '' }}>DOKU</option>
                        </select>
                        @error('provider')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Environment <span class="text-danger">*</span></label>
                        <select name="environment" class="form-control @error('environment') is-invalid @enderror" required>
                            <option value="sandbox" {{ $config->environment === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                            <option value="production" {{ $config->environment === 'production' ? 'selected' : '' }}>Production (Live)</option>
                        </select>
                        @error('environment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        @if($config->environment === 'production')
                            <div class="alert alert-warning mt-2">
                                <i class="fa fa-exclamation-triangle"></i> Mode Production - Transaksi akan real!
                            </div>
                        @endif
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client ID <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="client_id" 
                               class="form-control @error('client_id') is-invalid @enderror" 
                               value="{{ old('client_id', $config->client_id) }}"
                               required>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Partner Service ID</label>
                        <input type="text" 
                               name="partner_service_id" 
                               class="form-control @error('partner_service_id') is-invalid @enderror" 
                               value="{{ old('partner_service_id', $config->partner_service_id) }}"
                               maxlength="8"
                               placeholder="Contoh: '  888994' (max 7 spasi + 1-8 digit)">
                        @error('partner_service_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Partner Service ID untuk Virtual Account (max 7 spasi + 1-8 digit)<br>
                            Format: '  888994' (spasi di depan + digit, total max 8 karakter)<br>
                            Kosongkan untuk auto-generate dari Client ID
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Secret Key</label>
                        <input type="password" 
                               name="secret_key" 
                               class="form-control @error('secret_key') is-invalid @enderror" 
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('secret_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah secret key</small>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">SNAP API Configuration (Optional)</h5>
                    <p class="text-muted small">Field berikut diperlukan untuk menggunakan SNAP API DOKU</p>

                    <div class="mb-3">
                        <label class="form-label">Private Key</label>
                        <textarea name="private_key" 
                                  rows="4"
                                  class="form-control @error('private_key') is-invalid @enderror" 
                                  placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----&#10;&#10;Kosongkan jika tidak ingin mengubah">{{ old('private_key') }}</textarea>
                        @error('private_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Private key untuk signature (akan dienkripsi)<br>
                            @if($config->private_key)
                                <span class="text-success"><i class="fa fa-check"></i> Private key sudah tersimpan</span>
                            @else
                                <span class="text-muted">Belum ada private key tersimpan</span>
                            @endif
                        </small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Public Key</label>
                        <textarea name="public_key" 
                                  rows="4"
                                  class="form-control @error('public_key') is-invalid @enderror" 
                                  placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----&#10;&#10;Kosongkan jika tidak ingin mengubah">{{ old('public_key', $config->public_key) }}</textarea>
                        @error('public_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Public key Anda</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">DOKU Public Key</label>
                        <textarea name="doku_public_key" 
                                  rows="4"
                                  class="form-control @error('doku_public_key') is-invalid @enderror" 
                                  placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----&#10;&#10;Kosongkan jika tidak ingin mengubah">{{ old('doku_public_key', $config->doku_public_key) }}</textarea>
                        @error('doku_public_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Public key dari DOKU untuk verifikasi callback</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Issuer</label>
                        <input type="text" 
                               name="issuer" 
                               class="form-control @error('issuer') is-invalid @enderror" 
                               value="{{ old('issuer', $config->issuer) }}"
                               placeholder="Contoh: nama-merchant">
                        @error('issuer')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Issuer identifier (optional)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Auth Code</label>
                        <input type="password" 
                               name="auth_code" 
                               class="form-control @error('auth_code') is-invalid @enderror" 
                               placeholder="Kosongkan jika tidak ingin mengubah">
                        @error('auth_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Auth code untuk payment tertentu (akan dienkripsi)<br>
                            @if($config->auth_code)
                                <span class="text-success"><i class="fa fa-check"></i> Auth code sudah tersimpan</span>
                            @else
                                <span class="text-muted">Belum ada auth code tersimpan</span>
                            @endif
                        </small>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label">Base URL <span class="text-danger">*</span></label>
                        <input type="url" 
                               name="base_url" 
                               class="form-control @error('base_url') is-invalid @enderror" 
                               value="{{ old('base_url', $config->base_url) }}"
                               required>
                        @error('base_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   class="form-check-input" 
                                   id="is_active"
                                   {{ old('is_active', $config->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktifkan konfigurasi ini
                            </label>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update
                        </button>
                        <a href="{{ route('payment-gateway.index') }}" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Informasi</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td>Dibuat:</td>
                        <td>{{ $config->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Diupdate:</td>
                        <td>{{ $config->updated_at->format('d M Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Status:</td>
                        <td>
                            @if($config->is_active)
                                <span class="badge badge-success">Aktif</span>
                            @else
                                <span class="badge badge-secondary">Nonaktif</span>
                            @endif
                        </td>
                    </tr>
                </table>

                <hr>

                <h6>SNAP API Status:</h6>
                <ul class="small list-unstyled">
                    <li>
                        @if($config->private_key)
                            <i class="fa fa-check text-success"></i> Private Key
                        @else
                            <i class="fa fa-times text-muted"></i> Private Key
                        @endif
                    </li>
                    <li>
                        @if($config->public_key)
                            <i class="fa fa-check text-success"></i> Public Key
                        @else
                            <i class="fa fa-times text-muted"></i> Public Key
                        @endif
                    </li>
                    <li>
                        @if($config->doku_public_key)
                            <i class="fa fa-check text-success"></i> DOKU Public Key
                        @else
                            <i class="fa fa-times text-muted"></i> DOKU Public Key
                        @endif
                    </li>
                    <li>
                        @if($config->issuer)
                            <i class="fa fa-check text-success"></i> Issuer
                        @else
                            <i class="fa fa-times text-muted"></i> Issuer
                        @endif
                    </li>
                    <li>
                        @if($config->auth_code)
                            <i class="fa fa-check text-success"></i> Auth Code
                        @else
                            <i class="fa fa-times text-muted"></i> Auth Code
                        @endif
                    </li>
                </ul>

                <hr>

                <div class="alert alert-info small">
                    <i class="fa fa-info-circle"></i> Data sensitif (Secret Key, Private Key, Auth Code) tersimpan terenkripsi di database.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
