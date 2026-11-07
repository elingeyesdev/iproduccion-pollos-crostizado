@extends('layouts.crud')

@section('title', 'KPI de Finanzas y Rentabilidad')

@section('content')
    <x-crud.page-header title="KPI de Administración Financiera y Rentabilidad" subtitle="Indicadores de costos, rentabilidad y valoración del inventario.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    @include('kpi.navegacion', ['activo' => 'finanzas'])
    @include('kpi.filtro-fechas', ['ruta' => 'kpi.finanzas'])

    @php
        $tarjetas = [
            ['gasto_insumos', 'Compras de insumos', 'Bs ' . number_format($actual['gasto_insumos'], 2), 'fa-solid fa-truck-ramp-box', false, 'Cantidad por precio unitario de los ingresos de insumos.'],
            ['resultado_bruto', 'Resultado bruto', 'Bs ' . number_format($actual['resultado_bruto'], 2), 'fa-solid fa-scale-balanced', true, 'Ingresos por ventas menos compras de insumos.'],
            [null, 'Margen bruto', number_format($actual['margen_bruto'], 1) . '%', 'fa-solid fa-chart-pie', true, 'Resultado bruto dividido entre ingresos por ventas.'],
            [null, 'Descuentos otorgados', 'Bs ' . number_format($actual['descuentos'], 2), 'fa-solid fa-percent', false, 'Suma de descuentos aplicados en ventas.'],
            [null, 'Valor del inventario', 'Bs ' . number_format($valorInventario, 2), 'fa-solid fa-warehouse', true, 'Stock actual de productos por precio de venta.'],
        ];
    @endphp

    @include('kpi.tarjetas')
@endsection
