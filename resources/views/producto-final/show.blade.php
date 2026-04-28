@extends('layouts.crud')

@section('title', 'Detalle de Producto Final')

@section('content')
    <x-crud.page-header title="Ficha de producto final" :subtitle="'Detalle de ' . $productoFinal->nombre">
        <x-slot:actions>
            <a href="{{ route('producto-finals.edit', $productoFinal) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
            <a href="{{ route('producto-finals.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Nombre</small><strong class="fs-5">{{ $productoFinal->nombre }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Descripción</small><strong class="fs-5">{{ $productoFinal->descripcion }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Precio</small><strong class="fs-5">S/ {{ number_format((float) $productoFinal->precio, 2) }}</strong></div></div>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Estado</small>
                        <span class="badge rounded-pill {{ (int) $productoFinal->estado === 1 ? 'badge-state' : 'text-bg-secondary' }} mt-2">
                            {{ (int) $productoFinal->estado === 1 ? 'Activo' : 'Inactivo' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
