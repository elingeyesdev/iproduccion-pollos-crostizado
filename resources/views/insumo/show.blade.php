@extends('layouts.crud')

@section('title', 'Detalle de Insumo')

@section('content')
    <x-crud.page-header title="Ficha de insumo" :subtitle="'Detalle de ' . $insumo->nombre">
        <x-slot:actions>
            <a href="{{ route('insumos.edit', $insumo) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
            <a href="{{ route('insumos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Nombre</small><strong class="fs-5">{{ $insumo->nombre }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Unidad de medida</small><strong class="fs-5">{{ $insumo->unidadMedida?->nombre ?? '-' }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Precio unitario</small><strong class="fs-5">S/ {{ number_format((float) $insumo->precioUnitario, 2) }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Stock actual</small><strong class="fs-5">{{ (int) $insumo->stockActual }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Stock mínimo</small><strong class="fs-5">{{ (int) $insumo->stockMinimo }}</strong></div></div>
            </div>
        </div>
    </div>
@endsection
