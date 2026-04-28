@extends('layouts.crud')

@section('title', 'Editar Cliente')

@section('content')
    <x-crud.page-header title="Editar cliente" :subtitle="'Actualiza ' . $cliente->nombre">
        <x-slot:actions>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('clientes.update', $cliente) }}" novalidate>
                @method('PATCH')
                @csrf
                @include('cliente.form', ['submitLabel' => 'Actualizar cliente'])
            </form>
        </div>
    </div>
@endsection
