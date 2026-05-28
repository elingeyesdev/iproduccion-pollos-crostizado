@extends('layouts.crud')

@section('title', 'Crear Orden de Producción')

@section('content')
    <x-crud.page-header title="Crear orden de producción" subtitle="Registra una nueva orden de producción.">
        <x-slot:actions>
            <a href="{{ route('orden-producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('orden-producciones.store') }}" novalidate>
                @csrf
                @include('orden-produccion.form', ['submitLabel' => 'Guardar orden'])
            </form>
        </div>
    </div>
@endsection
