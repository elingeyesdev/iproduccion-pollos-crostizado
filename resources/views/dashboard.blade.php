@extends('layouts.crud')

@section('title', 'Dashboard')

@push('styles')
    @vite('resources/css/dashboard.css')
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
