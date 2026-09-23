@extends('layouts.crud')

@section('title', 'Ejecutar producción')

@section('content')
    <x-crud.page-header title="Ejecutar producción" subtitle="Descuenta todos los insumos pendientes de la orden {{ $ordenProduccion->numero_orden }} de una sola vez, con una sola receta.">
        <x-slot:actions>
            <a href="{{ route('orden-producciones.show', $ordenProduccion) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Receta asignada a esta orden</h5>
            @if ($regla)
                <p class="mb-2">
                    <span class="fw-semibold">{{ $regla->insumo?->nombre }}</span>
                    @if ($regla->recetaConversiones->isNotEmpty())
                        + {{ $regla->recetaConversiones->map(fn ($d) => $d->insumo?->nombre . ' (' . number_format((float) $d->cantidad, 2) . ' ' . ($d->unidadMedida?->abreviatura ?? $d->unidadMedida?->nombre) . ')')->filter()->implode(', ') }}
                    @endif
                    &rarr;
                    @if ($producto)
                        <span class="fw-semibold text-success">{{ $producto->nombre }}</span>
                    @else
                        <span class="text-danger">No se pudo determinar el producto resultante</span>
                    @endif
                </p>
                <span class="badge bg-light text-dark border">Tipo: {{ $regla->tipo_conversion }}</span>
            @else
                <p class="text-danger mb-0">Esta orden no tiene una receta (regla de conversión) asignada. <a href="{{ route('orden-producciones.edit', $ordenProduccion) }}">Editar la orden</a> para asignarle una.</p>
            @endif
        </div>
    </div>

    <div class="card card-elevated mb-4">
        <div class="card-body table-responsive">
            <h5 class="fw-bold mb-3">Insumos pendientes de la orden</h5>
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Unidad</th>
                        <th class="text-end">Cantidad planificada</th>
                        <th class="text-end">Stock actual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($detalles as $detalle)
                        <tr>
                            <td class="fw-semibold">{{ $detalle->insumo?->nombre ?? '-' }}</td>
                            <td>{{ $detalle->insumo?->unidadMedida?->abreviatura ?? $detalle->insumo?->unidadMedida?->nombre ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $detalle->cantidad, 2) }}</td>
                            <td class="text-end">{{ number_format((float) ($detalle->insumo?->stock_actual ?? 0), 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">Esta orden no tiene insumos pendientes de producción.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($detalles->isNotEmpty() && !$loteEjecutable)
        <div class="alert alert-warning">
            No se puede ejecutar todavía: la orden necesita una receta (regla de conversión) asignada que resuelva un producto válido.
        </div>
    @endif

    @if ($detalles->isNotEmpty() && $loteEjecutable)
        <div class="card card-elevated">
            <div class="card-body">
                <form method="POST" action="{{ route('producciones.lote.store', $ordenProduccion) }}" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="cantidad_obtenida" class="form-label fw-semibold">Cantidad obtenida (porciones/unidades del lote)</label>
                            <input type="number" step="0.01" min="{{ $rangoRendimiento['minimo'] }}" max="{{ $rangoRendimiento['maximo'] }}" name="cantidad_obtenida" id="cantidad_obtenida" class="form-control @error('cantidad_obtenida') is-invalid @enderror" value="{{ old('cantidad_obtenida') }}" placeholder="Ej: 40" required>
                            @error('cantidad_obtenida')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Para {{ number_format($cantidadPlanificada, 2) }} planificados, la cantidad debe estar entre {{ number_format($rangoRendimiento['minimo'], 2) }} y {{ number_format($rangoRendimiento['maximo'], 2) }}.</div>
                            <small class="text-muted">Se descuenta cada insumo según lo planificado y el producto sube esta cantidad una sola vez.</small>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <a href="{{ route('orden-producciones.show', $ordenProduccion) }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-play me-1"></i> Ejecutar producción del lote</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
@endsection
