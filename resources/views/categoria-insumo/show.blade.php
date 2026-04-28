@extends('layouts.crud')

@section('title', 'Detalle de Categoria')

@section('content')
    <x-crud.page-header
        title="Ficha de categoria"
        :subtitle="'Detalle de ' . $categoriaInsumo->nombre"
    >
        <x-slot:actions>
            <a href="{{ route('categoria-insumos.edit', $categoriaInsumo) }}" class="btn btn-app-outline">
                <i class="fa-solid fa-pen me-1"></i> Editar
            </a>
            <a href="{{ route('categoria-insumos.index') }}" class="btn btn-outline-secondary">
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
                        <strong class="fs-5">{{ $categoriaInsumo->nombre }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge rounded-pill {{ (int) $categoriaInsumo->estado === 1 ? 'badge-state' : 'text-bg-secondary' }} mt-2">
                            {{ (int) $categoriaInsumo->estado === 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded-3 p-3">
                        <small class="text-muted d-block">Descripcion</small>
                        <p class="mb-0">{{ $categoriaInsumo->descripcion }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
