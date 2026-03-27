@forelse($templates as $template)
<div class="col-xl-3 col-md-3 col-sm-6 col-6">
  <div class="template-card">
    <div class="template-thumbnail">
      @if($template->thumbnail)
      <img src="{{ asset('storage/' . $template->thumbnail) }}" alt="{{ $template->name }}">
      @else
      <img src="https://via.placeholder.com/300x400" alt="{{ $template->name }}">
      @endif
    </div>
    <div class="template-info">
      <h6 class="template-name">{{ $template->name }}</h6>
      <div class="template-price mb-2">
        <span class="badge badge-{{ $template->isFree() ? 'success' : 'warning' }}">
          {{ $template->formattedPrice() }}
        </span>
      </div>
      <div class="template-actions">
        @if($template->preview_url)
        <a href="{{ $template->preview_url }}" target="_blank" class="btn btn-sm btn-warning w-75 mx-auto d-block mb-2">
          <i class="fa fa-eye"></i>
          <span>Preview</span>
        </a>
        @endif
        @auth
        <a href="{{ route('invitations.create', ['template' => $template->id]) }}" class="btn btn-sm btn-primary w-75 mx-auto d-block">
          <i class="fa fa-check"></i>
          <span>Gunakan</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="btn btn-sm btn-primary w-75 mx-auto d-block">
          <i class="fa fa-sign-in-alt"></i>
          <span>Login</span>
        </a>
        @endauth
      </div>
    </div>
  </div>
</div>
@empty
<div class="col-12 text-center py-5">
  <p class="text-muted">Belum ada template tersedia untuk filter ini</p>
</div>
@endforelse
