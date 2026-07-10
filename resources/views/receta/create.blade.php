@extends('layouts.crud')

@section('title', 'Nueva Receta')

@section('content')
    <x-crud.page-header title="Crear Receta" subtitle="Define los rendimientos y requerimientos de insumos para la producción.">
        <x-slot:actions>
            <a href="{{ route('recetas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated fade-slide">
        <div class="card-body">
            <form method="POST" action="{{ route('recetas.store') }}" role="form">
                @csrf
                @include('receta.form', ['submitLabel' => 'Guardar Receta'])
            </form>
        </div>
    </div>
@endsection
