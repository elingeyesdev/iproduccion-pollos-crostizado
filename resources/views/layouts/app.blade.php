<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SISTEMA WEB KROSTY-SADO') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="app-body">
    <div class="app-wrapper">
        <aside class="app-sidebar d-flex flex-column">
            <div class="sidebar-brand px-3 py-3 border-bottom">
                <a class="text-decoration-none fw-semibold fs-5 text-dark" href="{{ url('/') }}">
                    {{ config('app.name', 'SISTEMA WEB KROSTY-SADO') }}
                </a>
            </div>

            <nav class="nav flex-column p-3 gap-1">
                <a class="nav-link active" href="{{ url('/') }}">Dashboard</a>
                <a class="nav-link" href="#">Ventas</a>
                <a class="nav-link" href="#">Inventario</a>
                <a class="nav-link" href="#">Usuarios</a>
                <a class="nav-link" href="#">Reportes</a>
            </nav>
        </aside>

        <div class="app-main">
            <nav class="navbar navbar-expand-lg bg-white border-bottom px-3">
                <div class="container-fluid px-0">
                    <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        Menu
                    </button>

                    <span class="navbar-brand mb-0 h1 ms-2">Panel</span>

                    <div class="ms-auto d-flex align-items-center gap-2">
                        <span class="text-muted small">{{ auth()->user()->name ?? 'Invitado' }}</span>
                    </div>
                </div>
            </nav>

            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileSidebarLabel">{{ config('app.name', 'SISTEMA WEB KROSTY-SADO') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <nav class="nav flex-column gap-1">
                        <a class="nav-link active" href="{{ url('/') }}">Dashboard</a>
                        <a class="nav-link" href="#">Ventas</a>
                        <a class="nav-link" href="#">Inventario</a>
                        <a class="nav-link" href="#">Usuarios</a>
                        <a class="nav-link" href="#">Reportes</a>
                    </nav>
                </div>
            </div>

            @isset($header)
                <header class="bg-white border-bottom px-4 py-3">
                    {{ $header }}
                </header>
            @endisset

            <main class="p-4 app-content">
                @yield('content')
            </main>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
