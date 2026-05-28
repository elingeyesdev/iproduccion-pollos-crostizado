@extends('layouts.crud')

@section('title', 'Detalle Componente de Producto')

@section('content')
    <x-crud.page-header title="Detalle del componente" subtitle="Información del componente registrado.">
        <x-slot:actions>
            <a href="{{ route('componente-productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('componente-productos.edit', $componenteProducto) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">{{ $componenteProducto->nombre }}</dd>

                <dt class="col-sm-3">Equivalencia</dt>
                <dd class="col-sm-9">{{ $componenteProducto->equivalencia ?? '-' }}</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    <span class="badge rounded-pill {{ (int) $componenteProducto->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                        {{ (int) $componenteProducto->estado === 1 ? 'Activo' : 'Inactivo' }}
                    </span>
                </dd>

                <dt class="col-sm-3">Creado</dt>
                <dd class="col-sm-9">{{ $componenteProducto->created_at?->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>
    </div>
@endsection
