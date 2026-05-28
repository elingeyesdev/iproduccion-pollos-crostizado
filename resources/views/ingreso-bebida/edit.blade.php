@extends('layouts.crud')

@section('title', 'Editar Ingreso de Bebida')

@section('content')
    <x-crud.page-header title="Editar ingreso de bebida" subtitle="Modifica los datos del ingreso.">
        <x-slot:actions>
            <a href="{{ route('ingreso-bebidas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('ingreso-bebidas.update', $ingresoBebida) }}" novalidate>
                @csrf
                @method('PUT')
                @include('ingreso-bebida.form', ['submitLabel' => 'Actualizar ingreso'])
            </form>
        </div>
    </div>
@endsection
