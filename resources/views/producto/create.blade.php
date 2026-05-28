@extends('layouts.crud')

@section('title', 'Crear Producto')

@section('content')
    <x-crud.page-header title="Crear producto" subtitle="Registra un nuevo producto.">
        <x-slot:actions>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('productos.store') }}" novalidate>
                @csrf
                @include('producto.form', ['submitLabel' => 'Guardar producto'])
            </form>
        </div>
    </div>
@endsection
