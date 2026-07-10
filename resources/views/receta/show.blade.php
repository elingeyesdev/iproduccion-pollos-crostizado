@extends('layouts.crud')

@section('title', 'Detalle de Receta')

@section('content')
    <x-crud.page-header title="Detalle de Receta" :subtitle="$receta->nombre">
        <x-slot:actions>
            <a href="{{ route('recetas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('recetas.edit', $receta) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="row fade-slide">
        <div class="col-md-5">
            <div class="card card-elevated mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-circle-info me-2"></i>Rendimiento Configurado</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Producto Final</small>
                        <span class="fw-bold fs-5 text-dark">{{ $receta->producto?->nombre }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Cantidad de Insumo Base</small>
                        <span class="fw-semibold fs-5 text-dark">{{ number_format($receta->cantidad_base, 2) }} unidad(es)</span>
                    </div>

                    <div class="mb-0">
                        <small class="text-muted d-block">Rendimiento Esperado</small>
                        <span class="fw-bold fs-4 text-success">{{ number_format($receta->rendimiento_esperado, 2) }} porciones/piezas</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card card-elevated">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-list-check me-2"></i>Insumos Requeridos</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Insumo</th>
                                    <th class="text-center">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receta->insumos as $insumo)
                                    <tr>
                                        <td class="fw-semibold text-dark">{{ $insumo->nombre }}</td>
                                        <td class="text-center fw-bold text-danger">{{ number_format($insumo->pivot->cantidad_requerida, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
