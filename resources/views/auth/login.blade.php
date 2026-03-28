<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $siteName = \App\Models\GeneralConfig::get('site_name', config('app.name'));
    @endphp
    <title>Login & Registrasi - {{ $siteName }}</title>
    
    @if(\App\Models\GeneralConfig::get('favicon'))
    <link rel="icon" href="{{ asset('storage/' . \App\Models\GeneralConfig::get('favicon')) }}">
    @else
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    @endif
    
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        .auth-tabs {
            display: flex;
            margin-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
        
        .auth-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            font-weight: 500;
            text-decoration: none;
        }
        
        .auth-tab:hover {
            color: rgba(255,255,255,0.9);
        }
        
        .auth-tab.active {
            color: #fff;
            border-bottom-color: #fff;
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
            animation: fadeIn 0.3s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .auth-logo {
            max-width: 200px;
            height: auto;
            margin-bottom: 20px;
        }
        
        .auth-description {
            color: rgba(255,255,255,0.7);
            font-size: 14px;
            margin-bottom: 30px;
        }
        
        .form-control:focus {
            border-color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(255,255,255,0.25);
        }
        
        .btn-auth {
            background: #fff;
            color: #6418C3;
            font-weight: 600;
            padding: 12px;
            border: none;
            transition: all 0.3s;
        }
        
        .btn-auth:hover {
            background: rgba(255,255,255,0.9);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }
        
        .back-to-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #fff;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .back-to-home:hover {
            color: rgba(255,255,255,0.8);
            transform: translateX(-5px);
        }
        
        .password-requirements {
            font-size: 12px;
            color: rgba(255,255,255,0.6);
            margin-top: 5px;
        }
    </style>
</head>
<body class="h-100">
    <a href="{{ route('landing') }}" class="back-to-home">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Kembali ke Beranda
    </a>
    
    <div class="authincation h-100">
        <div class="container h-100">
            <div class="row justify-content-center h-100 align-items-center">
                <div class="col-md-6 col-lg-5">
                    <div class="authincation-content">
                        <div class="row no-gutters">
                            <div class="col-xl-12">
                                <div class="auth-form">
                                    <div class="text-center mb-4">
                                        @php
                                            $logoDark = \App\Models\GeneralConfig::get('logo_dark');
                                        @endphp
                                        @if($logoDark)
                                        <img src="{{ asset('storage/' . $logoDark) }}" alt="{{ $siteName }}" class="auth-logo">
                                        @else
                                        <img src="{{ asset('assets/images/logo-full.png') }}" alt="{{ $siteName }}" class="auth-logo">
                                        @endif
                                        
                                        <p class="auth-description">
                                            {{ \App\Models\GeneralConfig::get('site_description', 'Platform undangan online terlengkap untuk acara spesial Anda') }}
                                        </p>
                                    </div>

                                    {{-- Tabs --}}
                                    <div class="auth-tabs">
                                        <a href="#" class="auth-tab {{ request()->routeIs('login') ? 'active' : '' }}" data-tab="login">
                                            Masuk
                                        </a>
                                        <a href="#" class="auth-tab {{ request()->routeIs('register') ? 'active' : '' }}" data-tab="register">
                                            Daftar
                                        </a>
                                    </div>

                                    @if($errors->any())
                                        <div class="alert alert-danger">
                                            <ul class="mb-0">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    @if(session('success'))
                                        <div class="alert alert-success">
                                            {{ session('success') }}
                                        </div>
                                    @endif

                                    {{-- Login Form --}}
                                    <div id="login-tab" class="tab-content {{ request()->routeIs('login') || !request()->routeIs('register') ? 'active' : '' }}">
                                        <h4 class="text-center mb-4 text-white">Masuk ke Akun Anda</h4>
                                        
                                        <form method="POST" action="{{ route('login') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label class="mb-1 text-white"><strong>Email</strong></label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                                            </div>
                                            <div class="form-group">
                                                <label class="mb-1 text-white"><strong>Password</strong></label>
                                                <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                                            </div>
                                            <div class="row d-flex justify-content-between mt-4 mb-2">
                                                <div class="form-group">
                                                    <div class="form-check custom-checkbox ms-1 text-white">
                                                        <input type="checkbox" name="remember" class="form-check-input" id="remember">
                                                        <label class="form-check-label" for="remember">Ingat saya</label>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-auth btn-block">Masuk</button>
                                            </div>
                                        </form>
                                        
                                        <div class="text-center mt-3">
                                            <p class="text-white">Belum punya akun? <a href="#" class="text-white" style="text-decoration: underline;" data-tab="register">Daftar sekarang</a></p>
                                        </div>
                                    </div>

                                    {{-- Register Form --}}
                                    <div id="register-tab" class="tab-content {{ request()->routeIs('register') ? 'active' : '' }}">
                                        <h4 class="text-center mb-4 text-white">Buat Akun Baru</h4>
                                        
                                        <form method="POST" action="{{ route('register') }}">
                                            @csrf
                                            <div class="form-group">
                                                <label class="mb-1 text-white"><strong>Nama Lengkap</strong></label>
                                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autofocus>
                                            </div>
                                            <div class="form-group">
                                                <label class="mb-1 text-white"><strong>Email</strong></label>
                                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required>
                                            </div>
                                            <div class="form-group">
                                                <label class="mb-1 text-white"><strong>Password</strong></label>
                                                <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                                                <small class="password-requirements">Minimal 8 karakter</small>
                                            </div>
                                            <div class="form-group">
                                                <label class="mb-1 text-white"><strong>Konfirmasi Password</strong></label>
                                                <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                                            </div>
                                            <div class="text-center mt-4">
                                                <button type="submit" class="btn btn-auth btn-block">Daftar Sekarang</button>
                                            </div>
                                        </form>
                                        
                                        <div class="text-center mt-3">
                                            <p class="text-white">Sudah punya akun? <a href="#" class="text-white" style="text-decoration: underline;" data-tab="login">Masuk di sini</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.auth-tab');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabs.forEach(tab => {
                tab.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    const targetTab = this.getAttribute('data-tab');
                    
                    // Remove active class from all tabs and contents
                    tabs.forEach(t => t.classList.remove('active'));
                    tabContents.forEach(c => c.classList.remove('active'));
                    
                    // Add active class to clicked tab and corresponding content
                    this.classList.add('active');
                    document.getElementById(targetTab + '-tab').classList.add('active');
                    
                    // Update URL without reload
                    const newUrl = targetTab === 'login' ? '{{ route("login") }}' : '{{ route("register") }}';
                    window.history.pushState({}, '', newUrl);
                });
            });
            
            // Handle links within forms
            document.querySelectorAll('a[data-tab]').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetTab = this.getAttribute('data-tab');
                    const tabElement = document.querySelector(`.auth-tab[data-tab="${targetTab}"]`);
                    if (tabElement) {
                        tabElement.click();
                    }
                });
            });
        });
    </script>
</body>
</html>
