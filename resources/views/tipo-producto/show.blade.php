@extends('layouts.crud')

@section('title', 'Detalle de Tipo de Producto')

@section('content')
    <x-crud.page-header
        title="Ficha de tipo de producto"
        :subtitle="'Detalle de ' . $tipoProducto->nombre"
    >
        <x-slot:actions>
            <a href="{{ route('tipo-productos.edit', $tipoProducto) }}" class="btn btn-app-outline">
                <i class="fa-solid fa-pen me-1"></i> Editar
            </a>
            <a href="{{ route('tipo-productos.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Nombre</small>
                        <strong class="fs-5">{{ $tipoProducto->nombre }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Descripcion</small>
                        <strong class="fs-5 text-accent">{{ $tipoProducto->descripcion ?: '-' }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge rounded-pill {{ (int) $tipoProducto->estado === 1 ? 'badge-state' : 'text-bg-secondary' }} mt-2">
                            {{ (int) $tipoProducto->estado === 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
