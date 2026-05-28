@extends('layouts.crud')

@section('title', 'Detalle Producción')

@section('content')
    <x-crud.page-header title="Detalle de producción" subtitle="Información general y relaciones de la producción.">
        <x-slot:actions>
            <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('producciones.edit', $produccione) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Orden de producción</dt>
                <dd class="col-sm-9">{{ $produccione->ordenProduccion?->numero_orden ?? '-' }}</dd>

                <dt class="col-sm-3">Fecha programada</dt>
                <dd class="col-sm-9">{{ $produccione->ordenProduccion?->fecha_programada ? \Illuminate\Support\Carbon::parse($produccione->ordenProduccion->fecha_programada)->format('d/m/Y') : '-' }}</dd>

                <dt class="col-sm-3">Cantidad de insumo</dt>
                <dd class="col-sm-9">{{ number_format((float) $produccione->cantidad_insumo, 2) }}</dd>

                <dt class="col-sm-3">Cantidad obtenida</dt>
                <dd class="col-sm-9">{{ number_format((float) $produccione->cantidad_obtenida, 2) }}</dd>

                <dt class="col-sm-3">Insumos relacionados</dt>
                <dd class="col-sm-9">{{ $produccione->insumos->count() }}</dd>

                <dt class="col-sm-3">Mermas registradas</dt>
                <dd class="col-sm-9">{{ $produccione->mermas->count() }}</dd>
            </dl>
        </div>
    </div>
@endsection
