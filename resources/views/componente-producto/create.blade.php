@extends('layouts.crud')

@section('title', 'Crear Componente de Producto')

@section('content')
    <x-crud.page-header title="Crear componente de producto" subtitle="Registra un nuevo componente.">
        <x-slot:actions>
            <a href="{{ route('componente-productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('componente-productos.store') }}" novalidate>
                @csrf
                @include('componente-producto.form', ['submitLabel' => 'Guardar componente'])
            </form>
        </div>
    </div>
@endsection
