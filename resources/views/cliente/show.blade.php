@extends('layouts.crud')

@section('title', 'Detalle de Cliente')

@section('content')
    <x-crud.page-header title="Ficha de cliente" :subtitle="'Detalle de ' . $cliente->nombre">
        <x-slot:actions>
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
            <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Nombre</small><strong class="fs-5">{{ $cliente->nombre }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Teléfono</small><strong class="fs-5 text-accent">{{ $cliente->telefono }}</strong></div></div>
            </div>
        </div>
    </div>
@endsection
