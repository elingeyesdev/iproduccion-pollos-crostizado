<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Panel CRUD') - {{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8Ryk2vQz5K1jYyp7R2WQz9WbM6f0aUg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --app-bg: #f1f3f5;
            --card-bg: #ffffff;
            --title-red: #b30000;
            --accent-green: #2f9e44;
            --text-main: #1f2937;
            --muted: #6b7280;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background: radial-gradient(circle at top right, #e9ecef 0%, #f1f3f5 40%, #f8f9fa 100%);
            color: var(--text-main);
            min-height: 100vh;
        }

        .app-navbar {
            background-color: #ffffff;
            border-bottom: 3px solid var(--title-red);
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }

        .brand-title {
            color: var(--title-red);
            font-weight: 800;
            letter-spacing: .3px;
        }

        .card-elevated {
            border: 1px solid #e5e7eb;
            border-radius: 16px;
            background-color: var(--card-bg);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
        }

        .section-title {
            color: var(--title-red);
            font-weight: 800;
        }

        .text-accent {
            color: var(--accent-green);
        }

        .btn-app-primary {
            background-color: var(--title-red);
            color: #fff;
            border: 1px solid var(--title-red);
        }

        .btn-app-primary:hover {
            background-color: #8e0000;
            border-color: #8e0000;
            color: #fff;
        }

        .btn-app-outline {
            border: 1px solid var(--accent-green);
            color: var(--accent-green);
            background: transparent;
        }

        .btn-app-outline:hover {
            background: var(--accent-green);
            color: #fff;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            font-weight: 700;
            color: #374151;
        }

        .badge-state {
            background-color: rgba(47, 158, 68, 0.15);
            color: var(--accent-green);
            border: 1px solid rgba(47, 158, 68, 0.35);
            font-weight: 700;
        }

        .fade-slide {
            animation: fadeSlide .35s ease;
        }

        @keyframes fadeSlide {
            from {
                opacity: 0;
                transform: translateY(8px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand brand-title" href="{{ url('/') }}">
                <i class="fa-solid fa-drumstick-bite me-2"></i>{{ config('app.name', 'Laravel') }}
            </a>
            <div class="ms-auto d-flex gap-2">
                <a href="{{ route('dashboard') }}" class="btn btn-sm btn-light border">Dashboard</a>
                <a href="{{ route('unidad-medidas.index') }}" class="btn btn-sm btn-app-primary">Unidad de Medida</a>
            </div>
        </div>
    </nav>

    <main class="py-4">
        <div class="container fade-slide">
            <x-crud.alert />
            @yield('content')
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    @stack('scripts')
</body>
</html>
