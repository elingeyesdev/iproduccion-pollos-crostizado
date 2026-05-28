@extends('layouts.crud')

@section('title', 'Editar Orden de Producción')

@section('content')
    <x-crud.page-header title="Editar orden de producción" subtitle="Modifica los datos de la orden.">
        <x-slot:actions>
            <a href="{{ route('orden-producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('orden-producciones.update', $ordenProduccion) }}" novalidate>
                @csrf
                @method('PUT')
                @include('orden-produccion.form', ['submitLabel' => 'Actualizar orden'])
            </form>
        </div>
    </div>
@endsection
