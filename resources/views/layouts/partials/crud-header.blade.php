<nav class="navbar navbar-expand-lg app-navbar sticky-top">
     <div class="container-fluid px-3 px-lg-4">
        <button class="btn btn-sm btn-light border d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
            <i class="bi bi-list me-1"></i> Menu
        </button>

        <button class="btn btn-sm btn-light border d-none d-lg-inline-flex" type="button" data-sidebar-desktop-toggle aria-label="Ocultar sidebar" title="Ocultar sidebar" aria-expanded="true">
            <i class="bi bi-layout-sidebar-inset" data-sidebar-desktop-toggle-icon></i>
        </button>

        <div class="ms-2">
            <p class="mb-0 fw-bold brand-title">SISTEMA WEB  KROSTY-SADO</p>
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
