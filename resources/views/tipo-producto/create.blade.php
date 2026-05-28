@extends('layouts.crud')

@section('title', 'Crear Tipo de Producto')

@section('content')
    <x-crud.page-header title="Crear tipo de producto" subtitle="Registra una nueva categoria para tus productos.">
        <x-slot:actions>
            <a href="{{ route('tipo-productos.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al listado
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('tipo-productos.store') }}" novalidate>
                @csrf
                @include('tipo-producto.form', ['submitLabel' => 'Guardar tipo'])
            </form>
        </div>
    </div>
@endsection
