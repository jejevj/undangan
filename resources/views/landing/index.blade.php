<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  @php
    $siteName = \App\Models\GeneralConfig::get('site_name', config('app.name'));
    $metaTitle = \App\Models\GeneralConfig::get('meta_title');
    $pageTitle = $metaTitle ?: $siteName . ' - Undangan Digital';
  @endphp
  <title>{{ $pageTitle }}</title>
  <meta name="description" content="{{ \App\Models\GeneralConfig::get('meta_description', 'Platform undangan online terlengkap dengan template elegan, fitur musik, galeri foto, dan amplop digital') }}">
  <meta name="keywords" content="{{ \App\Models\GeneralConfig::get('meta_keywords', 'undangan digital, undangan online, wedding invitation, undangan pernikahan') }}">
  <meta name="author" content="{{ \App\Models\GeneralConfig::get('site_name', config('app.name')) }}">
  
  {{-- Upgrade insecure requests to HTTPS (temporary fix for mixed content) --}}
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
  
  @if(\App\Models\GeneralConfig::get('google_site_verification'))
  <meta name="google-site-verification" content="{{ \App\Models\GeneralConfig::get('google_site_verification') }}">
  @endif
  
  <!-- Open Graph / Facebook -->
  <meta property="og:type" content="website">
  <meta property="og:url" content="{{ url('/') }}">
  <meta property="og:title" content="{{ $pageTitle }}">
  <meta property="og:description" content="{{ \App\Models\GeneralConfig::get('meta_description', 'Platform undangan online terlengkap') }}">
  @if(\App\Models\GeneralConfig::get('og_image'))
  <meta property="og:image" content="{{ asset('storage/' . \App\Models\GeneralConfig::get('og_image')) }}">
  @endif
  
  <!-- Twitter -->
  <meta property="twitter:card" content="summary_large_image">
  <meta property="twitter:url" content="{{ url('/') }}">
  <meta property="twitter:title" content="{{ $pageTitle }}">
  <meta property="twitter:description" content="{{ \App\Models\GeneralConfig::get('meta_description', 'Platform undangan online terlengkap') }}">
  @if(\App\Models\GeneralConfig::get('og_image'))
  <meta property="twitter:image" content="{{ asset('storage/' . \App\Models\GeneralConfig::get('og_image')) }}">
  @endif
  
  @if(\App\Models\GeneralConfig::get('favicon'))
  <link rel="icon" href="{{ asset('storage/' . \App\Models\GeneralConfig::get('favicon')) }}">
  @else
  <link rel="icon" href="{{ asset('demos-assets/img/favicon.png') }}">
  @endif
  
  <!-- Google Analytics -->
  @if(\App\Models\GeneralConfig::get('google_analytics_id'))
  <script async src="https://www.googletagmanager.com/gtag/js?id={{ \App\Models\GeneralConfig::get('google_analytics_id') }}"></script>
  <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '{{ \App\Models\GeneralConfig::get('google_analytics_id') }}');
  </script>
  @endif
  
  <!-- CSS -->
   <link rel="stylesheet" type="text/css" href="{{ asset('demos-assets/css/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('demos-assets/css/owl.carousel.min.css') }}">
   <link rel="stylesheet" href="{{ asset('demos-assets/css/owl.theme.default.min.css') }}">
   <link rel="stylesheet" href="{{ asset('demos-assets/css/jquery.fancybox.min.css') }}">
   <link rel="stylesheet" href="{{ asset('demos-assets/css/fontawesome.min.css') }}">
   <link rel="stylesheet" href="{{ asset('demos-assets/css/style.css') }}">
   <link rel="stylesheet" href="{{ asset('demos-assets/css/responsive.css') }}">
   <link rel="stylesheet" href="{{ asset('demos-assets/css/color.css') }}">
   <style>
     .theme-toggle {
       cursor: pointer;
       padding: 8px 15px;
       border-radius: 5px;
       background: rgba(255,255,255,0.1);
       transition: all 0.3s;
       display: inline-flex;
       align-items: center;
       gap: 8px;
     }
     .theme-toggle:hover {
       background: rgba(255,255,255,0.2);
     }
     .theme-toggle i {
       font-size: 18px;
     }
     
     /* Pricing detail box styling for dark/light theme */
     .pricing-detail-box {
       background: rgba(255,255,255,0.05);
       border-radius: 8px;
       padding: 15px;
       margin-bottom: 20px;
     }
     
     body.light .pricing-detail-box {
       background: rgba(0,0,0,0.05);
     }
     
     .pricing-detail-row {
       display: flex;
       justify-content: space-between;
       margin-bottom: 8px;
       font-size: 14px;
     }
     
     .pricing-detail-row:last-child {
       margin-bottom: 0;
     }
     
     .pricing-detail-label {
       opacity: 0.8;
       color: inherit;
     }
     
     body.dark .pricing-detail-label {
       color: rgba(255,255,255,0.8);
     }
     
     body.light .pricing-detail-label {
       color: rgba(0,0,0,0.7);
     }
     
     .pricing-detail-value {
       font-weight: 600;
       color: inherit;
     }
     
     body.dark .pricing-detail-value {
       color: #fff;
     }
     
     body.light .pricing-detail-value {
       color: #000;
     }
     
     .pricing-detail-value.text-success {
       color: #28a745 !important;
     }
     
     .pricing-detail-value.text-warning {
       color: #ffc107 !important;
     }
     
     /* Template Gallery Styles */
     .template-categories .nav-pills {
       gap: 10px;
       flex-wrap: wrap;
     }
     
     .template-categories .nav-link {
       border-radius: 25px;
       padding: 10px 25px;
       font-weight: 500;
       transition: all 0.3s;
       border: 2px solid transparent;
     }
     
     body.dark .template-categories .nav-link {
       background: rgba(255,255,255,0.05);
       color: rgba(255,255,255,0.8);
     }
     
     body.light .template-categories .nav-link {
       background: rgba(0,0,0,0.05);
       color: rgba(0,0,0,0.7);
     }
     
     .template-categories .nav-link:hover {
       transform: translateY(-2px);
     }
     
     .template-categories .nav-link.active {
       background: #2cc392;
       color: white;
       border-color: #2cc392;
     }
     
     .template-card {
       border-radius: 12px;
       overflow: hidden;
       transition: all 0.3s;
       height: 100%;
       display: flex;
       flex-direction: column;
     }
     
     body.dark .template-card {
       background: rgba(255,255,255,0.05);
       border: 1px solid rgba(255,255,255,0.1);
     }
     
     body.light .template-card {
       background: white;
       border: 1px solid rgba(0,0,0,0.1);
       box-shadow: 0 2px 8px rgba(0,0,0,0.1);
     }
     
     .template-card:hover {
       transform: translateY(-5px);
       box-shadow: 0 8px 20px rgba(0,0,0,0.15);
     }
     
     .template-thumbnail {
       position: relative;
       overflow: hidden;
       padding-top: 133%; /* 3:4 aspect ratio - lebih pendek */
     }
     
     .template-thumbnail img {
       position: absolute;
       top: 0;
       left: 0;
       width: 100%;
       height: 100%;
       object-fit: cover;
     }
     
     .template-overlay {
       position: absolute;
       top: 0;
       left: 0;
       right: 0;
       bottom: 0;
       background: rgba(0,0,0,0.7);
       display: flex;
       align-items: center;
       justify-content: center;
       opacity: 0;
       transition: opacity 0.3s;
     }
     
     .template-card:hover .template-overlay {
       opacity: 1;
     }
     
     .template-info {
       padding: 12px;
       flex: 1;
       display: flex;
       flex-direction: column;
     }
     
     .template-name {
       margin-bottom: 8px;
       font-size: 13px;
       font-weight: 600;
       line-height: 1.3;
       min-height: 34px;
     }
     
     body.dark .template-name {
       color: white;
     }
     
     body.light .template-name {
       color: #000;
     }
     
     .template-price {
       text-align: center;
       margin-bottom: 10px;
     }
     
     .template-price .badge {
       font-size: 11px;
       padding: 4px 10px;
     }
     
     .template-actions {
       margin-top: auto;
     }
     
     .template-actions .btn {
       font-size: 12px;
       padding: 8px 12px;
       font-weight: 500;
       line-height: 1.4;
       position: relative;
       overflow: hidden;
       display: inline-flex;
       align-items: center;
       justify-content: center;
       gap: 5px;
     }
     
     .template-actions .btn::before {
       content: '';
       position: absolute;
       top: 0;
       left: -100%;
       width: 100%;
       height: 100%;
       background: rgba(255, 255, 255, 0.2);
       transition: left 0.3s ease;
       z-index: 1;
     }
     
     .template-actions .btn:hover::before {
       left: 0;
     }
     
     .template-actions .btn i,
     .template-actions .btn span {
       position: relative;
       z-index: 2;
       display: inline-block;
     }
     
     .template-actions .btn i {
       font-size: 11px;
       line-height: 1;
     }
     
     .template-actions .btn span {
       line-height: 1;
     }
     
     .template-actions .btn:hover {
       transform: translateY(-1px);
     }
     
     /* Responsive adjustments */
     @media (max-width: 576px) {
       .template-name {
         font-size: 11px;
         min-height: 30px;
       }
       
       .template-price .badge {
         font-size: 10px;
         padding: 3px 8px;
       }
       
       .template-actions .btn {
         font-size: 11px;
         padding: 7px 10px;
         gap: 4px;
       }
       
       .template-actions .btn i {
         font-size: 10px;
       }
     }
        
     /* Template Type Filter Styles */
     .template-type-filter {
       margin-bottom: 25px;
     }
     
     .template-type-filter .btn-group {
       box-shadow: 0 2px 8px rgba(0,0,0,0.1);
       border-radius: 25px;
       overflow: hidden;
       display: inline-flex;
     }
     
     .template-type-filter .btn {
       border-radius: 0;
       padding: 10px 30px;
       font-weight: 500;
       transition: all 0.3s;
       border: none;
       text-decoration: none;
     }
     
     body.dark .template-type-filter .btn-outline-primary,
     body.dark .template-type-filter .btn-outline-success,
     body.dark .template-type-filter .btn-outline-warning {
       background: rgba(255,255,255,0.05);
       color: rgba(255,255,255,0.8);
     }
     
     body.light .template-type-filter .btn-outline-primary,
     body.light .template-type-filter .btn-outline-success,
     body.light .template-type-filter .btn-outline-warning {
       background: rgba(0,0,0,0.05);
       color: rgba(0,0,0,0.7);
     }
     
     .template-type-filter .btn-outline-primary.active {
       background: #007bff;
       color: white;
     }
     
     .template-type-filter .btn-outline-success.active {
       background: #28a745;
       color: white;
     }
     
     .template-type-filter .btn-outline-warning.active {
       background: #ffc107;
       color: #000;
     }
     
     .template-type-filter .btn:hover {
       opacity: 0.8;
     }
     
     .template-categories .nav-link {
       text-decoration: none;
     }
     
     /* Loading spinner */
     #template-loading .spinner-border {
       width: 3rem;
       height: 3rem;
     }
     
     .visually-hidden {
       position: absolute;
       width: 1px;
       height: 1px;
       padding: 0;
       margin: -1px;
       overflow: hidden;
       clip: rect(0, 0, 0, 0);
       white-space: nowrap;
       border: 0;
     }
     
     /* Mobile Menu Button - Hidden on Desktop */
     .navbar-toggler {
       display: none;
       background: transparent;
       border: none;
       color: white;
       font-size: 24px;
       cursor: pointer;
       padding: 8px 12px;
       transition: all 0.3s;
     }
     
     .navbar-toggler:hover {
       opacity: 0.8;
     }
     
     .navbar-toggler i {
       transition: all 0.3s;
     }
     
     @media (max-width: 1200px) {
       .navbar-toggler {
         display: block;
       }
     }
     
     /* Partner Section Styles */
     #partners .partner.item {
       display: flex;
       align-items: center;
       justify-content: center;
       min-height: 120px;
       transition: all 0.3s;
     }
     
     #partners .partner.item:hover {
       transform: translateY(-5px);
     }
     
     #partners .partner.item a {
       display: block;
       width: 100%;
       text-decoration: none;
     }
     
     #partners .partner.item .partner-name {
       transition: color 0.3s;
     }
     
     body.dark #partners .partner.item .partner-name {
       color: rgba(255,255,255,0.9);
     }
     
     body.light #partners .partner.item .partner-name {
       color: rgba(0,0,0,0.8);
     }
     
     #partners .partner.item a.partner-text-link:hover .partner-name {
       opacity: 0.7;
     }
     
     /* Business Plan Styling */
     .pricing-plans.business-plan {
       border: 2px solid #17a2b8;
     }
     
     .partnership-logos {
       padding: 15px;
       border-radius: 8px;
       background: rgba(255,255,255,0.03);
     }
     
     body.light .partnership-logos {
       background: rgba(0,0,0,0.03);
     }
   </style>
   <!-- jquery -->
   <script src="{{ asset('demos-assets/js/jquery-3.6.0.min.js') }}"></script>
   <script src="{{ asset('demos-assets/js/preloader.js') }}"></script>
   <script>
     // Theme toggle functionality
     (function() {
       const getPreferredTheme = () => {
         const storedTheme = localStorage.getItem('theme');
         if (storedTheme) {
           return storedTheme;
         }
         // Default to dark theme
         return 'dark';
       };

       const setTheme = (theme) => {
         document.documentElement.setAttribute('data-theme', theme);
         document.body.classList.remove('dark', 'light');
         document.body.classList.add(theme);
         localStorage.setItem('theme', theme);
         updateThemeIcon(theme);
         updateLogo(theme);
       };

       const updateThemeIcon = (theme) => {
         const icon = document.querySelector('.theme-toggle i');
         if (icon) {
           icon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
         }
       };

       const updateLogo = (theme) => {
         const logoDark = document.querySelectorAll('.logo-dark');
         const logoLight = document.querySelectorAll('.logo-light');
         
         if (theme === 'dark') {
           logoDark.forEach(el => el.style.display = 'block');
           logoLight.forEach(el => el.style.display = 'none');
         } else {
           logoDark.forEach(el => el.style.display = 'none');
           logoLight.forEach(el => el.style.display = 'block');
         }
       };

       // Set initial theme before page loads
       const initialTheme = getPreferredTheme();
       document.documentElement.setAttribute('data-theme', initialTheme);
       if (document.body) {
         document.body.classList.add(initialTheme);
       }

       // Listen for system theme changes
       window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
         if (!localStorage.getItem('theme')) {
           setTheme(e.matches ? 'dark' : 'light');
         }
       });

       // Toggle theme on button click
       window.addEventListener('DOMContentLoaded', () => {
         // Set theme again after DOM loads
         setTheme(initialTheme);
         
         const toggleBtn = document.querySelector('.theme-toggle');
         if (toggleBtn) {
           toggleBtn.addEventListener('click', (e) => {
             e.preventDefault();
             const currentTheme = document.body.classList.contains('dark') ? 'dark' : 'light';
             const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
             setTheme(newTheme);
           });
         }
       });
     })();
   </script>
 </head>
