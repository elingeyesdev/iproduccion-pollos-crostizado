@extends('layouts.crud')

@section('title', 'Detalle de Regla de Conversión')

@section('content')
    <x-crud.page-header title="Detalle de Regla" subtitle="Información técnica de la regla de conversión.">
        <x-slot:actions>
            <a href="{{ route('regla-conversiones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('regla-conversiones.edit', $reglaConversion) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="row fade-slide">
        <!-- Entrada -->
        <div class="col-md-6 mb-4">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-danger"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Entrada (Insumo)</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Insumo</small>
                        <span class="fw-bold fs-5 text-dark">{{ $reglaConversion->insumo?->nombre }}</span>
                    </div>

                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted d-block">Cantidad</small>
                            <span class="fw-semibold fs-5">{{ number_format($reglaConversion->cantidad_entrada, 2) }}</span>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Unidad</small>
                            <span class="fw-semibold fs-5">{{ $reglaConversion->unidadMedidaEntrada?->nombre }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Salida -->
        <div class="col-md-6 mb-4">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-success"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Salida (Producto Resultante)</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Producto</small>
                        <span class="fw-bold fs-5 text-dark">{{ $reglaConversion->producto?->nombre }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Unidad Resultante</small>
                        <span class="fw-semibold fs-5">{{ $reglaConversion->unidadMedidaResultante?->nombre }}</span>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">Observación General</small>
                        <p class="mb-0">{{ $reglaConversion->observacion ?: 'Sin observaciones' }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Receta de Conversión -->
        <div class="col-md-12 mb-4">
            <div class="card card-elevated">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-list-check me-2"></i>Receta de Conversión (Detalles)</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Insumo</th>
                                    <th class="text-center">Cantidad</th>
                                    <th>Unidad</th>
                                    <th>Observación</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reglaConversion->recetaConversiones as $detalle)
                                    <tr>
                                        <td class="fw-semibold">{{ $detalle->insumo?->nombre }}</td>
                                        <td class="text-center fw-bold">{{ number_format($detalle->cantidad, 2) }}</td>
                                        <td>{{ $detalle->unidadMedida?->nombre }}</td>
                                        <td><small>{{ $detalle->observacion }}</small></td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted">No hay detalles registrados para esta receta.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Rendimiento -->
        <div class="col-md-12">
            <div class="card card-elevated">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-info"><i class="fa-solid fa-calculator me-2"></i>Parámetros de Rendimiento</h5>
                    
                    <div class="row text-center">
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block mb-1">Rendimiento Mínimo</small>
                                <span class="fw-bold fs-3 text-primary">{{ number_format($reglaConversion->rendimiento_minimo, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block mb-1">Rendimiento Máximo</small>
                                <span class="fw-bold fs-3 text-primary">{{ number_format($reglaConversion->rendimiento_maximo, 2) }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded bg-light">
                                <small class="text-muted d-block mb-1">Tipo de Conversión</small>
                                <span class="badge fs-5 {{ $reglaConversion->tipo_conversion === 'Estática' ? 'bg-primary' : 'bg-info' }}">
                                    {{ $reglaConversion->tipo_conversion }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
