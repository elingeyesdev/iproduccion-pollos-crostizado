@extends('layouts.crud')

@section('title', 'Crear Producto Final')

@section('content')
    <x-crud.page-header title="Crear producto final" subtitle="Registra un nuevo producto para ventas.">
        <x-slot:actions>
            <a href="{{ route('producto-finals.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('producto-finals.store') }}" novalidate>
                @csrf
                @include('producto-final.form', ['submitLabel' => 'Guardar producto'])
            </form>
        </div>
    </div>
@endsection
