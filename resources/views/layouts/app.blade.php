<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - {{ \App\Models\GeneralConfig::get('site_name', config('app.name')) }}</title>

    {{-- Canonical URL --}}
    <link rel="canonical" href="{{ $canonicalUrl ?? url()->current() }}">
    
    {{-- Prevent dashboard from being indexed by search engines --}}
    <meta name="robots" content="noindex, nofollow">

    @php
        $favicon = \App\Models\GeneralConfig::get('favicon');
    @endphp
  {{-- Upgrade insecure requests to HTTPS (temporary fix for mixed content) --}}
  <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    @if($favicon)
    <link rel="icon" href="{{ asset('storage/' . $favicon) }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('storage/' . $favicon) }}">
    @else
    <link rel="icon" href="{{ asset('assets/images/favicon.ico') }}" type="image/x-icon">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/favicon.png') }}">
    @endif

    <link rel="stylesheet" href="{{ asset('assets/vendor/chartist/css/chartist.min.css') }}">
    <link href="{{ asset('assets/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/owl-carousel/owl.carousel.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
    
    <style>
        /* Logo visibility control */
        .nav-header .logo-abbr {
            display: none !important;
        }
        
        .nav-header .brand-title {
            display: inline-block !important;
            max-width: 120px;
        }
        
        /* Show logo-abbr only when sidebar is collapsed/mini */
        [data-sidebar-style="mini"] .nav-header .logo-abbr,
        .menu-toggle .nav-header .logo-abbr {
            display: block !important;
        }
        
        /* Hide brand-title when sidebar is collapsed/mini */
        [data-sidebar-style="mini"] .nav-header .brand-title,
        .menu-toggle .nav-header .brand-title {
            display: none !important;
        }
        
        @media (max-width: 767px) {
            .nav-header .brand-title {
                display: none !important;
            }
            .nav-header .logo-abbr {
                display: block !important;
            }
        }
    </style>
    @stack('styles')
</head>
<body>

    <div id="preloader">
        <div class="sk-three-bounce">
            <div class="sk-child sk-bounce1"></div>
            <div class="sk-child sk-bounce2"></div>
            <div class="sk-child sk-bounce3"></div>
        </div>
    </div>

    <div id="main-wrapper">

        {{-- Nav Header --}}
        <div class="nav-header bg-primary">
            <a href="{{ route('dashboard') }}" class="brand-logo">
                @php
                    $logoIcon = \App\Models\GeneralConfig::get('logo_icon');
                    $logoDark = \App\Models\GeneralConfig::get('logo_dark');
                @endphp
                
                {{-- Logo Icon untuk sidebar collapsed --}}
                @if($logoIcon)
                <img class="logo-abbr" src="{{ asset('storage/' . $logoIcon) }}" alt="">
                @else
                <img class="logo-abbr" src="{{ asset('assets/images/logo.png') }}" alt="">
                @endif
                
                {{-- Logo Full untuk sidebar expanded --}}
                @if($logoDark)
                <img class="brand-title" src="{{ asset('storage/' . $logoDark) }}" alt="">
                @else
                <img class="brand-title" src="{{ asset('assets/images/logo-text.png') }}" alt="">
                @endif
            </a>
            <div class="nav-control">
                <div class="hamburger">
                    <span class="line"></span><span class="line"></span><span class="line"></span>
                </div>
            </div>
        </div>

        {{-- Header --}}
        @include('partials.header')

        {{-- Sidebar --}}
        @include('partials.sidebar')

        {{-- Content Body --}}
        <div class="content-body">
            <div class="container-fluid">

                {{-- Page Title --}}
                <div class="row page-titles">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                        @yield('breadcrumb')
                    </ol>
                </div>

                {{-- Flash Messages --}}
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>

    </div>

    <script src="{{ asset('assets/vendor/global/global.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('assets/js/custom.min.js') }}"></script>
    <script src="{{ asset('assets/js/deznav-init.js') }}"></script>

    {{-- ── Global Confirm Modal ─────────────────────────────────────────── --}}
    <div class="modal fade" id="confirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <div class="modal-icon text-center w-100 pt-2">
                        <span id="confirmIcon" style="font-size:2.5rem">⚠️</span>
                    </div>
                </div>
                <div class="modal-body text-center pt-1">
                    <h5 id="confirmTitle" class="mb-1">Konfirmasi</h5>
                    <p id="confirmMessage" class="text-muted mb-0"></p>
                </div>
                <div class="modal-footer border-0 justify-content-center gap-2 pt-0">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn px-4" id="confirmOkBtn">Ya, Lanjutkan</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    /**
     * Global modal confirm — menggantikan window.confirm()
     *
     * Penggunaan via HTML:
     *   <form data-confirm="Hapus item ini?" data-confirm-title="Hapus?" data-confirm-ok="Hapus" data-confirm-type="danger">
     *
     * Penggunaan via JS:
     *   modalConfirm({ message: '...', onConfirm: () => { ... } })
     */
    window.modalConfirm = function ({ message = 'Apakah Anda yakin?', title = 'Konfirmasi', okText = 'Ya, Lanjutkan', type = 'danger', icon = null, onConfirm }) {
        const modal   = document.getElementById('confirmModal');
        const bsModal = bootstrap.Modal.getOrCreateInstance(modal);

        document.getElementById('confirmTitle').textContent   = title;
        document.getElementById('confirmMessage').textContent = message;

        const okBtn = document.getElementById('confirmOkBtn');
        okBtn.className = `btn px-4 btn-${type}`;
        okBtn.textContent = okText;

        const icons = { danger: '🗑️', warning: '⚠️', success: '✅', info: 'ℹ️' };
        document.getElementById('confirmIcon').textContent = icon ?? icons[type] ?? '⚠️';

        // Hapus listener lama
        const newBtn = okBtn.cloneNode(true);
        okBtn.parentNode.replaceChild(newBtn, okBtn);

        newBtn.addEventListener('click', () => {
            bsModal.hide();
            onConfirm();
        });

        bsModal.show();
    };

    // ── Intercept semua form dengan data-confirm ──────────────────────────
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!form.dataset.confirm) return;

        e.preventDefault();

        modalConfirm({
            message : form.dataset.confirm,
            title   : form.dataset.confirmTitle   ?? 'Konfirmasi',
            okText  : form.dataset.confirmOk      ?? 'Ya, Lanjutkan',
            type    : form.dataset.confirmType    ?? 'danger',
            onConfirm: () => form.submit(),
        });
    });
    </script>

    @stack('scripts')
</body>
</html>
