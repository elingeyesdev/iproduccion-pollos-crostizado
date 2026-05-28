@extends('layouts.crud')

@section('title', 'Editar Producción')

@section('content')
    <x-crud.page-header title="Editar producción" subtitle="Modifica la orden asociada a la producción.">
        <x-slot:actions>
            <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('producciones.update', $produccione) }}" novalidate>
                @csrf
                @method('PUT')
                @include('produccion.form', ['produccionRecord' => $produccione, 'submitLabel' => 'Actualizar producción'])
            </form>
        </div>
    </div>
@endsection
