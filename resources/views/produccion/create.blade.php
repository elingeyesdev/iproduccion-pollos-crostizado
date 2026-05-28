@extends('layouts.crud')

@section('title', 'Crear Producción')

@section('content')
    <x-crud.page-header title="Crear producción" subtitle="Registra una nueva ejecución de producción.">
        <x-slot:actions>
            <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('producciones.store') }}" novalidate>
                @csrf
                @include('produccion.form', ['produccionRecord' => $produccion, 'submitLabel' => 'Guardar producción'])
            </form>
        </div>
    </div>
@endsection