<body>
<!-- preloader -->
  <div class="preloader">
    <div>
      <div class="spinner spinner-3"></div>
    </div>
  </div>
<!-- preloader end -->
<!-- header -->
  <header id="stickyHeader" class="" style="background-color: #12a575;">
    <div class="container">
      <div class="top-bar">
        <div class="logo">
          @php
            $logoDark = \App\Models\GeneralConfig::get('logo_dark');
            $logoLight = \App\Models\GeneralConfig::get('logo_light');
          @endphp
          @if($logoDark)
          <img alt="logo" src="{{ asset('storage/' . $logoDark) }}" class="logo-dark">
          @else
          <img alt="logo" src="{{ asset('demos-assets/img/logo.png') }}" class="logo-dark">
          @endif
          @if($logoLight)
          <img alt="logo" src="{{ asset('storage/' . $logoLight) }}" class="logo-light" style="display: none;">
          @else
          <img alt="logo" src="{{ asset('demos-assets/img/logo.png') }}" class="logo-light" style="display: none;">
          @endif
        </div>
        
        {{-- Hamburger Menu Button for Mobile --}}
        <button class="navbar-toggler" type="button">
          <i class="fa-solid fa-bars"></i>
        </button>
        
        <nav>
          <ul>
            <li><a href="#about" class="text-white">Tentang</a></li>
            <li><a href="#features" class="text-white">Fitur</a></li>
            <li><a href="#templates" class="text-white">Template</a></li>
            <li><a href="#pricing" class="text-white">Harga</a></li>
            @auth
                <li><a href="{{ route('dashboard') }}" class="text-white">Dashboard</a></li>
            @else
                <li><a href="{{ route('login') }}" class="text-white">Login</a></li>
            @endauth
            <li><a class="theme-toggle text-white" href="#"><i class="fa-solid fa-moon"></i></a></li>
          </ul>
        </nav>
        <a class="text-white contact-link" href="mailto:{{ \App\Models\GeneralConfig::get('contact_email', 'info@undanganberpesta.com') }}"><i>
            <svg height="112" viewBox="0 0 24 24" width="112" xmlns="http://www.w3.org/2000/svg"><g clip-rule="evenodd" fill="rgb(255,255,255)" fill-rule="evenodd"><path d="m7 2.75c-.41421 0-.75.33579-.75.75v17c0 .4142.33579.75.75.75h10c.4142 0 .75-.3358.75-.75v-17c0-.41421-.3358-.75-.75-.75zm-2.25.75c0-1.24264 1.00736-2.25 2.25-2.25h10c1.2426 0 2.25 1.00736 2.25 2.25v17c0 1.2426-1.0074 2.25-2.25 2.25h-10c-1.24264 0-2.25-1.0074-2.25-2.25z"></path><path d="m10.25 5c0-.41421.3358-.75.75-.75h2c.4142 0 .75.33579.75.75s-.3358.75-.75.75h-2c-.4142 0-.75-.33579-.75-.75z"></path><path d="m9.25 19c0-.4142.33579-.75.75-.75h4c.4142 0 .75.3358.75.75s-.3358.75-.75.75h-4c-.41421 0-.75-.3358-.75-.75z"></path></g></svg>
          </i> {{ \App\Models\GeneralConfig::get('contact_email', 'info@undanganberpesta.com') }}</a>
      </div>
    </div>
  </header>
