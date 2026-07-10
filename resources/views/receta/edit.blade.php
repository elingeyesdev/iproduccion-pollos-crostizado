@extends('layouts.crud')

@section('title', 'Editar Receta')

@section('content')
    <x-crud.page-header title="Editar Receta" :subtitle="'Modificando: ' . $receta->nombre">
        <x-slot:actions>
            <a href="{{ route('recetas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated fade-slide">
        <div class="card-body">
            <form method="POST" action="{{ route('recetas.update', $receta) }}" role="form">
                @csrf
                @method('PUT')
                @include('receta.form', ['submitLabel' => 'Actualizar Receta'])
            </form>
        </div>
    </div>
@endsection
