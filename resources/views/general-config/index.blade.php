@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Konfigurasi Umum</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <svg viewBox="0 0 24 24" width="24" height="24" stroke="currentColor" stroke-width="2" fill="none" stroke-linecap="round" stroke-linejoin="round" class="me-2"><polyline points="9 11 12 14 22 4"></polyline><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path></svg>
                            <strong>Sukses!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="btn-close"></button>
                        </div>
                    @endif

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#general" role="tab">
                                <i class="fas fa-cog"></i> Umum
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#content" role="tab">
                                <i class="fas fa-file-alt"></i> Konten
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#seo" role="tab">
                                <i class="fas fa-search"></i> SEO
                            </a>
                        </li>
                    </ul>

                    <form action="{{ route('general-config.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="tab-content mt-4">
                            <!-- General Tab -->
                            <div class="tab-pane fade show active" id="general" role="tabpanel">
                                <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama Situs <span class="text-danger">*</span></label>
                                    <input type="text" name="site_name" class="form-control @error('site_name') is-invalid @enderror" 
                                           value="{{ old('site_name', $config['site_name']) }}" required>
                                    @error('site_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Email Kontak</label>
                                    <input type="email" name="contact_email" class="form-control @error('contact_email') is-invalid @enderror" 
                                           value="{{ old('contact_email', $config['contact_email']) }}">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nomor Telepon</label>
                                    <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" 
                                           value="{{ old('contact_phone', $config['contact_phone']) }}">
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">Deskripsi Situs</label>
                                    <textarea name="site_description" class="form-control @error('site_description') is-invalid @enderror" 
                                              rows="3">{{ old('site_description', $config['site_description']) }}</textarea>
                                    @error('site_description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Logo Icon/Collapsed</label>
                                    @if(isset($config['logo_icon']) && $config['logo_icon'])
                                        <div class="mb-2 p-3" style="background: #1a1a1a; border-radius: 8px;">
                                            <img src="{{ asset('storage/' . $config['logo_icon']) }}" alt="Logo Icon" style="max-height: 60px;">
                                        </div>
                                    @endif
                                    <input type="file" name="logo_icon" class="form-control @error('logo_icon') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted">Logo kecil untuk sidebar collapsed. Format: JPG, PNG. Maksimal 1MB. Rekomendasi: 40x40px</small>
                                    @error('logo_icon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Logo Dark Theme</label>
                                    @if(isset($config['logo_dark']) && $config['logo_dark'])
                                        <div class="mb-2 p-3" style="background: #1a1a1a; border-radius: 8px;">
                                            <img src="{{ asset('storage/' . $config['logo_dark']) }}" alt="Logo Dark" style="max-height: 60px;">
                                        </div>
                                    @endif
                                    <input type="file" name="logo_dark" class="form-control @error('logo_dark') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted">Logo untuk tema gelap & dashboard. Format: JPG, PNG. Maksimal 2MB</small>
                                    @error('logo_dark')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">Logo Light Theme</label>
                                    @if(isset($config['logo_light']) && $config['logo_light'])
                                        <div class="mb-2 p-3" style="background: #f8f9fa; border-radius: 8px;">
                                            <img src="{{ asset('storage/' . $config['logo_light']) }}" alt="Logo Light" style="max-height: 60px;">
                                        </div>
                                    @endif
                                    <input type="file" name="logo_light" class="form-control @error('logo_light') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted">Logo untuk tema terang. Format: JPG, PNG. Maksimal 2MB</small>
                                    @error('logo_light')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Favicon</label>
                                    @if(isset($config['favicon']) && $config['favicon'])
                                        <div class="mb-2">
                                            <img src="{{ asset('storage/' . $config['favicon']) }}" alt="Favicon" style="max-height: 50px;">
                                        </div>
                                    @endif
                                    <input type="file" name="favicon" class="form-control @error('favicon') is-invalid @enderror" accept="image/*">
                                    <small class="text-muted">Format: ICO, PNG. Maksimal 512KB</small>
                                    @error('favicon')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                                </div>
                            </div>

                            <!-- Content Tab -->
                            <div class="tab-pane fade" id="content" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Judul Hero</label>
                                            <input type="text" name="hero_title" class="form-control @error('hero_title') is-invalid @enderror" 
                                                   value="{{ old('hero_title', $config['hero_title']) }}">
                                            @error('hero_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Highlight Hero</label>
                                            <input type="text" name="hero_highlight" class="form-control @error('hero_highlight') is-invalid @enderror" 
                                                   value="{{ old('hero_highlight', $config['hero_highlight']) }}">
                                            @error('hero_highlight')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Subtitle Hero</label>
                                            <input type="text" name="hero_subtitle" class="form-control @error('hero_subtitle') is-invalid @enderror" 
                                                   value="{{ old('hero_subtitle', $config['hero_subtitle']) }}">
                                            @error('hero_subtitle')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Subtitle About</label>
                                            <input type="text" name="about_subtitle" class="form-control @error('about_subtitle') is-invalid @enderror" 
                                                   value="{{ old('about_subtitle', $config['about_subtitle']) }}">
                                            @error('about_subtitle')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Judul About</label>
                                            <input type="text" name="about_title" class="form-control @error('about_title') is-invalid @enderror" 
                                                   value="{{ old('about_title', $config['about_title']) }}">
                                            @error('about_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Deskripsi About</label>
                                            <textarea name="about_description" class="form-control @error('about_description') is-invalid @enderror" 
                                                      rows="3">{{ old('about_description', $config['about_description']) }}</textarea>
                                            @error('about_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- SEO Tab -->
                            <div class="tab-pane fade" id="seo" role="tabpanel">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Meta Title</label>
                                            <input type="text" name="meta_title" class="form-control @error('meta_title') is-invalid @enderror" 
                                                   value="{{ old('meta_title', $config['meta_title']) }}" maxlength="255">
                                            <small class="text-muted">Judul yang muncul di hasil pencarian Google (max 60 karakter)</small>
                                            @error('meta_title')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Meta Description</label>
                                            <textarea name="meta_description" class="form-control @error('meta_description') is-invalid @enderror" 
                                                      rows="3" maxlength="500">{{ old('meta_description', $config['meta_description']) }}</textarea>
                                            <small class="text-muted">Deskripsi yang muncul di hasil pencarian Google (max 160 karakter)</small>
                                            @error('meta_description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Meta Keywords</label>
                                            <input type="text" name="meta_keywords" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                                   value="{{ old('meta_keywords', $config['meta_keywords']) }}">
                                            <small class="text-muted">Kata kunci dipisahkan dengan koma (contoh: undangan digital, wedding invitation)</small>
                                            @error('meta_keywords')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Open Graph Image</label>
                                            @if(isset($config['og_image']) && $config['og_image'])
                                                <div class="mb-2">
                                                    <img src="{{ asset('storage/' . $config['og_image']) }}" alt="OG Image" style="max-height: 150px;">
                                                </div>
                                            @endif
                                            <input type="file" name="og_image" class="form-control @error('og_image') is-invalid @enderror" accept="image/*">
                                            <small class="text-muted">Gambar yang muncul saat link dibagikan di social media (1200x630px recommended)</small>
                                            @error('og_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Google Analytics ID</label>
                                            <input type="text" name="google_analytics_id" class="form-control @error('google_analytics_id') is-invalid @enderror" 
                                                   value="{{ old('google_analytics_id', $config['google_analytics_id']) }}" placeholder="G-XXXXXXXXXX">
                                            <small class="text-muted">Google Analytics Measurement ID</small>
                                            @error('google_analytics_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">Google Site Verification</label>
                                            <input type="text" name="google_site_verification" class="form-control @error('google_site_verification') is-invalid @enderror" 
                                                   value="{{ old('google_site_verification', $config['google_site_verification']) }}">
                                            <small class="text-muted">Google Search Console verification code</small>
                                            @error('google_site_verification')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