<!-- header end -->
<section class="hero-section two">
  <div class="container">
    <div class="row">
      <div class="col-lg-12">
        <div class="hero-text">
          <h2>{{ \App\Models\GeneralConfig::get('hero_title', 'Buat Undangan Digital') }} <span>{{ \App\Models\GeneralConfig::get('hero_highlight', 'Impianmu') }}</span></h2>
          <p>{{ \App\Models\GeneralConfig::get('hero_subtitle', 'Mulai Sekarang Gratis!') }}</p>
          @guest
          <form role="form" class="get-subscribee" action="{{ route('login') }}" method="get">
            <button type="submit" class="btn"><span>Mulai Gratis</span></button>
          </form>
          @else
          <a href="{{ route('dashboard') }}" class="btn"><span>Ke Dashboard</span></a>
          @endguest  
        </div>
      </div>
    </div>
  </div>
</section>
<section id="about" class="gap">
  <div class="container">
    <div class="heading">
      <span>{{ \App\Models\GeneralConfig::get('about_subtitle', 'SELAMAT DATANG DI UNDANGAN DIGITAL') }}</span>
      <h2>{{ \App\Models\GeneralConfig::get('about_title', 'Platform Undangan Online Terlengkap') }}</h2>
    </div>
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="customize-img">
          <div style="width:100%; height:400px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display:flex; align-items:center; justify-content:center;">
            <span style="color:white; font-size:24px; font-weight:600;">Platform Undangan Digital</span>
          </div>
        </div>
      </div>
      <div class="col-lg-5">
        <div class="customize-text">
          <p>{{ \App\Models\GeneralConfig::get('about_description', 'Buat undangan digital yang elegan dan profesional dengan mudah. Platform kami menyediakan berbagai template menarik, fitur musik, galeri foto, dan amplop digital untuk membuat undangan Anda sempurna.') }}</p>
          <ul>
              <li><img alt="check" src="{{ asset('demos-assets/img/check-b.png') }}">Template Premium & Basic</li>
              <li><img alt="check" src="{{ asset('demos-assets/img/check-b.png') }}">Musik Latar Pilihan</li>
              <li><img alt="check" src="{{ asset('demos-assets/img/check-b.png') }}">Galeri Foto Cantik</li>
              <li><img alt="check" src="{{ asset('demos-assets/img/check-b.png') }}">Amplop Digital</li>
            </ul>
        </div>
      </div>
    </div>
  </div>
