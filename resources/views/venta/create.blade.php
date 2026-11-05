@extends('layouts.crud')

@section('title', 'Nueva Venta')

@section('content')
    <x-crud.page-header title="Punto de Venta" subtitle="Crear una nueva venta para cliente.">
        <x-slot:actions>
            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="fade-slide">
        <form method="POST" action="{{ route('ventas.store') }}" role="form" id="venta-form">
            @csrf
            @include('venta.form')
        </form>
    </div>
@endsection

@push('styles')
    @vite('resources/css/venta-create.css')
@endpush
