@extends('layouts.crud')

@section('title', 'Editar Rol')

@section('content')
    <x-crud.page-header title="Editar rol" :subtitle="'Actualiza ' . $role->name">
        <x-slot:actions>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i> Volver
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('roles.update', $role) }}" novalidate>
                @method('PATCH')
                @csrf
                @include('role.form', ['submitLabel' => 'Actualizar rol'])
            </form>
        </div>
    </div>
@endsection