</section>
<section id="features" class="gap no-top">
  <div class="container">
    <div class="heading two">
      <span>Fitur Unggulan</span>
      <h2>Semua Yang Anda Butuhkan Untuk Undangan Sempurna</h2>
    </div>
    <div class="row">
      <div class="col-lg-4 col-md-6">
        <div class="server">
          <i><img alt="server" src="{{ asset('demos-assets/img/server-1.png') }}"></i>
          <a href="#"><h5>Template Elegan</h5></a>
          <p>Pilihan template premium dan basic yang bisa disesuaikan dengan tema acara Anda. Desain modern dan responsif.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="server two">
          <i><img alt="server" src="{{ asset('demos-assets/img/server-2.png') }}"></i>
          <a href="#"><h5>Musik & Galeri</h5></a>
          <p>Tambahkan musik favorit dan galeri foto untuk membuat undangan lebih personal dan berkesan.</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="server three">
          <i><img alt="server" src="{{ asset('demos-assets/img/server-3.png') }}"></i>
          <a href="#"><h5>Amplop Digital</h5></a>
          <p>Terima ucapan dan hadiah digital dari tamu undangan dengan fitur amplop digital yang mudah digunakan.</p>
        </div>
      </div>
    </div>
    <div class="questions">
      <h5>Punya pertanyaan? Hubungi kami di</h5>
      <a class="btn" href="mailto:info@undanganberpesta.com"><span><i class="fa-regular fa-envelope"></i> info@undanganberpesta.com</span></a>
    </div>
  </div>
