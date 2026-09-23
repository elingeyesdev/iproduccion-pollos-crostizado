@extends('layouts.crud')

@section('title', 'Detalle Producción')

@section('content')
    <x-crud.page-header title="Detalle de producción" subtitle="Información general y relaciones de la producción.">
        <x-slot:actions>
            <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Orden de producción</dt>
                <dd class="col-sm-9">{{ $produccion->ordenProduccion?->numero_orden ?? '-' }}</dd>

                <dt class="col-sm-3">Fecha programada</dt>
                <dd class="col-sm-9">{{ $produccion->ordenProduccion?->fecha_programada ? \Illuminate\Support\Carbon::parse($produccion->ordenProduccion->fecha_programada)->format('d/m/Y') : '-' }}</dd>

                <dt class="col-sm-3">Insumo de detalle</dt>
                <dd class="col-sm-9">{{ $produccion->detalle_orden?->insumo?->nombre ?? '-' }}</dd>

                <dt class="col-sm-3">Regla aplicada</dt>
                <dd class="col-sm-9">
                    @if($produccion->reglaConversion)
                        {{ $produccion->reglaConversion->insumo?->nombre ?? 'Insumo' }}
                        ->
                        {{ $produccion->producto?->nombre ?? $produccion->reglaConversion->producto_resultante ?? 'Resultado' }}
                        ({{ $produccion->reglaConversion->tipo_conversion }})
                    @else
                        -
                    @endif
                </dd>

                <dt class="col-sm-3">Producto obtenido</dt>
                <dd class="col-sm-9">{{ $produccion->producto?->nombre ?? '-' }}</dd>

                <dt class="col-sm-3">Cantidad de insumo</dt>
                <dd class="col-sm-9">{{ number_format((float) $produccion->cantidad_insumo, 2) }}</dd>

                <dt class="col-sm-3">Cantidad obtenida</dt>
                <dd class="col-sm-9">{{ number_format((float) $produccion->cantidad_obtenida, 2) }}</dd>

                <dt class="col-sm-3">Mermas registradas</dt>
                <dd class="col-sm-9">{{ $produccion->mermas->count() }}</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">{{ (int) $produccion->estado === 1 ? 'Activo' : 'Inactivo' }}</dd>

                <dt class="col-sm-3">Nivel recursivo</dt>
                <dd class="col-sm-9">{{ (int) ($produccion->nivel ?? 0) }}</dd>
            </dl>
        </div>
    </div>
@endsection
