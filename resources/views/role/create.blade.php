@extends('layouts.crud')

@section('title', 'Crear Rol')

@section('content')
    <x-crud.page-header title="Crear rol" subtitle="Registra un nuevo rol para permisos.">
        <x-slot:actions>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.store') }}" novalidate>
                @csrf
                @include('role.form', ['submitLabel' => 'Guardar rol'])
            </form>
        </div>
    </div>
@endsection
