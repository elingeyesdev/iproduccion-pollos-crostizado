@extends('layouts.crud')

@section('title', 'Crear Ingreso de Bebida')

@section('content')
    <x-crud.page-header title="Crear ingreso de bebida" subtitle="Registra un nuevo ingreso de bebidas.">
        <x-slot:actions>
            <a href="{{ route('ingreso-bebidas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('ingreso-bebidas.store') }}" novalidate>
                @csrf
                @include('ingreso-bebida.form', ['submitLabel' => 'Guardar ingreso'])
            </form>
        </div>
    </div>
@endsection
