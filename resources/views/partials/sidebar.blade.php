<div class="deznav">
    <div class="deznav-scroll">
        <ul class="metismenu" id="menu">

            @foreach(\App\Models\Menu::getMenuTree() as $menu)
                @php
                    $hasVisibleChildren = $menu->children->filter(fn($c) =>
                        !$c->permission_name || auth()->user()->can($c->permission_name)
                    )->count() > 0;

                    // Parent dengan children: tampil jika ada child yang bisa diakses
                    // Parent tanpa children: tampil jika tidak ada permission, atau user punya permission
                    $canSeeMenu = $menu->children->count() > 0
                        ? $hasVisibleChildren
                        : (!$menu->permission_name || auth()->user()->can($menu->permission_name));
                @endphp

                @if($canSeeMenu)

                    @if($menu->children->count() > 0)
                        {{-- Parent menu dengan sub-menu --}}
                        @if($hasVisibleChildren)
                            <li class="{{ request()->routeIs(rtrim($menu->slug, '/') . '.*') ? 'mm-active' : '' }}">
                                <a class="has-arrow ai-icon" href="javascript:void(0)" aria-expanded="false">
                                    <i class="{{ $menu->icon }}"></i>
                                    <span class="nav-text">{{ $menu->name }}</span>
                                </a>
                                <ul aria-expanded="false">
                                    @foreach($menu->children as $child)
                                        @if(!$child->permission_name || auth()->user()->can($child->permission_name))
                                            <li class="{{ request()->is(ltrim($child->url ?? '', '/') . '*') ? 'mm-active' : '' }}">
                                                <a href="{{ $child->url ? url($child->url) : 'javascript:void(0)' }}">{{ $child->name }}</a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </li>
                        @endif
                    @else
                        {{-- Menu tunggal tanpa sub-menu --}}
                        <li class="{{ request()->is(ltrim($menu->url ?? '/', '/')) || ($menu->url === '/' && request()->is('/')) ? 'mm-active' : '' }}">
                            <a href="{{ $menu->url ? url($menu->url) : 'javascript:void(0)' }}" class="ai-icon" aria-expanded="false">
                                <i class="{{ $menu->icon }}"></i>
                                <span class="nav-text">{{ $menu->name }}</span>
                            </a>
                        </li>
                    @endif

                @endif
            @endforeach

        </ul>
        <div class="copyright">
            <p><strong>{{ config('app.name') }}</strong> © {{ date('Y') }} All Rights Reserved</p>
        </div>
    </div>
</div>
