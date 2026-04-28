<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>POLLOS KROSTY SADO</title>

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

        .hero-title {
            color: var(--title-red);
            font-weight: 800;
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

        .hero-image-placeholder {
            border: 2px dashed #d1d5db;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(179, 0, 0, 0.08), rgba(47, 158, 68, 0.08));
            min-height: 260px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg app-navbar sticky-top">
        <div class="container">
            <a class="navbar-brand brand-title" href="{{ url('/') }}">
                <i class="fa-solid fa-drumstick-bite me-2"></i> BIENVEIDOS AL RESTAURANTE POLLOS KROSTY SADO
            </a>

            @if (Route::has('login'))
                <div class="ms-auto d-flex gap-2">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-sm btn-app-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-sm btn-app-outline">Iniciar sesión</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-sm btn-app-primary">Registrarse</a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            <div class="card card-elevated">
                <div class="card-body p-4 p-lg-5">
                    <div class="row align-items-center g-4">
                        <div class="col-12 col-lg-6">
                            <h1 class="hero-title display-6 mb-3">Sistema de Producción y Venta</h1>
                            <p class="text-muted mb-4">
                                Gestiona usuarios, clientes, insumos, categorías, unidades y productos finales desde un solo lugar.
                            </p>

                            <div class="d-flex flex-wrap gap-2">
                                @auth
                                    <a href="{{ route('dashboard') }}" class="btn btn-app-primary">
                                        <i class="fa-solid fa-gauge-high me-1"></i> Ir al dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="btn btn-app-primary">
                                        <i class="fa-solid fa-right-to-bracket me-1"></i> Ingresar
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="btn btn-app-outline">
                                            <i class="fa-solid fa-user-plus me-1"></i> Crear cuenta
                                        </a>
                                    @endif
                                @endauth
                            </div>

                            @auth
                                <hr class="my-4">
                                <h6 class="fw-bold mb-2">Accesos rapidos</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-app-outline">Clientes</a>
                                    <a href="{{ route('insumos.index') }}" class="btn btn-sm btn-app-outline">Insumos</a>
                                    <a href="{{ route('producto-finals.index') }}" class="btn btn-sm btn-app-outline">Productos</a>
                                    <a href="{{ route('categoria-insumos.index') }}" class="btn btn-sm btn-app-outline">Categorias</a>
                                    <a href="{{ route('unidad-medidas.index') }}" class="btn btn-sm btn-app-outline">Unidades</a>
                                    <a href="{{ route('users.index') }}" class="btn btn-sm btn-app-outline">Usuarios</a>
                                    <a href="{{ route('roles.index') }}" class="btn btn-sm btn-app-outline">Roles</a>
                                </div>
                            @endauth
                        </div>

                        <div class="col-12 col-lg-6">
                            <div class="hero-image-placeholder d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <i class="fa-solid fa-image fa-2x mb-3" style="color: #6b7280;"></i>
                                <h6 class="mb-1 fw-bold"><i class="fa-solid fa-image"></i> Espacio para imagen principal</h6>
                                <p class="mb-0 text-muted">ruta de la imagen</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
