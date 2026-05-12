@extends('layouts.crud')

@section('title', 'Crear Usuario')

@section('content')
    <x-crud.page-header title="Agregar usuario" subtitle="Registra una nueva cuenta en el sistema.">
        <x-slot:actions>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}" novalidate>
                @csrf
                @include('users.form', [
                    'submitLabel' => 'Guardar usuario',
                    'selectedRoles' => old('roles', []),
                    'isCreate' => true,
                ])
            </form>
        </div>
    </div>
@endsection