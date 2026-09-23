<nav class="navbar navbar-expand-lg app-navbar sticky-top">
     <div class="container-fluid px-3 px-lg-4">
        <button class="btn btn-sm btn-light border d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <i class="bi bi-list me-1"></i> Menu
        </button>

        <button class="btn btn-sm btn-light border d-none d-lg-inline-flex" type="button" data-sidebar-desktop-toggle aria-label="Ocultar sidebar" title="Ocultar sidebar" aria-expanded="true">
            <i class="bi bi-layout-sidebar-inset" data-sidebar-desktop-toggle-icon></i>
        </button>

        <div class="ms-2">
            <p class="mb-0 brand-title" aria-label="Sistema Web Krosty-Sado">
                @php
                    $miniLogoCandidates = [
                        public_path('images/logo.svg'),
                        public_path('images/logo.png'),
                        public_path('images/logo.jpg'),
                        public_path('images/logo.jpeg'),
                        public_path('images/logo.webp'),
                    ];
                    $miniLogoPath = null;
                    foreach ($miniLogoCandidates as $candidate) {
                        if (file_exists($candidate)) {
                            $miniLogoPath = str_replace(public_path(), '', $candidate);
                            $miniLogoPath = str_replace('\\', '/', $miniLogoPath);
                            break;
                        }
                    }
                @endphp

                @if ($miniLogoPath)
                    <img src="{{ asset($miniLogoPath) }}" alt="Logo" style="width:42px;height:42px;object-fit:contain;border-radius:50% !important;background:#fff;padding:3px;border:2px solid rgba(179,0,0,0.28);box-shadow:0 3px 10px rgba(0,0,0,0.09);flex-shrink:0;" onerror="this.style.display='none'; const hf = document.getElementById('header-icon-fallback'); hf.style.display='inline-flex';">
                    <span id="header-icon-fallback" class="brand-title-icon" style="display:none;width:42px;height:42px;border-radius:50% !important;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;background:linear-gradient(135deg,#a81616 0%,#b30000 100%);border:2px solid rgba(255,255,255,0.85);box-shadow:0 3px 10px rgba(179,0,0,0.24);flex-shrink:0;">
                        <i class="fa-solid fa-drumstick-bite"></i>
                    </span>
                @else
                    <span class="brand-title-icon" style="width:42px;height:42px;border-radius:50% !important;display:inline-flex;align-items:center;justify-content:center;font-size:1.1rem;color:#fff;background:linear-gradient(135deg,#a81616 0%,#b30000 100%);border:2px solid rgba(255,255,255,0.85);box-shadow:0 3px 10px rgba(179,0,0,0.24);flex-shrink:0;">
                        <i class="fa-solid fa-drumstick-bite"></i>
                    </span>
                @endif

                <span class="brand-title-text">
                    <span class="brand-name">SISTEMA WEB</span>
                    <span class="brand-suffix">KROSTY-SADO</span>
                </span>
            </p>
            <p class="mb-0 small text-muted">@yield('title', 'Panel CRUD')</p>
        </div>

        <div class="ms-auto d-flex align-items-center gap-2">
            @yield('crud_header_actions')
            <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light border">Dashboard</a>

            <div class="dropdown">
                <button
                    class="btn btn-sm btn-light border dropdown-toggle"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >
                    <i class="fa-solid fa-gear me-1"></i>
                    {{ Auth::user()->name ?? 'Cuenta' }}
                </button>

                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                            <i class="fa-solid fa-user me-2"></i>Perfil
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="fa-solid fa-right-from-bracket me-2"></i>Cerrar sesion
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>