</section>
<section id="templates" class="it-works gap">
  <div class="container">
    <div class="heading">
      <span>Template Pilihan</span>
      <h2>Desain Profesional Yang Siap Pakai</h2>
    </div>
    <div class="row align-items-center">
      <div class="col-lg-6">
        <div class="staps">
          <div class="staps-img">
            <i><img alt="staps" src="{{ asset('demos-assets/img/shaps-1.png') }}"></i>
            <span>1</span>
          </div>
          <div class="staps-text">
            <h4>Pilih Template Favorit</h4>
            <p>Pilih dari berbagai template yang tersedia sesuai dengan tema acara Anda.</p>
          </div>
        </div>
        <div class="staps">
          <div class="staps-img">
            <i><img alt="staps" src="{{ asset('demos-assets/img/shaps-2.png') }}"></i>
            <span>2</span>
          </div>
          <div class="staps-text">
            <h4>Kustomisasi Konten</h4>
            <p>Isi data undangan, tambahkan foto, musik, dan informasi acara dengan mudah.</p>
          </div>
        </div>
        <div class="staps mb-lg-0">
          <div class="staps-img">
            <i><img alt="staps" src="{{ asset('demos-assets/img/shaps-3.png') }}"></i>
            <span>3</span>
          </div>
          <div class="staps-text">
            <h4>Bagikan Ke Tamu</h4>
            <p>Publikasikan dan bagikan link undangan ke tamu melalui WhatsApp atau media sosial.</p>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="customize-img">
          <div style="width:100%; height:400px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 10px; display:flex; align-items:center; justify-content:center;">
            <span style="color:white; font-size:24px; font-weight:600;">Template Elegan</span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <img alt="lines" src="{{ asset('demos-assets/img/lines.png') }}" class="lines">
