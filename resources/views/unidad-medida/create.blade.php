@extends('layouts.crud')

@section('title', 'Crear Unidad de Medida')

@section('content')
    <x-crud.page-header title="Crear unidad de medida" subtitle="Registra una nueva unidad para el catálogo.">
        <x-slot:actions>
            <a href="{{ route('unidad-medidas.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al listado
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('unidad-medidas.store') }}" novalidate>
                @csrf
                @include('unidad-medida.form', ['submitLabel' => 'Guardar unidad'])
            </form>
        </div>
    </div>
@endsection
