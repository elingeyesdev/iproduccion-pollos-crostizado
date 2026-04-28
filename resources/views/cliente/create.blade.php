@extends('layouts.crud')

@section('title', 'Crear Cliente')

@section('content')
    <x-crud.page-header title="Crear cliente" subtitle="Registra un nuevo cliente.">
        <x-slot:actions>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('clientes.store') }}" novalidate>
                @csrf
                @include('cliente.form', ['submitLabel' => 'Guardar cliente'])
            </form>
        </div>
    </div>
@endsection