</section>

{{-- Template Gallery Section --}}
<section id="template-gallery" class="gap">
  <div class="container">
    <div class="heading">
      <span>Pilihan Template</span>
      <h2>Template Undangan Digital Pestamu Nanti</h2>
    </div>
    
    {{-- Type Filter --}}
    <div class="template-type-filter mb-4 text-center">
      <div class="btn-group" role="group">
        <a href="#" data-type="all" class="btn btn-outline-primary type-filter-btn active">
          Semua
        </a>
        <a href="#" data-type="free" class="btn btn-outline-success type-filter-btn">
          Gratis
        </a>
        <a href="#" data-type="premium" class="btn btn-outline-warning type-filter-btn">
          Premium
        </a>
      </div>
    </div>
    
    {{-- Category Tabs --}}
    <div class="template-categories mb-4">
      <ul class="nav nav-pills justify-content-center" role="tablist">
        <li class="nav-item">
          <a href="#" data-category="all" class="nav-link category-filter-btn active">
            Semua Template
          </a>
        </li>
        @foreach($categories->where('slug', '!=', 'all') as $category)
        <li class="nav-item">
          <a href="#" data-category="{{ $category->slug }}" class="nav-link category-filter-btn">
            {{ $category->name }}
          </a>
        </li>
        @endforeach
      </ul>
    </div>

    {{-- Loading Indicator --}}
    <div id="template-loading" class="text-center py-5" style="display: none;">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
      <p class="mt-2 text-muted">Memuat template...</p>
    </div>

    {{-- Template Content --}}
    <div class="template-content">
      <div class="row g-3" id="template-grid">
        @include('landing.partials.template-grid', ['templates' => $templates])
      </div>
    </div>

    <div class="text-center mt-5">
      @auth
      <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
        <span>Mulai Buat Undangan</span>
      </a>
      @else
      <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
        <span>Daftar Sekarang</span>
      </a>
      @endauth
    </div>
  </div>
</section>

