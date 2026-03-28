@forelse($templates as $template)
<div class="col-xl-3 col-md-3 col-sm-6 col-6">
    @php
        $user = auth()->user();
        $isPremium = $template->type === 'premium';
        $canAccess = $user->isAdmin() || !$isPremium || $user->canUsePremiumTemplate();
        $needUpgrade = $isPremium && !$canAccess;
    @endphp
    
    <div class="card h-100 {{ $needUpgrade ? 'border-warning' : '' }}" style="overflow: hidden; {{ $needUpgrade ? 'opacity: 0.85;' : '' }}">
        {{-- Thumbnail dengan aspect ratio 3:4 --}}
        <div style="position: relative; padding-top: 133%; overflow: hidden;">
            @if($template->thumbnail)
                <img src="{{ asset('storage/' . $template->thumbnail) }}" 
                     style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; {{ $needUpgrade ? 'filter: grayscale(50%);' : '' }}" 
                     alt="{{ $template->name }}">
            @else
                <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(135deg,#6f42c1,#e83e8c); display: flex; align-items: center; justify-content: center;">
                    <span class="text-white fw-bold">{{ $template->name }}</span>
                </div>
            @endif
            
            @if($needUpgrade)
            <div style="position: absolute; top: 10px; right: 10px;">
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-lock me-1"></i> Premium
                </span>
            </div>
            @endif
        </div>
        
        {{-- Card Body --}}
        <div class="card-body p-3">
            <h6 class="card-title mb-2" style="font-size: 14px; font-weight: 600; min-height: 40px; line-height: 1.4;">
                {{ $template->name }}
            </h6>
            
            {{-- Badges --}}
            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                @if($template->category)
                <span class="badge badge-secondary" style="font-size: 10px;">{{ $template->category->name }}</span>
                @endif
                <span class="badge badge-{{ $template->type === 'free' ? 'success' : 'warning' }}" style="font-size: 10px;">
                    {{ $template->formattedPrice() }}
                </span>
            </div>
            
            {{-- Button --}}
            @if($canAccess)
                <a href="{{ route('invitations.create', ['template_id' => $template->id]) }}" 
                   class="btn btn-primary btn-sm w-100" 
                   style="font-size: 12px; padding: 8px;">
                    <i class="fas fa-check me-1"></i> Gunakan
                </a>
            @else
                <button type="button" 
                        class="btn btn-warning btn-sm w-100 upgrade-modal-trigger" 
                        style="font-size: 12px; padding: 8px;"
                        data-template-name="{{ $template->name }}"
                        data-bs-toggle="modal" 
                        data-bs-target="#upgradeModal">
                    <i class="fas fa-crown me-1"></i> Upgrade untuk Akses
                </button>
            @endif
        </div>
    </div>
</div>
@empty
<div class="col-12">
    <div class="card card-body text-center py-5">
        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
        <p class="text-muted mb-0">Tidak ada template yang sesuai dengan filter</p>
    </div>
</div>
@endforelse
