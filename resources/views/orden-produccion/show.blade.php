@extends('layouts.crud')

@section('title', 'Detalle Orden de Producción')

@section('content')
    <x-crud.page-header title="Detalle de la orden de producción" subtitle="Información de la orden registrada.">
        <x-slot:actions>
            <a href="{{ route('orden-producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('orden-producciones.edit', $ordenProduccion) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Número de orden</dt>
                <dd class="col-sm-9">{{ $ordenProduccion->numero_orden }}</dd>

                <dt class="col-sm-3">Fecha programada</dt>
                <dd class="col-sm-9">{{ $ordenProduccion->fecha_programada ? \Illuminate\Support\Carbon::parse($ordenProduccion->fecha_programada)->format('d/m/Y') : '-' }}</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    <span class="badge rounded-pill {{ (int) $ordenProduccion->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                        {{ (int) $ordenProduccion->estado === 1 ? 'Activo' : 'Inactivo' }}
                    </span>
                </dd>

                <dt class="col-sm-3">Receta / Regla de conversión</dt>
                <dd class="col-sm-9">
                    @if ($ordenProduccion->reglaConversion)
                        <span class="fw-semibold">{{ $ordenProduccion->reglaConversion->insumo?->nombre }}</span>
                        @if ($ordenProduccion->reglaConversion->recetaConversiones->isNotEmpty())
                            + {{ $ordenProduccion->reglaConversion->recetaConversiones->map(fn ($d) => $d->insumo?->nombre)->filter()->implode(', ') }}
                        @endif
                        &rarr; {{ $ordenProduccion->reglaConversion->producto?->nombre ?? $ordenProduccion->reglaConversion->producto_resultante }}
                    @else
                        <span class="text-danger small">Sin receta asignada</span>
                    @endif
                </dd>

                <dt class="col-sm-3">Insumos planificados</dt>
                <dd class="col-sm-9">{{ $ordenProduccion->insumos->count() }} insumos</dd>

                <dt class="col-sm-3">Estado de producción</dt>
                <dd class="col-sm-9">
                    @if ((int) ($ordenProduccion->insumos_pendientes_count ?? 0) > 0)
                        <span class="badge bg-warning text-dark">Pendiente de ejecutar</span>
                    @else
                        <span class="badge bg-success">Producción ejecutada</span>
                    @endif
                </dd>
            </dl>
        </div>
    </div>

    @php
        $detallesInsumos = $ordenProduccion->insumos ?? collect();
    @endphp

    <div class="card card-elevated mt-4">
        <div class="card-body table-responsive">
            <h5 class="fw-bold mb-3">Insumos de la orden</h5>
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Unidad</th>
                        <th class="text-end">Stock actual</th>
                        <th class="text-end">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($detallesInsumos as $detalle)
                        <tr>
                            <td class="fw-semibold">{{ $detalle->insumo?->nombre ?? '-' }}</td>
                            <td>{{ $detalle->insumo?->unidadMedida?->abreviatura ?? $detalle->insumo?->unidadMedida?->nombre ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) ($detalle->insumo?->stock_actual ?? 0), 2) }}</td>
                            <td class="text-end">{{ number_format((float) $detalle->cantidad, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3 text-muted">Sin insumos registrados en esta orden.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
