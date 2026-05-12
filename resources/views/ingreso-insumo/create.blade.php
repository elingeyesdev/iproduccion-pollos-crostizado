@extends('layouts.crud')

@section('title', 'Nuevo Ingreso')

@section('content')
    <x-crud.page-header title="Nuevo ingreso de insumos" subtitle="Registra una cabecera con varios insumos y su costo por detalle.">
        <x-slot:actions>
            <a href="{{ route('ingreso-insumos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('ingreso-insumos.store') }}" novalidate>
                @csrf
                @include('ingreso-insumo.form', ['submitLabel' => 'Guardar ingreso'])
            </form>
        </div>
    </div>
@endsection
