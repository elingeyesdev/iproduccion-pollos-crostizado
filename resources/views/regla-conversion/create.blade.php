@extends('layouts.crud')

@section('title', 'Nueva Regla de Conversión')

@section('content')
    <x-crud.page-header title="Crear Regla de Conversión" subtitle="Configura la transformación de insumos a productos.">
        <x-slot:actions>
            <a href="{{ route('regla-conversiones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated fade-slide">
        <div class="card-body">
            <form method="POST" action="{{ route('regla-conversiones.store') }}" role="form">
                @csrf
                @include('regla-conversion.form', ['submitLabel' => 'Guardar Regla'])
            </form>
        </div>
    </div>
@endsection
