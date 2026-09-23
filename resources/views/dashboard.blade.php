@extends('layouts.crud')

@section('title', 'Dashboard')

@push('styles')
<style>
    :root {
        --krosty-red: #8a0d0d;
        --krosty-red-light: rgba(138, 13, 13, 0.08);
        --krosty-yellow: #eac773;
        --krosty-green: #56774d;
        --krosty-green-light: rgba(86, 119, 77, 0.10);
    }

    .page-title-dash {
        font-size: 2rem;
        font-weight: 800;
        color: #111827;
        margin: 0;
        line-height: 1.1;
    }

    .page-subtitle-dash {
        font-size: 1.05rem;
        color: #6b7280;
        margin: .2rem 0 0;
        font-weight: 500;
    }

    /* ===== STATS CARDS ===== */
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 1.5rem 1.6rem;
        box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        border: 1px solid #f1f1ec;
        transition: all .25s ease;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 28px rgba(0,0,0,0.09);
    }

    .stat-card.stat-primary {
        background: linear-gradient(135deg, #8a0d0d 0%, #a81616 100%);
        color: #fff;
        border: none;
    }

    .stat-card.stat-primary .stat-icon {
        background: rgba(255,255,255,0.18);
        color: #fff;
    }

    .stat-card.stat-primary .stat-label,
    .stat-card.stat-primary .stat-trend {
        color: #fde3e3;
    }

    .stat-header {
        display: flex;
        align-items: center;
        gap: .85rem;
        margin-bottom: .6rem;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.65rem;
        font-weight: 900;
        flex-shrink: 0;
        border: 2px solid rgba(255,255,255,0.75);
        box-shadow: 0 2px 10px rgba(0,0,0,0.10);
    }

    .stat-icon.icon-red    { background: rgba(138,13,13,0.18);  color: #7a0b0b; }
    .stat-icon.icon-green  { background: rgba(86,119,77,0.22);  color: #435f3b; }
    .stat-icon.icon-yellow { background: rgba(234,199,115,0.38); color: #8a691c; }

    .stat-label {
        font-size: .98rem;
        font-weight: 700;
        color: #374151;
        margin: 0;
    }

    .stat-value {
        font-size: 2.45rem;
        font-weight: 900;
        line-height: 1;
        margin: .35rem 0 .65rem;
        letter-spacing: -.5px;
    }

    .stat-trend {
        display: inline-flex;
        align-items: center;
        gap: .35rem;
        font-size: .88rem;
        font-weight: 700;
    }

    .stat-trend.up   { color: #16a34a; }
    .stat-trend.down { color: #dc2626; }
    .stat-trend.flat { color: #6b7280; }

    /* ===== MODULE CARDS ===== */
    .module-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 4px 18px rgba(0,0,0,0.05);
        border: 1px solid #f1f1ec;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .module-header {
        background: linear-gradient(135deg, #8a0d0d 0%, #a81616 100%);
        padding: .95rem 1.4rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: .7rem;
    }

    .module-header i {
        font-size: 1.5rem;
        opacity: .95;
    }

    .module-header h4 {
        margin: 0;
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: .3px;
    }

    .module-body {
        padding: 1.2rem;
        display: flex;
        flex-direction: column;
        gap: .8rem;
        flex-grow: 1;
    }

    .module-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: .95rem 1.05rem;
        border-radius: 14px;
        background: #fff;
        border: 1px solid #efece6;
        text-decoration: none;
        color: inherit;
        transition: all .22s ease;
    }

    .module-item:hover {
        background: #faf6ef;
        border-color: #e4ddcb;
        transform: translateX(4px);
        color: inherit;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }

    .module-item-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        font-weight: 900;
        flex-shrink: 0;
        background: rgba(138,13,13,0.16);
        color: #7a0b0b;
        border: 1.5px solid rgba(255,255,255,0.7);
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .module-item-icon.green  { background: rgba(86,119,77,0.22);   color: #3d5736; }
    .module-item-icon.yellow { background: rgba(234,199,115,0.40); color: #7a5c16; }
    .module-item-icon.blue   { background: rgba(59,130,246,0.18);  color: #1d4ed8; }
    .module-item-icon.purple { background: rgba(139,92,246,0.20);  color: #6d28d9; }
    .module-item-icon.teal   { background: rgba(20,184,166,0.18);  color: #0f766e; }
    .module-item-icon.orange { background: rgba(249,115,22,0.22);  color: #c2410c; }

    .module-item-text {
        flex-grow: 1;
        min-width: 0;
    }

    .module-item-text h6 {
        margin: 0;
        font-size: .98rem;
        font-weight: 800;
        color: #111827;
    }

    .module-item-text p {
        margin: .15rem 0 0;
        font-size: .82rem;
        color: #6b7280;
        line-height: 1.35;
    }

    @media (max-width: 1199.98px) {
        .stat-value { font-size: 2rem; }
        .page-title-dash { font-size: 1.7rem; }
    }

    @media (max-width: 991.98px) {
        .stat-card { padding: 1.2rem; }
        .stat-value { font-size: 1.8rem; }
        .module-body { padding: .9rem; }
    }

    /* ===== MODULE BODY EN GRILLA (para columnas con muchos ítems) ===== */
    .module-body-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-content: start;
    }

    @media (max-width: 767.98px) {
        .module-body-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
    @php
        function trendClass($pct) {
            if ($pct > 0)  return 'up';
            if ($pct < 0)  return 'down';
            return 'flat';
        }
        function trendIcon($pct) {
            if ($pct > 0)  return 'fa-solid fa-arrow-trend-up';
            if ($pct < 0)  return 'fa-solid fa-arrow-trend-down';
            return 'fa-solid fa-minus';
        }
    @endphp

    <div class="mb-4">
        <h1 class="page-title-dash">Dashboard</h1>
        <p class="page-subtitle-dash">Resumen general del sistema</p>
    </div>

    {{-- ===== FILA DE STATS ===== --}}
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card stat-primary">
                <div class="stat-header">
                    <div class="stat-icon">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <p class="stat-label">Usuarios Activos</p>
                </div>
                <div class="stat-value">{{ number_format($stats['usuarios']['valor']) }}</div>
                <span class="stat-trend {{ trendClass($stats['usuarios']['pct']) }}">
                    <i class="{{ trendIcon($stats['usuarios']['pct']) }}"></i>
                    {{ abs($stats['usuarios']['pct']) }}% vs mes anterior
                </span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon icon-red">
                        <i class="fa-solid fa-address-book"></i>
                    </div>
                    <p class="stat-label">Clientes</p>
                </div>
                <div class="stat-value">{{ number_format($stats['clientes']['valor']) }}</div>
                <span class="stat-trend {{ trendClass($stats['clientes']['pct']) }}">
                    <i class="{{ trendIcon($stats['clientes']['pct']) }}"></i>
                    {{ abs($stats['clientes']['pct']) }}% vs mes anterior
                </span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon icon-green">
                        <i class="fa-solid fa-drumstick-bite"></i>
                    </div>
                    <p class="stat-label">Productos</p>
                </div>
                <div class="stat-value">{{ number_format($stats['productos']['valor']) }}</div>
                <span class="stat-trend {{ trendClass($stats['productos']['pct']) }}">
                    <i class="{{ trendIcon($stats['productos']['pct']) }}"></i>
                    {{ abs($stats['productos']['pct']) }}% vs mes anterior
                </span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon icon-yellow">
                        <i class="fa-solid fa-sack-dollar"></i>
                    </div>
                    <p class="stat-label">Ventas del mes</p>
                </div>
                <div class="stat-value">Bs {{ number_format($stats['ventas']['valor'], 2) }}</div>
                <span class="stat-trend {{ trendClass($stats['ventas']['pct']) }}">
                    <i class="{{ trendIcon($stats['ventas']['pct']) }}"></i>
                    {{ abs($stats['ventas']['pct']) }}% vs mes anterior
                </span>
            </div>
        </div>
    </div>

    {{-- ===== FILA DE MÓDULOS ===== --}}
    <div class="row g-4">
        {{-- ===== COLUMNA: SEGURIDAD ===== --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="module-card">
                <div class="module-header">
                    <i class="fa-solid fa-shield-halved"></i>
                    <h4>SEGURIDAD</h4>
                </div>
                <div class="module-body">
                    <a href="{{ route('users.index') }}" class="module-item">
                        <div class="module-item-icon">
                            <i class="fa-solid fa-user"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Usuarios</h6>
                            <p>Gestiona los usuarios del sistema y sus accesos.</p>
                        </div>
                    </a>

                    <a href="{{ route('roles.index') }}" class="module-item">
                        <div class="module-item-icon green">
                            <i class="fa-solid fa-user-shield"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Roles</h6>
                            <p>Define y administra los roles y permisos.</p>
                        </div>
                    </a>

                    <a href="{{ route('role-assignments.index') }}" class="module-item">
                        <div class="module-item-icon yellow">
                            <i class="fa-solid fa-id-card-clip"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Asignación de roles</h6>
                            <p>Asigna roles a usuarios y gestiona sus permisos.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== COLUMNA: PARAMETRIZACIÓN ===== --}}
        <div class="col-12 col-md-12 col-lg-6">
            <div class="module-card">
                <div class="module-header">
                    <i class="fa-solid fa-gear"></i>
                    <h4>PARAMETRIZACIÓN</h4>
                </div>
                <div class="module-body module-body-grid">
                    <a href="{{ route('clientes.index') }}" class="module-item">
                        <div class="module-item-icon">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Clientes</h6>
                            <p>Gestiona la información de los clientes.</p>
                        </div>
                    </a>

                    <a href="{{ route('unidad-medidas.index') }}" class="module-item">
                        <div class="module-item-icon green">
                            <i class="fa-solid fa-ruler-combined"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Unidades de medida</h6>
                            <p>Configura las unidades de medida del sistema.</p>
                        </div>
                    </a>

                    <a href="{{ route('insumos.index') }}" class="module-item">
                        <div class="module-item-icon yellow">
                            <i class="fa-solid fa-boxes-stacked"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Insumos</h6>
                            <p>Administra los insumos y materiales.</p>
                        </div>
                    </a>

                    <a href="{{ route('productos.index') }}" class="module-item">
                        <div class="module-item-icon blue">
                            <i class="fa-solid fa-drumstick-bite"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Productos</h6>
                            <p>Gestiona los productos y su catálogo.</p>
                        </div>
                    </a>

                    <a href="{{ route('productos-composicion.index') }}" class="module-item">
                        <div class="module-item-icon purple">
                            <i class="fa-solid fa-diagram-project"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Producto-Composición</h6>
                            <p>Define la composición de los productos.</p>
                        </div>
                    </a>

                    <a href="{{ route('regla-conversiones.index') }}" class="module-item">
                        <div class="module-item-icon teal">
                            <i class="fa-solid fa-right-left"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Reglas de conversión</h6>
                            <p>Configura reglas de conversión entre unidades.</p>
                        </div>
                    </a>

                    <a href="{{ route('tipo-productos.index') }}" class="module-item">
                        <div class="module-item-icon orange">
                            <i class="fa-solid fa-tags"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Tipo de productos</h6>
                            <p>Gestiona los tipos y categorías de productos.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- ===== COLUMNA: TRANSACCIONAL ===== --}}
        <div class="col-12 col-md-6 col-lg-3">
            <div class="module-card">
                <div class="module-header">
                    <i class="fa-solid fa-file-invoice"></i>
                    <h4>TRANSACCIONAL</h4>
                </div>
                <div class="module-body">
                    <a href="{{ route('ingreso-insumos.index') }}" class="module-item">
                        <div class="module-item-icon">
                            <i class="fa-solid fa-download"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Ingresos de insumos</h6>
                            <p>Registra el ingreso de insumos al inventario.</p>
                        </div>
                    </a>

                    <a href="{{ route('orden-producciones.index') }}" class="module-item">
                        <div class="module-item-icon green">
                            <i class="fa-solid fa-clipboard-list"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Orden de producción</h6>
                            <p>Crea y gestiona órdenes de producción.</p>
                        </div>
                    </a>

                    <a href="{{ route('producciones.index') }}" class="module-item">
                        <div class="module-item-icon yellow">
                            <i class="fa-solid fa-industry"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Producción</h6>
                            <p>Gestiona el proceso de producción.</p>
                        </div>
                    </a>

                    <a href="{{ route('ventas.index') }}" class="module-item">
                        <div class="module-item-icon blue">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                        <div class="module-item-text">
                            <h6>Ventas</h6>
                            <p>Registra y administra las ventas.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
