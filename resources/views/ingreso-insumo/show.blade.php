@extends('layouts.crud')

@section('title', 'Detalle de Ingreso')

@section('content')
    <x-crud.page-header title="Ficha de ingreso" :subtitle="'Detalle del ingreso del ' . $ingreso->fecha_ingreso?->format('d/m/Y')">
        <x-slot:actions>
            <a href="{{ route('ingreso-insumos.edit', $ingreso) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
            <a href="{{ route('ingreso-insumos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Fecha de ingreso</small><strong class="fs-5">{{ $ingreso->fecha_ingreso?->format('d/m/Y') }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Descripción</small><strong class="fs-5">{{ $ingreso->descripcion ?? '-' }}</strong></div></div>
            </div>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-body table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($ingreso->detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->insumo?->nombre ?? '-' }}</td>
                            <td>{{ $detalle->cantidad }}</td>
                            <td>S/ {{ number_format((float) $detalle->precio_unitario, 2) }}</td>
                            <td>S/ {{ number_format((float) ($detalle->cantidad * $detalle->precio_unitario), 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
