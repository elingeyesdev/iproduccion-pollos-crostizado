@extends('layouts.crud')

@section('title', 'Crear Categoria de Insumo')

@section('content')
    <x-crud.page-header title="Crear categoria de insumo" subtitle="Registra una nueva categoria para tu catalogo.">
        <x-slot:actions>
            <a href="{{ route('categoria-insumos.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al listado
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('categoria-insumos.store') }}" novalidate>
                @csrf
                @include('categoria-insumo.form', ['submitLabel' => 'Guardar categoria'])
            </form>
        </div>
    </div>
@endsection
