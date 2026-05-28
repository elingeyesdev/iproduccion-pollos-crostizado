<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel CRUD') - {{ config('app.name') === 'Laravel' ? 'SISTEMA WEB KROSTY-SADO' : config('app.name', 'SISTEMA WEB KROSTY-SADO') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8Ryk2vQz5K1jYyp7R2WQz9WbM6f0aUg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    @vite(['resources/css/crud-layout.css', 'resources/js/crud-layout.js'])

    @stack('styles')
</head>
<body>
    <div class="app-shell">
        @include('layouts.partials.sidebar')

        <div class="app-main">
            @include('layouts.partials.crud-header')

            <div class="offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
                <div class="offcanvas-header">
                    <h5 class="offcanvas-title" id="mobileSidebarLabel">{{ config('app.name', 'SISTEMA WEB KROSTY-SADO') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                </div>
                <div class="offcanvas-body">
                    <div class="d-grid gap-1">
                        @include('layouts.partials.sidebar', ['mobile' => true])
                    </div>
                </div>
            </div>

            <main class="py-4 flex-grow-1">
                <div class="container-fluid px-3 px-lg-4 fade-slide">
                    <x-crud.alert />
                    @yield('content')
                </div>
            </main>

            @include('layouts.partials.footer')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
