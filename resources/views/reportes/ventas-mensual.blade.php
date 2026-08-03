@extends('layouts.crud')

@section('title', 'Reporte Mensual de Ventas')

@php
    $nombresMeses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre',
    ];
@endphp

@section('content')
    <x-crud.page-header title="Reporte mensual de ventas" subtitle="Resumen y desglose por producto del mes seleccionado.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.ventas-mensual') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label for="mes" class="form-label fw-semibold">Mes</label>
                    <select id="mes" name="mes" class="form-select">
                        @foreach ($nombresMeses as $numero => $nombre)
                            <option value="{{ $numero }}" @selected($numero === $mes)>{{ $nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="anio" class="form-label fw-semibold">Año</label>
                    <input id="anio" type="number" name="anio" value="{{ $anio }}" class="form-control" min="2000" max="2100">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Consultar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card card-elevated">
                <div class="card-body">
                    <p class="text-muted small mb-1">Ventas registradas en {{ $nombresMeses[$mes] }} {{ $anio }}</p>
                    <h3 class="fw-bold mb-0">{{ $totalVentas }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-elevated">
                <div class="card-body">
                    <p class="text-muted small mb-1">Monto total vendido</p>
                    <h3 class="fw-bold mb-0">{{ number_format((float) $totalMonto, 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-body table-responsive">
            <h5 class="fw-bold mb-3">Desglose por producto</h5>
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th class="text-end">Cantidad vendida</th>
                        <th class="text-end">Monto vendido</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productosVendidos as $fila)
                        <tr>
                            <td class="fw-semibold">{{ $fila->producto_nombre }}</td>
                            <td class="text-end">{{ number_format((float) $fila->cantidad_total, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $fila->monto_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center py-4 text-muted">No hay ventas registradas en este período.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
