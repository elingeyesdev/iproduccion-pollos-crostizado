@php
    use App\Http\Middleware\CheckRoutePermission;

    $isMobile = $mobile ?? false;
    $menuPrefix = $isMobile ? 'm' : 'd';

    $sections = config('sidebar.crud_sections', []);
@endphp

@if (!$isMobile)
    <aside class="app-sidebar d-flex flex-column p-3">
        <a href="{{ route('dashboard') }}" class="sidebar-logo mb-3" aria-label="Ir al Dashboard">
            @php
                $logoCandidates = [
                    public_path('images/logo.svg'),
                    public_path('images/logo.png'),
                    public_path('images/logo.jpg'),
                    public_path('images/logo.jpeg'),
                    public_path('images/logo.webp'),
                ];
                $logoPath = null;
                foreach ($logoCandidates as $candidate) {
                    if (file_exists($candidate)) {
                        $logoPath = str_replace(public_path(), '', $candidate);
                        $logoPath = str_replace('\\', '/', $logoPath);
                        break;
                    }
                }
            @endphp

            @if ($logoPath)
                <img src="{{ asset($logoPath) }}" alt="Logo Krosty-Sado" style="width:56px;height:56px;object-fit:contain;border-radius:50% !important;background:#fff;padding:4px;border:2px solid rgba(179,0,0,0.28);box-shadow:0 4px 12px rgba(0,0,0,0.10);flex-shrink:0;" onerror="this.style.display='none'; const fb = document.getElementById('sidebar-icon-fallback'); fb.style.display='flex';">
                <span id="sidebar-icon-fallback" class="sidebar-logo-icon" style="display:none;width:56px;height:56px;border-radius:50% !important;align-items:center;justify-content:center;font-size:1.55rem;color:#fff;background:linear-gradient(135deg,#a81616 0%,#b30000 100%);border:2px solid rgba(255,255,255,0.85);box-shadow:0 4px 12px rgba(179,0,0,0.28);flex-shrink:0;">
                    <i class="fa-solid fa-drumstick-bite"></i>
                </span>
            @else
                <span class="sidebar-logo-icon" style="width:56px;height:56px;border-radius:50% !important;display:flex;align-items:center;justify-content:center;font-size:1.55rem;color:#fff;background:linear-gradient(135deg,#a81616 0%,#b30000 100%);border:2px solid rgba(255,255,255,0.85);box-shadow:0 4px 12px rgba(179,0,0,0.28);flex-shrink:0;">
                    <i class="fa-solid fa-drumstick-bite"></i>
                </span>
            @endif

            <span class="sidebar-logo-text">
                <span class="brand-top">SISTEMA WEB</span>
                <span class="brand-bottom">KROSTY-SADO</span>
            </span>
        </a>
@endif

<div class="sidebar-module mb-2">
    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
        <i class="fa-solid fa-house"></i><span>Dashboard</span>
    </a>
</div>

@foreach ($sections as $sectionIndex => $section)
    @php
        $sectionId = 'sidebar-module-' . $menuPrefix . '-' . $sectionIndex;
        $isSectionActive = collect($section['items'])->contains(fn ($item) => request()->routeIs($item['active']));
        $sectionIcon = $section['module_icon'] ?? 'bi bi-grid-1x2';
    @endphp

    <div class="sidebar-module mb-2" data-sidebar-module>
        <button
            type="button"
            class="sidebar-module-toggle"
            data-sidebar-toggle
            data-target="{{ $sectionId }}"
            aria-expanded="{{ $isSectionActive ? 'true' : 'false' }}"
        >
            <span class="sidebar-section-title mb-0 mt-0">
                <i class="{{ $sectionIcon }} sidebar-section-icon"></i>
                <span>{{ $section['title'] }}</span>
            </span>
            <i class="fa-solid fa-chevron-down sidebar-chevron {{ $isSectionActive ? 'is-open' : '' }}"></i>
        </button>

        <div id="{{ $sectionId }}" class="sidebar-module-menu {{ $isSectionActive ? 'is-open' : '' }}" data-sidebar-content>
            @foreach ($section['items'] as $item)
                @php
                    $isAvailable = Route::has($item['route']);
                    $permission = CheckRoutePermission::permissionForRoute($item['route']);
                    $isAuthorized = $permission === null || auth()->user()?->can($permission);
                    $href = $isAvailable ? route($item['route']) : 'javascript:void(0)';
                    $isActive = request()->routeIs($item['active']);
                @endphp

                @if ($isAuthorized)
                <a href="{{ $href }}" class="sidebar-link {{ $isActive ? 'active' : '' }} {{ !$isAvailable ? 'disabled' : '' }}" @if(!$isAvailable) aria-disabled="true" @endif>
                    <i class="{{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
                </a>
                @endif
            @endforeach
        </div>
    </div>
@endforeach

<hr class="sidebar-divider"><br/><br/><br/><br/><br/>

<div class="sidebar-module mb-2">
    <a href="{{ route('profile.edit') }}" class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
        <i class="fa-solid fa-user"></i><span>Perfil</span>
    </a>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="sidebar-link sidebar-link-pill">
            <i class="fa-solid fa-right-from-bracket"></i><span>Cerrar sesión</span>
        </button>
    </form>
</div>

@if (!$isMobile)
    </aside>
@endif