<section id="pricing" class="pricing-plans-section gap">
  <div class="container">
    <div class="heading">
      <span>Paket Harga</span>
      <h2>Pilih Paket Yang Sesuai Dengan Kebutuhan Anda</h2>
    </div>
    <div class="row justify-content-center">
      @foreach($plans as $plan)
      <div class="col-lg-4 col-md-6">
        <div class="pricing-plans {{ $plan->is_popular ? 'two' : '' }} {{ $plan->isBusinessPlan() ? 'business-plan' : '' }}">
          @if($plan->is_popular)
          <div style="background: rgba(104, 103, 102, 0.2); padding: 8px; margin: -20px -20px 15px; border-radius: 8px 8px 0 0;">
            <small style="color: #ffffffff; font-weight: bold;">⭐ PALING POPULER</small>
          </div>
          @endif
          @if($plan->isBusinessPlan())
          <div style="background: rgba(23, 162, 184, 0.2); padding: 8px; margin: -20px -20px 15px; border-radius: 8px 8px 0 0;">
            <small style="color: #ffffffff; font-weight: bold;">🏢 PAKET BISNIS</small>
          </div>
          @endif
          <span>{{ $plan->name }}</span>
          <h5>{{ $plan->formattedPrice() }}</h5>
        </div>
        <div class="pricing-plans-text {{ $loop->last ? 'mb-0' : '' }}">
          <i><svg enable-background="new 0 0 512 512" height="512" viewBox="0 0 512 512" width="512" xmlns="http://www.w3.org/2000/svg"><g><path d="m376.437 232.804v-130.342h30.125v-27.213l-75.249-75.249-75.25 75.25v27.213h30.125v81.052c-9.792-1.811-19.88-2.766-30.188-2.766s-20.396.955-30.188 2.766v-33.059h30.125v-27.213l-75.249-75.25-75.25 75.25v27.213h30.125v82.347c-27.998 29.674-45.187 69.65-45.187 113.57-.001 91.327 74.298 165.627 165.624 165.627s165.625-74.3 165.625-165.625c0-43.92-17.19-83.897-45.188-113.571zm-75.161-160.341 30.037-30.037 30.036 30.036h-14.912v135.228c-9.461-6.19-19.586-11.444-30.25-15.618v-119.609zm-120.588 17.957 30.037 30.037h-14.912v71.616c-10.664 4.174-20.789 9.428-30.25 15.618v-87.233h-14.912zm75.312 391.58c-74.784 0-135.625-60.841-135.625-135.625s60.841-135.625 135.625-135.625 135.625 60.841 135.625 135.625-60.842 135.625-135.625 135.625z"/><path d="m263.88 331.376h-15.757c-7.547 0-13.687-6.14-13.687-13.687 0-7.546 6.14-13.686 13.687-13.686h15.396c7.557 0 13.704 6.147 13.704 13.704h30c0-21.546-15.677-39.488-36.222-43.049v-18.659h-30v18.595c-20.712 3.412-36.565 21.433-36.565 43.097 0 24.088 19.598 43.686 43.687 43.686h15.757c7.547 0 13.687 6.14 13.687 13.687s-6.14 13.687-13.687 13.687h-16.038c-7.267 0-13.178-5.912-13.178-13.178h-30c0 21.48 15.769 39.342 36.337 42.631v18.553h30v-18.595c20.712-3.411 36.565-21.433 36.565-43.097.001-24.091-19.597-43.689-43.686-43.689z"/></g></svg></i>
            <div class="hero-text">
              {{-- Partnership Logo untuk Business Plan --}}
              @if($plan->show_partnership_logo && $partners->count() > 0)
              <div class="partnership-logos mb-3">
                <p class="text-center mb-2" style="font-size: 14px; opacity: 0.8;">Partner Kami:</p>
                <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
                  @foreach($partners->take(4) as $partner)
                    @if($partner->logo)
                      <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" style="max-height: 40px; width: auto; opacity: 0.7;">
                    @else
                      <span style="font-size: 12px; opacity: 0.6;">{{ $partner->name }}</span>
                    @endif
                  @endforeach
                </div>
              </div>
              @endif
              
              {{-- Batas detail seperti di subscription page --}}
              <div class="pricing-detail-box">
                <div class="pricing-detail-row">
                  <span class="pricing-detail-label">Undangan</span>
                  <strong class="pricing-detail-value">{{ $plan->max_invitations >= 999 ? 'Unlimited' : $plan->max_invitations }}</strong>
                </div>
                <div class="pricing-detail-row">
                  <span class="pricing-detail-label">Foto Galeri</span>
                  <strong class="pricing-detail-value">{{ $plan->max_gallery_photos ?? 'Unlimited' }}</strong>
                </div>
                <div class="pricing-detail-row">
                  <span class="pricing-detail-label">Upload Lagu</span>
                  <strong class="pricing-detail-value">{{ $plan->max_music_uploads === null ? 'Unlimited' : ($plan->max_music_uploads === 0 ? 'Tidak bisa' : $plan->max_music_uploads . ' lagu') }}</strong>
                </div>
                <div class="pricing-detail-row">
                  <span class="pricing-detail-label">Gift Section</span>
                  <strong class="pricing-detail-value {{ $plan->gift_section_included ? 'text-success' : 'text-warning' }}">
                    {{ $plan->gift_section_included ? 'Gratis' : 'Rp 10.000' }}
                  </strong>
                </div>
              </div>

              {{-- Features list --}}
              @if($plan->features && is_array($plan->features) && count($plan->features) > 0)
              <ul style="margin-bottom: 20px;">
                @foreach($plan->features as $feature)
                <li><img alt="check" src="{{ asset('demos-assets/img/check-b.png') }}"> {{ $feature }}</li>
                @endforeach
              </ul>
              @endif

              {{-- Button untuk Business Plan: WhatsApp --}}
              @if($plan->isBusinessPlan())
                @php
                  $whatsapp = \App\Models\GeneralConfig::get('contact_phone');
                  $message = urlencode('Halo, saya tertarik dengan paket Business. Mohon informasi lebih lanjut.');
                  $waLink = $whatsapp ? 'https://wa.me/' . preg_replace('/[^0-9]/', '', $whatsapp) . '?text=' . $message : '#';
                @endphp
                <a href="{{ $waLink }}" target="_blank" class="btn btn-success">
                  <span>Hubungi Admin</span>
                </a>
              @else
                {{-- Button untuk paket public --}}
                @auth
                  <a href="{{ route('subscription.index') }}" class="btn"><span>Lihat Paket</span></a>
                @else
                  <a href="{{ route('login') }}" class="btn"><span>Pilih Paket</span></a>
                @endauth
              @endif
            </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

{{-- Partners Section --}}
@if($partners->count() > 0)
<section id="partners" class="gap">
  <div class="container">
    <div class="heading">
      <span>Partner Kami</span>
      <h2>Dipercaya Oleh Partner Terbaik</h2>
    </div>
    <div class="logodata owl-carousel owl-theme">
      @foreach($partners as $partner)
      <div class="partner item">
        @if($partner->logo)
          <a href="{{ $partner->site_url }}" target="_blank" rel="noopener noreferrer">
            <img src="{{ $partner->logo_url }}" alt="{{ $partner->name }}" style="max-height: 80px; width: auto; object-fit: contain;">
          </a>
        @else
          <a href="{{ $partner->site_url }}" target="_blank" rel="noopener noreferrer" class="partner-text-link">
            <div style="padding: 20px; text-align: center;">
              <h5 class="partner-name" style="margin: 0; font-size: 16px;">{{ $partner->name }}</h5>
            </div>
          </a>
        @endif
      </div>
      @endforeach
    </div>
  </div>
