@extends('layouts.app')
@section('title', 'Pilih Template')
@section('page-title', 'Pilih Template Undangan')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('invitations.index') }}">Undangan Saya</a></li>
    <li class="breadcrumb-item active">Pilih Template</li>
@endsection

@section('content')
<p class="text-muted mb-4">
    Pilih template yang ingin Anda gunakan untuk undangan Anda.
</p>

@php $user = auth()->user(); @endphp
@if(!$user->isAdmin())
@php
    $plan      = $user->activePlan();
    $used      = $user->invitationCount();
    $remaining = $user->remainingInvitations();
@endphp
<div class="alert alert-{{ $remaining > 0 ? 'info' : 'danger' }} d-flex align-items-center gap-3 mb-4">
    <div>
        <strong>Paket {{ $plan->name }}</strong> —
        Undangan: <strong>{{ $used }}</strong> / {{ $plan->max_invitations }}
        @if($remaining > 0)
            &nbsp;·&nbsp; <span class="text-success">Sisa {{ $remaining }} slot</span>
        @else
            &nbsp;·&nbsp; <span class="text-danger">Limit tercapai</span>
        @endif
    </div>
    <a href="{{ route('subscription.index') }}" class="btn btn-sm btn-outline-primary ms-auto">
        Upgrade Paket
    </a>
</div>
@endif

{{-- Filter Section --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row g-3">
            {{-- Category Filter --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="fas fa-folder me-1"></i> Kategori
                </label>
                <select class="form-select" id="category-filter">
                    <option value="all">Semua Kategori</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->slug }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            
            {{-- Type Filter --}}
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="fas fa-tag me-1"></i> Tipe Template
                </label>
                <select class="form-select" id="type-filter">
                    <option value="all">Semua Tipe</option>
                    <option value="free">Gratis</option>
                    <option value="premium">Premium</option>
                </select>
            </div>
        </div>
    </div>
</div>

{{-- Loading Indicator --}}
<div id="template-loading" class="text-center py-5" style="display: none;">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
    <p class="mt-2 text-muted">Memuat template...</p>
</div>

{{-- Template Grid --}}
<div id="template-grid" class="row g-3">
    @include('invitations.partials.template-grid', ['templates' => $templates])
</div>

{{-- Upgrade Modal --}}
<div class="modal fade" id="upgradeModal" tabindex="-1" aria-labelledby="upgradeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="upgradeModalLabel">
                    <i class="fas fa-crown me-2"></i> Upgrade Paket untuk Akses Template Premium
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $plan = $user->activePlan();
                    $usedPremium = $user->premiumInvitationCount();
                    $remainingPremium = $user->remainingPremiumTemplates();
                @endphp
                
                <div class="alert alert-info">
                    <strong>Paket Anda Saat Ini: {{ $plan->name }}</strong>
                    <br>
                    @if($plan->max_premium_templates === 0)
                        <span class="text-danger">❌ Tidak bisa menggunakan template premium</span>
                    @elseif($plan->max_premium_templates === null)
                        <span class="text-success">✅ Template premium unlimited</span>
                    @else
                        Template Premium: <strong>{{ $usedPremium }}</strong> / {{ $plan->max_premium_templates }}
                        @if($remainingPremium > 0)
                            <br><span class="text-success">Sisa {{ $remainingPremium }} slot premium</span>
                        @else
                            <br><span class="text-danger">Limit template premium tercapai</span>
                        @endif
                    @endif
                </div>
                
                <p class="mb-3">
                    <strong id="template-name-display"></strong> adalah template premium yang memerlukan paket Basic atau lebih tinggi.
                </p>
                
                <h6 class="mb-3">Keuntungan Upgrade ke Paket Basic:</h6>
                <ul class="mb-4">
                    <li><i class="fas fa-check text-success me-2"></i> 3 undangan digital</li>
                    <li><i class="fas fa-check text-success me-2"></i> <strong>3 template premium gratis</strong></li>
                    <li><i class="fas fa-check text-success me-2"></i> 50 foto galeri</li>
                    <li><i class="fas fa-check text-success me-2"></i> Upload 4 lagu sendiri</li>
                    <li><i class="fas fa-check text-success me-2"></i> Gift section gratis</li>
                </ul>
                
                <div class="text-center">
                    <p class="text-muted mb-2">Harga Paket Basic</p>
                    <h3 class="text-primary mb-3">Rp 49.000</h3>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Nanti Saja</button>
                <a href="{{ route('subscription.index') }}" class="btn btn-warning">
                    <i class="fas fa-crown me-1"></i> Upgrade Sekarang
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Responsive card title height */
@media (max-width: 576px) {
    #template-grid .card-title {
        font-size: 12px !important;
        min-height: 35px !important;
    }
    #template-grid .btn {
        font-size: 11px !important;
        padding: 6px !important;
    }
    #template-grid .badge {
        font-size: 9px !important;
    }
}
</style>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let currentCategory = 'all';
    let currentType = 'all';
    
    // Update modal dengan nama template
    $(document).on('click', '.upgrade-modal-trigger', function() {
        const templateName = $(this).data('template-name');
        $('#template-name-display').text(templateName);
    });
    
    // Category filter change handler
    $('#category-filter').on('change', function() {
        currentCategory = $(this).val();
        loadTemplates(currentCategory, currentType);
    });
    
    // Type filter change handler
    $('#type-filter').on('change', function() {
        currentType = $(this).val();
        loadTemplates(currentCategory, currentType);
    });
    
    // Function to load templates via AJAX
    function loadTemplates(category, type) {
        // Show loading indicator
        $('#template-loading').show();
        $('#template-grid').hide();
        
        // AJAX request
        $.ajax({
            url: '{{ route("invitations.templates") }}',
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
                    '<div class="col-12">' +
                    '<div class="alert alert-danger">' +
                    '<i class="fas fa-exclamation-triangle me-2"></i>' +
                    'Terjadi kesalahan saat memuat template. Silakan coba lagi.' +
                    '</div>' +
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
@endpush
