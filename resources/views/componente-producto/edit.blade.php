@extends('layouts.crud')

@section('title', 'Editar Componente de Producto')

@section('content')
    <x-crud.page-header title="Editar componente de producto" subtitle="Modifica los datos del componente.">
        <x-slot:actions>
            <a href="{{ route('componente-productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('componente-productos.update', $componenteProducto) }}" novalidate>
                @csrf
                @method('PUT')
                @include('componente-producto.form', ['submitLabel' => 'Actualizar componente'])
            </form>
        </div>
    </div>
@endsection
