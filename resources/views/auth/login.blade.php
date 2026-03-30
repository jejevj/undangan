<!DOCTYPE html>
<html lang="id" class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @php
        $siteName = \App\Models\GeneralConfig::get('site_name', config('app.name'));
    @endphp
    <title>Login & Registrasi - {{ $siteName }}</title>
    
    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    
    @if(\App\Models\GeneralConfig::get('favicon'))
    <link rel="icon" href="{{ asset('storage/' . \App\Models\GeneralConfig::get('favicon')) }}">
    @else
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    @endif
    
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            overflow: hidden;
        }
        
        .auth-container {
            display: flex;
            height: 100vh;
            width: 100%;
        }
        
        /* Left Side - Illustration */
        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #2cc392 0%, #1a9d6f 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px;
            position: relative;
            overflow: hidden;
        }
        
        .auth-left::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: moveBackground 20s linear infinite;
        }
        
        @keyframes moveBackground {
            0% { transform: translate(0, 0); }
            100% { transform: translate(50px, 50px); }
        }
        
        .auth-logo-section {
            position: relative;
            z-index: 2;
            text-align: center;
            margin-bottom: 40px;
        }
        
        .auth-logo-section a {
            display: inline-block;
            text-decoration: none;
            transition: transform 0.3s;
        }
        
        .auth-logo-section a:hover {
            transform: scale(1.05);
        }
        
        .auth-logo-section img {
            max-width: 200px;
            height: auto;
            margin-bottom: 20px;
            filter: drop-shadow(0 10px 30px rgba(0,0,0,0.2));
        }
        
        .auth-logo-section h1 {
            color: white;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .auth-logo-section p {
            color: rgba(255,255,255,0.9);
            font-size: 16px;
            line-height: 1.6;
        }
        
        .auth-illustration {
            position: relative;
            z-index: 2;
            max-width: 500px;
            width: 100%;
        }
        
        .auth-illustration svg {
            width: 100%;
            height: auto;
            filter: drop-shadow(0 20px 40px rgba(0,0,0,0.15));
        }
        
        /* Right Side - Form */
        .auth-right {
            flex: 1;
            background: linear-gradient(135deg, #2cc392 0%, #1a9d6f 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 40px;
            overflow-y: auto;
            min-height: 100vh;
        }
        
        .auth-form-container {
            width: 100%;
            max-width: 480px;
            margin: auto;
        }
        
        .auth-form-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }
        
        .auth-tabs {
            display: flex;
            margin-bottom: 40px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .auth-tab {
            flex: 1;
            padding: 15px;
            text-align: center;
            color: #999;
            cursor: pointer;
            transition: all 0.3s;
            border-bottom: 3px solid transparent;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
        }
        
        .auth-tab:hover {
            color: #2cc392;
        }
        
        .auth-tab.active {
            color: #2cc392;
            border-bottom-color: #2cc392;
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
        
        .form-title {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin-bottom: 10px;
        }
        
        .form-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #2cc392;
            box-shadow: 0 0 0 3px rgba(44, 195, 146, 0.1);
        }
        
        .password-requirements {
            font-size: 12px;
            color: #999;
            margin-top: 5px;
        }
        
        .btn-auth {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #2cc392 0%, #1a9d6f 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(44, 195, 146, 0.3);
        }
        
        .form-footer {
            text-align: center;
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }
        
        .form-footer a {
            color: #2cc392;
            text-decoration: none;
            font-weight: 600;
        }
        
        .form-footer a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .alert-success {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
        }
        
        /* Campaign Alert */
        .campaign-alert {
            background: linear-gradient(135deg, #2cc392 0%, #1a9d6f 100%);
            color: white;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .campaign-alert strong {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
        }
        
        .campaign-alert small {
            display: block;
            margin-top: 8px;
            opacity: 0.95;
        }
        
        /* Campaign Warning */
        .campaign-warning {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
            color: #333;
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.6;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }
        
        .campaign-warning strong {
            display: block;
            margin-bottom: 5px;
            font-size: 16px;
            color: #333;
        }
        
        /* Mobile Logo */
        .mobile-logo {
            display: none;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .auth-left {
                display: none;
            }
            
            .auth-right {
                flex: 1;
                padding: 20px;
            }
            
            /* Mobile Logo */
            .mobile-logo {
                display: block !important;
                text-align: center;
                margin-top: 20px;
                margin-bottom: 30px;
            }
            
            .mobile-logo a {
                display: inline-block;
                text-decoration: none;
                transition: transform 0.3s;
            }
            
            .mobile-logo a:hover {
                transform: scale(1.05);
            }
            
            .mobile-logo img {
                max-width: 150px;
                height: auto;
                display: block;
                margin: 0 auto;
            }
            
            .mobile-logo h1 {
                color: white;
                font-size: 28px;
                font-weight: 700;
                margin: 0;
            }
        }
        
        @media (max-width: 576px) {
            .auth-right {
                padding: 15px;
            }
            
            .auth-form-container {
                max-width: 100%;
            }
            
            .auth-form-card {
                padding: 30px 20px;
                border-radius: 12px;
            }
            
            .form-title {
                font-size: 22px;
            }
            
            .mobile-logo {
                margin-top: 15px;
                margin-bottom: 25px;
            }
            
            .mobile-logo img {
                max-width: 120px;
            }
            
            .mobile-logo h1 {
                font-size: 24px;
            }
            
            .campaign-alert {
                font-size: 13px;
            }
            
            .campaign-alert strong {
                font-size: 15px;
            }
            
            .campaign-warning {
                font-size: 13px;
            }
            
            .campaign-warning strong {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        {{-- Left Side - Illustration --}}
        <div class="auth-left">
            <div class="auth-logo-section">
                <a href="{{ url('/') }}">
                    @if(\App\Models\GeneralConfig::get('logo_light'))
                    <img src="{{ asset('storage/' . \App\Models\GeneralConfig::get('logo_light')) }}" alt="{{ $siteName }}">
                    @else
                    <h1>{{ $siteName }}</h1>
                    @endif
                </a>
                <p>Buat undangan digital yang indah dan mudah dibagikan</p>
            </div>
            
            <div class="auth-illustration">
                {{-- Wedding Invitation Illustration SVG --}}
                <svg viewBox="0 0 500 400" xmlns="http://www.w3.org/2000/svg">
                    {{-- Envelope --}}
                    <rect x="100" y="150" width="300" height="200" rx="10" fill="#fff" opacity="0.9"/>
                    <path d="M100 150 L250 250 L400 150" stroke="#1a9d6f" stroke-width="3" fill="none"/>
                    <path d="M100 150 L100 350 L250 250 Z" fill="#2cc392" opacity="0.3"/>
                    <path d="M400 150 L400 350 L250 250 Z" fill="#1a9d6f" opacity="0.3"/>
                    
                    {{-- Hearts --}}
                    <path d="M230 200 C230 190, 240 185, 250 195 C260 185, 270 190, 270 200 C270 215, 250 230, 250 230 C250 230, 230 215, 230 200" fill="#ff6b9d"/>
                    
                    {{-- Decorative elements --}}
                    <circle cx="150" cy="100" r="8" fill="#ffd700" opacity="0.6"/>
                    <circle cx="350" cy="120" r="6" fill="#ffd700" opacity="0.6"/>
                    <circle cx="180" cy="80" r="5" fill="#fff" opacity="0.8"/>
                    <circle cx="320" cy="90" r="7" fill="#fff" opacity="0.8"/>
                    
                    {{-- Rings --}}
                    <circle cx="200" cy="320" r="15" fill="none" stroke="#ffd700" stroke-width="3"/>
                    <circle cx="220" cy="320" r="15" fill="none" stroke="#ffd700" stroke-width="3"/>
                    
                    {{-- Flowers --}}
                    <circle cx="120" cy="180" r="8" fill="#ff6b9d" opacity="0.7"/>
                    <circle cx="115" cy="175" r="5" fill="#ff6b9d" opacity="0.5"/>
                    <circle cx="125" cy="175" r="5" fill="#ff6b9d" opacity="0.5"/>
                    
                    <circle cx="380" cy="200" r="8" fill="#ff6b9d" opacity="0.7"/>
                    <circle cx="375" cy="195" r="5" fill="#ff6b9d" opacity="0.5"/>
                    <circle cx="385" cy="195" r="5" fill="#ff6b9d" opacity="0.5"/>
                </svg>
            </div>
        </div>
        
        {{-- Right Side - Form --}}
        <div class="auth-right">
            <div class="auth-form-container">
                {{-- Mobile Logo (only visible on mobile) --}}
                <div class="mobile-logo">
                    <a href="{{ url('/') }}">
                        @if(\App\Models\GeneralConfig::get('logo_light'))
                        <img src="{{ asset('storage/' . \App\Models\GeneralConfig::get('logo_light')) }}" alt="{{ $siteName }}">
                        @elseif(\App\Models\GeneralConfig::get('logo_dark'))
                        <img src="{{ asset('storage/' . \App\Models\GeneralConfig::get('logo_dark')) }}" alt="{{ $siteName }}">
                        @else
                        <h1>{{ $siteName }}</h1>
                        @endif
                    </a>
                </div>
                
                {{-- Form Card --}}
                <div class="auth-form-card">
                    {{-- Tabs --}}
                    <div class="auth-tabs">
                        <a href="#" class="auth-tab {{ request()->routeIs('login') || !request()->routeIs('register') ? 'active' : '' }}" data-tab="login">
                            Masuk
                        </a>
                        <a href="#" class="auth-tab {{ request()->routeIs('register') ? 'active' : '' }}" data-tab="register">
                            Daftar
                        </a>
                    </div>
                    
                    {{-- Error Messages --}}
                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                    
                    @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}</div>
                    @endif
                    
                    @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    
                    {{-- Login Tab --}}
                    <div id="login-tab" class="tab-content {{ request()->routeIs('login') || !request()->routeIs('register') ? 'active' : '' }}">
                    <h2 class="form-title">Masuk</h2>
                    <p class="form-subtitle">Masuk ke akun Anda untuk melanjutkan</p>
                    
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-auth">Masuk</button>
                        </div>
                    </form>
                    
                    <div class="form-footer">
                        Belum punya akun? <a href="#" data-tab="register">Daftar sekarang</a>
                    </div>
                </div>
                
                {{-- Register Tab --}}
                <div id="register-tab" class="tab-content {{ request()->routeIs('register') ? 'active' : '' }}">
                    <h2 class="form-title">Buat Akun Baru</h2>
                    <p class="form-subtitle">Daftar untuk mulai membuat undangan digital</p>
                    
                    @if(isset($campaign) && $campaign)
                    <div class="campaign-alert">
                        <strong>🎉 Selamat!</strong>
                        Anda mendapatkan akses <strong>{{ $campaign->pricingPlan->name }}</strong> gratis dari kampanye <strong>{{ $campaign->name }}</strong>
                        @if($campaign->max_users > 0)
                        <small>Sisa kuota: {{ $campaign->getRemainingSlots() }} dari {{ $campaign->max_users }}</small>
                        @endif
                    </div>
                    @elseif(isset($campaignWarning) && $campaignWarning)
                    <div class="campaign-warning">
                        <strong>⚠️ Perhatian</strong>
                        {{ $campaignWarning }}
                    </div>
                    @endif
                    
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        
                        {{-- Hidden input for campaign code --}}
                        @if(isset($campaign) && $campaign)
                        <input type="hidden" name="campaign_code" value="{{ $campaign->code }}">
                        @endif
                        
                        <div class="form-group">
                            <label>Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Nama lengkap Anda" required autofocus>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="nama@email.com" required>
                        </div>
                        <div class="form-group">
                            <label>Password</label>
                            <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required>
                            <small class="password-requirements">Minimal 8 karakter</small>
                        </div>
                        <div class="form-group">
                            <label>Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password" required>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn-auth">Daftar Sekarang</button>
                        </div>
                    </form>
                    
                    <div class="form-footer">
                        Sudah punya akun? <a href="#" data-tab="login">Masuk di sini</a>
                    </div>
                </div>
                {{-- End Form Card --}}
            </div>
        </div>
    </div>
    
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
                    
                    // Preserve URL parameters (like campaign ref)
                    const currentParams = window.location.search;
                    const loginUrl = '{{ route("login") }}'.replace(/^http:/, window.location.protocol);
                    const registerUrl = '{{ route("register") }}'.replace(/^http:/, window.location.protocol);
                    const newUrl = (targetTab === 'login' ? loginUrl : registerUrl) + currentParams;
                    window.history.pushState({}, '', newUrl);
                });
            });
            
            // Handle link clicks in form footer
            document.querySelectorAll('.form-footer a[data-tab]').forEach(link => {
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
