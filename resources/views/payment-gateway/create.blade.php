@extends('layouts.app')

@section('title', 'Tambah Konfigurasi Payment Gateway')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('payment-gateway.index') }}">Payment Gateway</a></li>
    <li class="breadcrumb-item active">Tambah Konfigurasi</li>
@endsection

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tambah Konfigurasi Payment Gateway</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('payment-gateway.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Provider <span class="text-danger">*</span></label>
                        <select name="provider" class="form-control @error('provider') is-invalid @enderror" required>
                            <option value="doku" selected>DOKU</option>
                        </select>
                        @error('provider')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Payment gateway provider</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Environment <span class="text-danger">*</span></label>
                        <select name="environment" class="form-control @error('environment') is-invalid @enderror" required>
                            <option value="sandbox" selected>Sandbox (Testing)</option>
                            <option value="production">Production (Live)</option>
                        </select>
                        @error('environment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Gunakan Sandbox untuk testing, Production untuk live</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Client ID <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="client_id" 
                               class="form-control @error('client_id') is-invalid @enderror" 
                               value="{{ old('client_id') }}"
                               placeholder="Masukkan Client ID dari DOKU"
                               required>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Dapatkan dari DOKU Back Office</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                        <input type="password" 
                               name="secret_key" 
                               class="form-control @error('secret_key') is-invalid @enderror" 
                               placeholder="Masukkan Secret Key dari DOKU"
                               required>
                        @error('secret_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Secret key akan dienkripsi secara otomatis</small>
                    </div>

                    <hr class="my-4">
                    <h5 class="mb-3">SNAP API Configuration (Optional)</h5>
                    <p class="text-muted small">Field berikut diperlukan untuk menggunakan SNAP API DOKU</p>

                    <div class="mb-3">
                        <label class="form-label">Private Key</label>
                        <textarea name="private_key" 
                                  rows="4"
                                  class="form-control @error('private_key') is-invalid @enderror" 
                                  placeholder="-----BEGIN PRIVATE KEY-----&#10;...&#10;-----END PRIVATE KEY-----">{{ old('private_key') }}</textarea>
                        @error('private_key')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Private key untuk signature (akan dienkripsi)</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Public Key</label>
                        <textarea name="public_key" 
                                  rows="4"
                                  class="form-control @error('public_key') is-invalid @enderror" 
                                  placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----">{{ old('public_key') }}</textarea>
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
                                  placeholder="-----BEGIN PUBLIC KEY-----&#10;...&#10;-----END PUBLIC KEY-----">{{ old('doku_public_key') }}</textarea>
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
                               value="{{ old('issuer') }}"
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
                               placeholder="Masukkan Auth Code jika diperlukan">
                        @error('auth_code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Auth code untuk payment tertentu (akan dienkripsi)</small>
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label class="form-label">Base URL <span class="text-danger">*</span></label>
                        <input type="url" 
                               name="base_url" 
                               class="form-control @error('base_url') is-invalid @enderror" 
                               value="{{ old('base_url', 'https://api-sandbox.doku.com') }}"
                               placeholder="https://api-sandbox.doku.com"
                               required>
                        @error('base_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">
                            Sandbox: https://api-sandbox.doku.com<br>
                            Production: https://api.doku.com
                        </small>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" 
                                   name="is_active" 
                                   value="1" 
                                   class="form-check-input" 
                                   id="is_active"
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Aktifkan konfigurasi ini
                            </label>
                        </div>
                        <small class="form-text text-muted">Hanya satu konfigurasi yang bisa aktif per provider</small>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Simpan
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
                <h5 class="card-title">Panduan</h5>
            </div>
            <div class="card-body">
                <h6>Cara Mendapatkan Credentials:</h6>
                <ol class="small">
                    <li>Login ke <a href="https://jokul.doku.com/" target="_blank">DOKU Jokul</a></li>
                    <li>Buka menu "Settings" atau "API Credentials"</li>
                    <li>Copy Client ID dan Secret Key</li>
                    <li>Pilih environment yang sesuai</li>
                </ol>

                <hr>

                <h6>SNAP API Keys (Optional):</h6>
                <p class="small">Untuk menggunakan SNAP API, Anda perlu:</p>
                <ul class="small">
                    <li><strong>Private Key:</strong> Generate RSA key pair Anda sendiri</li>
                    <li><strong>Public Key:</strong> Upload ke DOKU Dashboard</li>
                    <li><strong>DOKU Public Key:</strong> Download dari DOKU Dashboard</li>
                    <li><strong>Issuer:</strong> Identifier merchant Anda</li>
                    <li><strong>Auth Code:</strong> Untuk payment tertentu</li>
                </ul>

                <hr>

                <h6>Environment:</h6>
                <ul class="small">
                    <li><strong>Sandbox:</strong> Untuk testing, tidak ada transaksi real</li>
                    <li><strong>Production:</strong> Untuk live, transaksi real</li>
                </ul>

                <hr>

                <h6>Base URL:</h6>
                <ul class="small">
                    <li><strong>Sandbox:</strong><br><code>https://api-sandbox.doku.com</code></li>
                    <li><strong>Production:</strong><br><code>https://api.doku.com</code></li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
