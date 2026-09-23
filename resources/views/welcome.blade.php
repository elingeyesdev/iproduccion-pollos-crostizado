<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Sistema Interno - Pollos Krosty-Sado</title>

    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800;900&family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" integrity="sha512-SnH5WK+bZxgPHs44uWix+LLLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkR4j8Ryk2vQz5K1jYyp7R2WQz9WbM6f0aUg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <style>
        :root {
            --krosty-red:        #8a0d0d;
            --krosty-red-dark:   #6b0707;
            --krosty-red-header: #a01616ff;
            --krosty-yellow:     #eac773;
            --krosty-yellow-dark:#d3ae54;
            --krosty-green:      #56774d;
            --krosty-green-soft: #e5efe4;
            --text-main:         #1f2937;
            --muted:             #525c69;
        }

        * { -webkit-font-smoothing: antialiased; }

        html, body {
            font-family: 'Nunito', sans-serif;
            background-color: #f8f8f4;
            color: var(--text-main);
            min-height: 100vh;
        }

        /* ===== HEADER ===== */
        .krosty-header {
            background-color: var(--krosty-red-header);
            border-bottom: 2px solid rgba(0,0,0,.10);
            position: relative;
            z-index: 2;
        }

        .krosty-header .brand-title {
            font-family: 'Playfair Display', serif;
            color: #fff;
            font-weight: 700;
            letter-spacing: .2px;
            font-size: 1.45rem;
            margin: 0;
            white-space: nowrap;
        }

        .btn-login-outline {
            color: #fff;
            border: 2px solid #fff;
            background: transparent;
            font-weight: 700;
            font-size: 1.05rem;
            padding: .55rem 1.6rem;
            border-radius: 8px;
            transition: all .2s ease;
        }

        .btn-login-outline:hover {
            background: #fff;
            color: var(--krosty-red-header);
            border-color: #fff;
        }

        .btn-register-solid {
            color: #1f2937;
            background: var(--krosty-yellow);
            border: 2px solid var(--krosty-yellow);
            font-weight: 800;
            font-size: 1.05rem;
            padding: .55rem 1.6rem;
            border-radius: 8px;
            transition: all .2s ease;
        }

        .btn-register-solid:hover {
            background: var(--krosty-yellow-dark);
            border-color: var(--krosty-yellow-dark);
            color: #1f2937;
        }

        /* ===== HERO CARD ===== */
        .hero-card {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 6px 24px rgba(0, 0, 0, 0.06);
            border: 1px solid #ececea;
            position: relative;
            z-index: 1;
            overflow: hidden;
        }

        .hero-divider-col {
            position: relative;
        }

        @media (min-width: 992px) {
            .hero-divider-col::before {
                content: "";
                position: absolute;
                top: 10%;
                bottom: 10%;
                right: 0;
                width: 1px;
                background: #e6e3dd;
            }
        }

        .hero-content-wrapper {
            width: 100%;
        }

        .hero-title {
            font-family: 'Playfair Display', serif;
            color: var(--krosty-red);
            font-weight: 700;
            line-height: 1.05;
            font-size: clamp(1.3em, 4vw, 4rem);
            margin-bottom: 0.4rem;
            width: 100%;
            text-align: center;
        }

        /* ===== SEPARADOR TÍTULO ===== */
        .title-separator {
            display: flex;
            align-items: center;
            gap: .8rem;
            margin-bottom: 1.7rem;
            max-width: 380px;
        }

        .title-separator::before,
        .title-separator::after {
            content: "";
            flex: 1 1 auto;
            height: 2px;
            background-color: var(--krosty-green);
            border-radius: 2px;
            opacity: .85;
        }

        .title-separator::before { margin-right: auto; }
        .title-separator::after  { margin-left:  auto; }

        .title-separator .dot {
            flex: 0 0 auto;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: var(--krosty-green);
        }

        .hero-description {
            color: var(--muted);
            font-size: 1.2rem;
            line-height: 1.7;
            font-weight: 500;
            max-width: 520px;
            margin-bottom: 2.2rem;
        }

        .btn-enter-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .75rem;
            width: 100%;
            background: var(--krosty-red);
            color: #fff;
            font-weight: 700;
            font-size: 1.35rem;
            padding: 1rem 2rem;
            border-radius: 10px;
            border: 2px solid var(--krosty-red);
            transition: all .2s ease;
        }

        .btn-enter-primary:hover {
            background: var(--krosty-red-dark);
            border-color: var(--krosty-red-dark);
            color: #fff;
            transform: translateY(-1px);
        }

        .btn-register-outline {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            width: 100%;
            border: 2px solid var(--krosty-green);
            color: var(--krosty-green);
            background: #fff;
            font-weight: 700;
            font-size: 1.25rem;
            padding: .9rem 2rem;
            border-radius: 10px;
            transition: all .2s ease;
            margin-top: 1rem;
        }

        .btn-register-outline:hover {
            background: var(--krosty-green);
            color: #fff;
            border-color: var(--krosty-green);
            transform: translateY(-1px);
        }

        .btn-register-outline i {
            font-size: 1.35rem;
        }

        /* ===== HERO IMAGE ===== */
        .hero-illustration {
            background-color: var(--krosty-green-soft);
            border-radius: 18px;
            min-height: 420px;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            gap: 1.3rem;
        }

        .hero-illustration img.main-char {
            max-width: 88%;
            max-height: 340px;
            border-radius: 24px;
            object-fit: cover;
            filter: drop-shadow(0 6px 12px rgba(0, 40, 0, 0.10));
        }

        .logo-krosty {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .logo-krosty img.logo-gif {
            max-width: 200px;
            max-height: 80px;
            height: auto;
            width: auto;
            border-radius: 8px;
        }

        @media (max-width: 1199.98px) {
            .hero-divider-col .hero-content-wrapper {
                text-align: center;
                margin: 0 auto;
                display: flex;
                flex-direction: column;
                align-items: center;
            }
            .hero-divider-col .hero-content-wrapper .hero-title {
                text-align: center;
            }
            .hero-divider-col .hero-content-wrapper .title-separator {
                margin-left: auto !important;
                margin-right: auto !important;
            }
            .hero-divider-col .hero-content-wrapper .hero-description {
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (max-width: 991.98px) {
            .krosty-header .brand-title { font-size: 1.15rem; text-align: left; width: 100%; white-space: normal; }
            .krosty-header .header-actions { width: 100%; justify-content: center !important; margin-top: 1rem; }
            .krosty-header > .container > div { align-items: flex-start !important; }
            .hero-illustration { min-height: 320px; margin-top: 1.5rem; }
        }

        /* ===== ANIMACIONES POR PALABRAS ===== */
        .animated-word {
            display: inline-block;
            opacity: 0;
            transform: translateY(16px) rotateX(-70deg);
            animation: wordFadeIn 0.7s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
            transition: all 0.25s ease;
        }

        .animated-word + .animated-word {
            margin-left: 0.28em;
        }

        @keyframes wordFadeIn {
            0% {
                opacity: 0;
                transform: translateY(16px) rotateX(-70deg) scale(0.92);
                filter: blur(4px);
            }
            60% {
                opacity: 1;
                transform: translateY(-4px) rotateX(10deg) scale(1.02);
                filter: blur(0);
            }
            100% {
                opacity: 1;
                transform: translateY(0) rotateX(0) scale(1);
                filter: blur(0);
            }
        }

        .brand-title .animated-word:hover,
        .hero-title .animated-word:hover {
            transform: translateY(-6px) scale(1.05);
            color: var(--krosty-yellow);
            text-shadow: 0 4px 12px rgba(234, 199, 115, 0.5);
        }

        .hero-title .animated-word:hover {
            color: var(--krosty-green);
            text-shadow: 0 4px 12px rgba(86, 119, 77, 0.4);
        }
    </style>
</head>
<body>

<header class="krosty-header">
    <div class="container">
        <div class="d-flex flex-column flex-lg-row align-items-lg-center justify-content-between py-4">
            <h1 class="brand-title" data-animate-text>
                BIENVENIDOS AL SISTEMA INTERNO – POLLOS KROSTY-SADO
            </h1>

            @if (Route::has('login'))
                <div class="d-flex gap-3 align-items-center header-actions">
                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-register-solid">
                            <i class="fa-solid fa-gauge-high me-1"></i> Ir al panel
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-login-outline">
                            Iniciar sesión
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-register-solid">
                                Registrarse
                            </a>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="hero-card p-4 p-lg-5 mt-2">
            <div class="row align-items-center g-4">
                <div class="col-12 col-lg-6 px-lg-5 hero-divider-col">
                    <div class="hero-content-wrapper">
                        <h2 class="hero-title" data-animate-text>
                            SISTEMA DE PRODUCCION Y VENTAS 
                        </h2>

                    <div class="title-separator">
                        <span class="dot"></span>
                    </div>

                    <p class="hero-description">
                        Administra clientes, producción, inventario y ventas.
                    </p>

                    @auth
                        <a href="{{ route('dashboard') }}" class="btn btn-enter-primary">
                            Ir al panel
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-register-outline" style="display:none;">
                            <i class="fa-solid fa-user"></i> Crear cuenta
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-enter-primary">
                            Ingresar
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-register-outline">
                                <i class="fa-solid fa-user"></i>
                                Crear cuenta
                            </a>
                        @endif
                    @endauth

                    @auth
                        <hr class="my-4 border-light-subtle">
                        <h6 class="fw-bold mb-3" style="color: var(--krosty-red);">
                            <i class="fa-solid fa-bolt me-1"></i> Accesos rápidos
                        </h6>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="{{ route('clientes.index') }}"       class="btn btn-sm btn-outline-success">Clientes</a>
                            <a href="{{ route('insumos.index') }}"        class="btn btn-sm btn-outline-success">Insumos</a>
                            <a href="{{ route('productos.index') }}"      class="btn btn-sm btn-outline-success">Productos</a>
                            <a href="{{ route('ventas.index') }}"         class="btn btn-sm btn-outline-success">Ventas</a>
                            <a href="{{ route('producciones.index') }}"   class="btn btn-sm btn-outline-success">Producción</a>
                            <a href="{{ route('users.index') }}"          class="btn btn-sm btn-outline-success">Usuarios</a>
                        </div>
                    @endauth
                    </div>
                </div>

                <div class="col-12 col-lg-6 ps-lg-4">
                    <div class="hero-illustration">
                        <img
                            src="{{ asset('images/krosty-hero.gif') }}"
                            alt="Personaje Pollos Krosty"
                            class="main-char"
                            onerror="this.onerror=null; this.src='data:image/svg+xml;utf8,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 200 220%22><rect width=%22200%22 height=%22220%22 fill=%22%23e5efe4%22 rx=%2218%22/><text x=%22100%22 y=%22115%22 font-family=%22Arial%22 font-size=%2218%22 fill=%22%2356774d%22 text-anchor=%22middle%22>Imagen Krosty</text></svg>';">

                        <div class="logo-krosty">
                            <img
                                src="{{ asset('images/krosty-logo.gif') }}"
                                alt="Logo Krosty"
                                class="logo-gif"
                                onerror="this.onerror=null; this.innerHTML=''; this.outerHTML='<div class=\'brand-name\' style=\'font-family:Playfair Display,serif;color:#56774d;font-weight:700;font-size:2.5rem;\'>Krosty</div>';">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const elements = document.querySelectorAll('[data-animate-text]');
        let globalDelay = 0;

        elements.forEach((el) => {
            const text = el.textContent.trim();
            const words = text.split(/\s+/);

            if (!words.length) {
                return;
            }

            let html = '';
            words.forEach((word, index) => {
                const delay = globalDelay + index * 0.15;
                html += '<span class="animated-word" style="animation-delay:' + delay + 's">' + word + '</span>';
                if (index < words.length - 1) {
                    html += '&nbsp;';
                }
            });

            el.innerHTML = html;
            globalDelay += words.length * 0.15 + 0.2;
        });
    });
</script>
</body>
</html>
