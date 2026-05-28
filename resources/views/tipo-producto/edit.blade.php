@extends('layouts.crud')

@section('title', 'Editar Tipo de Producto')

@section('content')
    <x-crud.page-header
        title="Editar tipo de producto"
        subtitle="Actualiza la informacion de {{ $tipoProducto->nombre }}"
    >
        <x-slot:actions>
            <a href="{{ route('tipo-productos.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al listado
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('tipo-productos.update', $tipoProducto) }}" novalidate>
                @method('PATCH')
                @csrf
                @include('tipo-producto.form', ['submitLabel' => 'Actualizar tipo'])
            </form>
        </div>
    </div>
@endsection
