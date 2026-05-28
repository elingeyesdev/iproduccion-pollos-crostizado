@extends('layouts.crud')

@section('title', 'Detalle Ingreso de Bebida')

@section('content')
    <x-crud.page-header title="Ficha de ingreso de bebida"
        :subtitle="'Ingreso del ' . (\Illuminate\Support\Carbon::parse($ingresoBebida->fecha_ingreso)->format('d/m/Y'))">
        <x-slot:actions>
            <a href="{{ route('ingreso-bebidas.edit', $ingresoBebida) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
            <a href="{{ route('ingreso-bebidas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Nombre</small>
                        <strong class="fs-5">{{ $ingresoBebida->nombre }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Fecha de ingreso</small>
                        <strong class="fs-5">{{ \Illuminate\Support\Carbon::parse($ingresoBebida->fecha_ingreso)->format('d/m/Y') }}</strong>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Descripción</small>
                        <strong class="fs-5">{{ $ingresoBebida->descripcion ?? '-' }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-body table-responsive">
            <h6 class="fw-bold mb-3">Bebidas ingresadas</h6>
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Cantidad ingresada</th>
                        <th>Precio</th>
                        <th>Stock actual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ingresoBebida->productos as $producto)
                        <tr>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td>{{ $producto->tipoProducto?->nombre ?? '-' }}</td>
                            <td>{{ number_format((float) $producto->pivot->cantidad_ingresa, 2) }}</td>
                            <td>S/ {{ number_format((float) $producto->pivot->precio, 2) }}</td>
                            <td>{{ (int) $producto->cantidad_actual }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-3 text-muted">Sin productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
