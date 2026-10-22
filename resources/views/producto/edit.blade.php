@extends('layouts.crud')

@section('title', 'Editar Producto')

@section('content')
    <x-crud.page-header title="Editar producto" subtitle="Modifica los datos del producto.">
        <x-slot:actions>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('productos.update', $producto) }}" enctype="multipart/form-data" novalidate>
                @csrf
                @method('PUT')
                @include('producto.form', ['submitLabel' => 'Actualizar producto'])
            </form>
        </div>
    </div>
@endsection
