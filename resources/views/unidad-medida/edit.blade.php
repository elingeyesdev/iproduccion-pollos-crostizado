@extends('layouts.crud')

@section('title', 'Editar Unidad de Medida')

@section('content')
    <x-crud.page-header
        title="Editar unidad de medida"
        subtitle="Actualiza la información de {{ $unidadMedida->nombre }}"
    >
        <x-slot:actions>
            <a href="{{ route('unidad-medidas.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver al listado
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('unidad-medidas.update', $unidadMedida) }}" novalidate>
                @method('PATCH')
                @csrf
                @include('unidad-medida.form', ['submitLabel' => 'Actualizar unidad'])
            </form>
        </div>
    </div>
@endsection
