@extends('layouts.crud')

@section('title', 'Editar Ingreso')

@section('content')
    <x-crud.page-header title="Editar ingreso de insumos" :subtitle="'Actualiza el ingreso del ' . $ingreso->fecha_ingreso?->format('d/m/Y')">
        <x-slot:actions>
            <a href="{{ route('ingreso-insumos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('ingreso-insumos.update', $ingreso) }}" novalidate>
                @csrf
                @method('PUT')
                @include('ingreso-insumo.form', ['submitLabel' => 'Actualizar ingreso'])
            </form>
        </div>
    </div>
@endsection
