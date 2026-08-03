@extends('layouts.crud')

@section('title', 'Ejecutar producción por lote')

@section('content')
    <x-crud.page-header title="Ejecutar producción por lote" subtitle="Descuenta todos los insumos pendientes de la orden {{ $ordenProduccion->numero_orden }} de una sola vez.">
        <x-slot:actions>
            <a href="{{ route('orden-producciones.show', $ordenProduccion) }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

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
                        <th>Regla de conversión</th>
                        <th>Producto resultante</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($detalles as $linea)
                        @php($detalle = $linea['detalle'])
                        <tr>
                            <td class="fw-semibold">{{ $detalle->insumo?->nombre ?? '-' }}</td>
                            <td>{{ $detalle->insumo?->unidadMedida?->abreviatura ?? $detalle->insumo?->unidadMedida?->nombre ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $detalle->cantidad, 2) }}</td>
                            <td class="text-end">{{ number_format((float) ($detalle->insumo?->stock_actual ?? 0), 2) }}</td>
                            <td>
                                @if ($linea['regla'])
                                    <span class="badge bg-light text-dark border">{{ $linea['regla']->tipo_conversion }}</span>
                                @else
                                    <span class="badge bg-danger">Sin regla activa</span>
                                @endif
                            </td>
                            <td>
                                @if ($linea['producto'])
                                    {{ $linea['producto']->nombre }}
                                @else
                                    <span class="text-danger small">No se pudo determinar el producto</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">Esta orden no tiene insumos pendientes de producción.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if ($detalles->isNotEmpty() && !$loteEjecutable)
        <div class="alert alert-warning">
            No se puede ejecutar el lote todavía: revisa que todos los insumos tengan una regla de conversión activa y que todas las líneas resuelvan al mismo producto resultante.
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
                            <input type="number" step="0.01" min="0.01" name="cantidad_obtenida" id="cantidad_obtenida" class="form-control @error('cantidad_obtenida') is-invalid @enderror" value="{{ old('cantidad_obtenida') }}" placeholder="Ej: 40" required>
                            @error('cantidad_obtenida')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
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
