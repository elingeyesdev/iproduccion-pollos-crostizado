@extends('layouts.crud')

@section('title', 'Cantidad Stock')

@section('content')
    <x-crud.page-header title="Cantidad Stock" subtitle="Alerta de insumos bajo el mínimo y stock actual de productos.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body table-responsive">
            <h5 class="fw-bold mb-1">Insumos bajo stock mínimo</h5>
            <p class="text-muted small mb-3">Insumos cuya cantidad disponible ya llegó o pasó su stock mínimo configurado.</p>
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Unidad</th>
                        <th class="text-end">Stock actual</th>
                        <th class="text-end">Stock mínimo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($insumosBajoMinimo as $insumo)
                        <tr>
                            <td class="fw-semibold">{{ $insumo->nombre }}</td>
                            <td>{{ $insumo->unidadMedida?->abreviatura ?? $insumo->unidadMedida?->nombre ?? '-' }}</td>
                            <td class="text-end"><span class="badge bg-danger">{{ number_format((float) $insumo->stock_actual, 2) }}</span></td>
                            <td class="text-end">{{ number_format((float) $insumo->stock_minimo, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-3 text-muted">Ningún insumo está bajo su stock mínimo.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-body table-responsive">
            <h5 class="fw-bold mb-1">Stock de productos</h5>
            <p class="text-muted small mb-3">Los productos no tienen un stock mínimo configurado; se muestran ordenados de menor a mayor cantidad disponible.</p>
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th class="text-end">Stock actual</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td>{{ $producto->tipoProducto?->nombre ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $producto->cantidad_actual, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-3 text-muted">No hay productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
