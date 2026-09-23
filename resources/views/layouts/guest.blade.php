<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Acceso - BIENVENIDOS AL RESTAURANTE POLLOS KROSTY SADO</title>

        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <link href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.5.2/css/all.min.css" rel="stylesheet">

        <style>
            :root {
                --app-bg: #f1f3f5;
                --card-bg: #ffffff;
                --title-red: #b30000;
                --accent-green: #2f9e44;
                --text-main: #1f2937;
            }

            body {
                font-family: 'Nunito', sans-serif;
                background: radial-gradient(circle at top right, #e9ecef 0%, #f1f3f5 40%, #f8f9fa 100%);
                color: var(--text-main);
                min-height: 100vh;
            }

            .auth-brand {
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
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="min-vh-100 d-flex align-items-center justify-content-center p-3">
            <div class="w-100" style="max-width: 460px;">
                <div class="text-center mb-3">
                    <a href="{{ url('/') }}" class="text-decoration-none auth-brand fs-4">
                        <i class="fa-solid fa-drumstick-bite me-2"></i>POLLOS KROSTY SADO
                    </a>
                </div>
                <div class="card card-elevated border-0">
                    <div class="card-body p-4 p-md-5">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>
