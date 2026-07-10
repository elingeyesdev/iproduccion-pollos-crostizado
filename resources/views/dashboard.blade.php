@extends('layouts.crud')

@section('title', 'Dashboard')

@section('content')
    <x-crud.page-header title="Dashboard" subtitle="Accesos rápidos a los módulos principales del sistema." />

    <div class="card card-elevated mb-4">
        <div class="card-body d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
            <div>
                <h5 class="mb-1 section-title">Bienvenido al panel</h5>
                <p class="mb-0 text-muted">Selecciona un módulo para gestionar tu información.</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="btn btn-app-outline">
                <i class="fa-solid fa-user-gear me-1"></i> Mi perfil
            </a>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('users.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-users me-2"></i> Usuarios
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('roles.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-user-shield me-2"></i> Roles
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('role-assignments.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-id-card-clip me-2"></i> Asignación de roles
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('clientes.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-address-book me-2"></i> Clientes
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('unidad-medidas.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-ruler-combined me-2"></i> Unidades de medida
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('insumos.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-boxes-stacked me-2"></i> Insumos
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('ingreso-insumos.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-truck-ramp-box me-2"></i> Ingresos de insumos
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('productos.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-drumstick-bite me-2"></i> Productos
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('productos-composicion.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-layer-group me-2"></i> Producto-Composición
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('regla-conversiones.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-calculator me-2"></i> Reglas de conversión
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('tipo-productos.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-layer-group me-2"></i> Tipo de productos
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('orden-producciones.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-clipboard-list me-2"></i> Orden de producción
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('producciones.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-fire-burner me-2"></i> Producción
            </a>
        </div>
        
        <div class="col-12 col-md-6 col-lg-4">
            <a href="{{ route('ventas.index') }}" class="btn btn-app-primary w-100 py-3 text-start">
                <i class="fa-solid fa-cash-register me-2"></i> Ventas
            </a>
        </div>
    </div>
@endsection
