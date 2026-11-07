@extends('layouts.crud')

@section('title', 'KPI de Producción')

@section('content')
    <x-crud.page-header title="KPI de Producción" subtitle="Indicadores de producción, rendimiento, mermas y órdenes del período.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    @include('kpi.navegacion', ['activo' => 'produccion'])
    @include('kpi.filtro-fechas', ['ruta' => 'kpi.produccion'])

    @if ($ordenesPendientes > 0)
        <div class="alert alert-warning">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>
            Hay {{ $ordenesPendientes }} orden(es) de producción pendientes de ejecutar.
        </div>
    @endif

    @php
        $tarjetas = [
            ['cantidad_obtenida', 'Producto obtenido', number_format($actual['cantidad_obtenida'], 2), 'fa-solid fa-drumstick-bite', true, 'Unidades producidas en el período.'],
            ['rendimiento', 'Rendimiento', number_format($actual['rendimiento'], 2) . ' x', 'fa-solid fa-gauge-high', true, 'Producto obtenido ÷ insumo consumido (' . number_format($actual['cantidad_insumo'], 2) . ').'],
            ['pct_merma', 'Porcentaje de merma', number_format($actual['pct_merma'], 1) . '%', 'fa-solid fa-trash-can', false, 'Mermas (' . number_format($actual['mermas'], 2) . ') ÷ producto obtenido.'],
            [null, 'Cumplimiento de órdenes', number_format($actual['cumplimiento'], 0) . '%', 'fa-solid fa-clipboard-check', true, $actual['ordenes_ejecutadas'] . ' de ' . $actual['ordenes_programadas'] . ' órdenes ejecutadas.'],
        ];
    @endphp

    @include('kpi.tarjetas')
@endsection
