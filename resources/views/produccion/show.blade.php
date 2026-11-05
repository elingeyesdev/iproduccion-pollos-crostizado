@extends('layouts.crud')

@section('title', 'Detalle Producción')

@section('content')
    <x-crud.page-header title="Detalle de producción" subtitle="Insumos consumidos, producto obtenido y mermas del lote ejecutado.">
        <x-slot:actions>
            <a href="{{ route('producciones.pdf', $produccion) }}" target="_blank" rel="noopener" class="btn btn-danger"><i class="fa-solid fa-file-pdf me-1"></i> Ver PDF</a>
            <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-header fw-semibold">Datos generales</div>
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Orden de producción</dt>
                <dd class="col-sm-9">{{ $orden?->numero_orden ?? '-' }}</dd>

                <dt class="col-sm-3">Fecha programada</dt>
                <dd class="col-sm-9">{{ $orden?->fecha_programada ? \Illuminate\Support\Carbon::parse($orden->fecha_programada)->format('d/m/Y') : '-' }}</dd>

                <dt class="col-sm-3">Fecha de ejecución</dt>
                <dd class="col-sm-9">{{ $produccion->created_at?->format('d/m/Y H:i') ?? '-' }}</dd>

                <dt class="col-sm-3">Regla aplicada</dt>
                <dd class="col-sm-9">
                    @if($produccion->reglaConversion)
                        {{ $produccion->reglaConversion->insumo?->nombre ?? 'Insumo' }}
                        ->
                        {{ $produccion->producto?->nombre ?? 'Resultado' }}
                        ({{ $produccion->reglaConversion->tipo_conversion }})
                    @else
                        -
                    @endif
                </dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">{{ (int) $produccion->estado === 1 ? 'Activo' : 'Inactivo' }}</dd>
            </dl>
        </div>
    </div>

    <div class="card card-elevated mb-4">
        <div class="card-header fw-semibold">
            Insumos consumidos
            <span class="badge bg-success ms-2"><i class="fa-solid fa-check me-1"></i>Descontados del stock al ejecutar</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Insumo</th>
                            <th class="text-end">Cantidad consumida</th>
                            <th>Unidad</th>
                            <th class="text-end">Stock actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($insumosConsumidos as $i => $item)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>
                                    {{ $item['insumo'] }}
                                    @if($item['principal'])
                                        <span class="badge bg-primary ms-1">Principal</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($item['cantidad'], 2) }}</td>
                                <td>{{ $item['unidad'] }}</td>
                                <td class="text-end">{{ number_format($item['stock_actual'], 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card card-elevated h-100">
                <div class="card-header fw-semibold">Resultado de producción</div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-7">Producto obtenido</dt>
                        <dd class="col-5">{{ $produccion->producto?->nombre ?? '-' }}</dd>

                        <dt class="col-7">Cantidad producida</dt>
                        <dd class="col-5">{{ number_format($resumen['cantidad_obtenida'], 2) }} {{ $resumen['unidad_salida'] }}</dd>

                        <dt class="col-7">Total merma</dt>
                        <dd class="col-5 text-danger">- {{ number_format($resumen['total_merma'], 2) }} {{ $resumen['unidad_salida'] }}</dd>

                        <dt class="col-7">Cantidad neta (sin merma)</dt>
                        <dd class="col-5 fw-bold">{{ number_format($resumen['cantidad_neta'], 2) }} {{ $resumen['unidad_salida'] }}</dd>

                        <dt class="col-7">Porcentaje de merma</dt>
                        <dd class="col-5">{{ number_format($resumen['porcentaje_merma'], 2) }} %</dd>

                        @if($resumen['rendimiento'] !== null)
                            <dt class="col-7">Rendimiento por {{ $resumen['insumo_principal']['unidad'] }} de {{ $resumen['insumo_principal']['insumo'] }}</dt>
                            <dd class="col-5">{{ number_format($resumen['rendimiento'], 2) }} {{ $resumen['unidad_salida'] }}</dd>
                        @endif
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card card-elevated h-100">
                <div class="card-header fw-semibold">Mermas registradas ({{ $mermas->count() }})</div>
                <div class="card-body p-0">
                    @if($mermas->isEmpty())
                        <p class="text-muted p-3 mb-0">No se registraron mermas para este lote.</p>
                    @else
                        <table class="table table-sm mb-0">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th class="text-end">Cantidad</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mermas as $merma)
                                    <tr>
                                        <td>{{ $merma->created_at?->format('d/m/Y H:i') }}</td>
                                        <td class="text-end">{{ number_format((float) $merma->cantidad, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
