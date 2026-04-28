@extends('layouts.crud')

@section('title', 'Editar Producto Final')

@section('content')
    <x-crud.page-header title="Editar producto final" :subtitle="'Actualiza ' . $productoFinal->nombre">
        <x-slot:actions>
            <a href="{{ route('producto-finals.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('producto-finals.update', $productoFinal) }}" novalidate>
                @method('PATCH')
                @csrf
                @include('producto-final.form', ['submitLabel' => 'Actualizar producto'])
            </form>
        </div>
    </div>
@endsection