</section>
@endif

<footer class="gap no-bottom">
  <div class="container">
    <div class="row">
      <div class="col-lg-4 col-md-6">
        <div class="logo">
          <a href="/">
            @php
              $logoDark = \App\Models\GeneralConfig::get('logo_dark');
              $logoLight = \App\Models\GeneralConfig::get('logo_light');
            @endphp
            @if($logoDark)
            <img alt="logo" src="{{ asset('storage/' . $logoDark) }}" class="logo-dark">
            @else
            <img alt="logo" src="{{ asset('demos-assets/img/logo.png') }}" class="logo-dark">
            @endif
            @if($logoLight)
            <img alt="logo" src="{{ asset('storage/' . $logoLight) }}" class="logo-light" style="display: none;">
            @else
            <img alt="logo" src="{{ asset('demos-assets/img/logo.png') }}" class="logo-light" style="display: none;">
            @endif
          </a>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="links">
          <h5>Informasi</h5>
          <div class="line"></div>
          <p>{{ \App\Models\GeneralConfig::get('site_description', 'Platform undangan online terlengkap untuk acara spesial Anda. Mudah, cepat, dan profesional.') }}</p>
        </div>
      </div>
      <div class="col-lg-4 col-md-6">
        <div class="links">
          <h5>Hubungi Kami</h5>
          <div class="line"></div>
          <span>Email:<a href="mailto:{{ \App\Models\GeneralConfig::get('contact_email', 'info@undanganberpesta.com') }}">{{ \App\Models\GeneralConfig::get('contact_email', 'info@undanganberpesta.com') }}</a></span>
          @if(\App\Models\GeneralConfig::get('contact_phone'))
          <span class="mt-2 d-block">Phone:<a href="tel:{{ \App\Models\GeneralConfig::get('contact_phone') }}">{{ \App\Models\GeneralConfig::get('contact_phone') }}</a></span>
          @endif
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <h3>Mulai Gratis Sekarang</h3>
      <p>Buat undangan digital pertama Anda dan bagikan ke tamu dengan mudah</p>
      <a href="{{ route('login') }}" class="btn"><span>Mulai Gratis</span></a>
    </div>
    <div class="footer-end">
      <p>{{ date('Y') }} © {{ \App\Models\GeneralConfig::get('site_name', config('app.name')) }} | Platform Undangan Digital</p>
    </div>
  </div>
  <div class="footer-shaps">
  </div>
</footer>
<!-- progress -->
<div id="progress">
      <span id="progress-value"><i class="fa-solid fa-arrow-up"></i></span>
</div>
<!-- Bootstrap Js -->
<script src="{{ asset('demos-assets/js/bootstrap.min.js') }}"></script>
<script src="{{ asset('demos-assets/js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('demos-assets/js/jquery.fancybox.min.js') }}"></script>
<script src="{{ asset('demos-assets/js/custom.js') }}"></script>

{{-- Template Filter AJAX Script --}}
<script>
$(document).ready(function() {
  let currentCategory = 'all';
  let currentType = 'all';
  
  // Type filter click handler
  $('.type-filter-btn').on('click', function(e) {
    e.preventDefault();
    
    // Update active state
    $('.type-filter-btn').removeClass('active');
    $(this).addClass('active');
    
    // Get selected type
    currentType = $(this).data('type');
    
    // Load templates
    loadTemplates(currentCategory, currentType);
  });
  
  // Category filter click handler
  $('.category-filter-btn').on('click', function(e) {
    e.preventDefault();
    
    // Update active state
    $('.category-filter-btn').removeClass('active');
    $(this).addClass('active');
    
    // Get selected category
    currentCategory = $(this).data('category');
    
    // Load templates
    loadTemplates(currentCategory, currentType);
  });
  
  // Function to load templates via AJAX
  function loadTemplates(category, type) {
    // Show loading indicator
    $('#template-loading').show();
    $('#template-grid').hide();
    
    // Smooth scroll to template section
    $('html, body').animate({
      scrollTop: $('#template-gallery').offset().top - 100
    }, 300);
    
    // AJAX request
    $.ajax({
      url: '{{ route("landing.templates") }}',
      method: 'GET',
      data: {
        category: category,
        type: type
      },
      success: function(response) {
        // Update template grid
        $('#template-grid').html(response);
        
        // Hide loading, show grid
        $('#template-loading').hide();
        $('#template-grid').fadeIn(300);
      },
      error: function(xhr, status, error) {
        console.error('Error loading templates:', error);
        
        // Show error message
        $('#template-grid').html(
          '<div class="col-12 text-center py-5">' +
          '<p class="text-danger">Terjadi kesalahan saat memuat template. Silakan coba lagi.</p>' +
          '</div>'
        );
        
        // Hide loading, show grid
        $('#template-loading').hide();
        $('#template-grid').fadeIn(300);
      }
    });
  }
});
</script>
</body>
</html>
