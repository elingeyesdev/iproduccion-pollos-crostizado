@php
    $isMobile = $mobile ?? false;
    $menuPrefix = $isMobile ? 'm' : 'd';

    $sections = config('sidebar.crud_sections', []);
@endphp

@if (!$isMobile)
    <aside class="app-sidebar d-flex flex-column p-3">
        <a href="{{ route('dashboard') }}" class="sidebar-logo mb-3">
            <i class="fa-solid fa-drumstick-bite me-2"></i> SISTEMA WEB <br> KROSTY-SADO 
        </a>
@endif

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
                    $href = $isAvailable ? route($item['route']) : 'javascript:void(0)';
                    $isActive = request()->routeIs($item['active']);
                @endphp

                <a href="{{ $href }}" class="sidebar-link {{ $isActive ? 'active' : '' }} {{ !$isAvailable ? 'disabled' : '' }}" @if(!$isAvailable) aria-disabled="true" @endif>
                    <i class="{{ $item['icon'] }}"></i><span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>
@endforeach

@if (!$isMobile)
    </aside>
@endif
