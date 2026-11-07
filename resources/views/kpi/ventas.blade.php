@extends('layouts.crud')

@section('title', 'KPI de Administración de Ventas')

@section('content')
    <x-crud.page-header title="KPI de Administración de Ventas" subtitle="Indicadores del desempeño comercial para el período seleccionado.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    @include('kpi.navegacion', ['activo' => 'ventas'])
    @include('kpi.filtro-fechas', ['ruta' => 'kpi.ventas'])

    @php
        $tarjetas = [
            ['ingresos', 'Ingresos por ventas', 'Bs ' . number_format($actual['ingresos'], 2), 'fa-solid fa-sack-dollar', true, 'Total de ventas activas del período.'],
            ['num_ventas', 'Número de ventas', number_format($actual['num_ventas']), 'fa-solid fa-receipt', true, 'Cantidad de ventas registradas.'],
            ['ticket_promedio', 'Ticket promedio', 'Bs ' . number_format($actual['ticket_promedio'], 2), 'fa-solid fa-tag', true, 'Ingresos divididos entre el número de ventas.'],
            ['clientes_atendidos', 'Clientes atendidos', number_format($actual['clientes_atendidos']), 'fa-solid fa-users', true, 'Clientes distintos que realizaron compras.'],
        ];
    @endphp

    @include('kpi.tarjetas')
@endsection
