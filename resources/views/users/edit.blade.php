@extends('layouts.crud')

@section('title', 'Editar Usuario')

@section('content')
    <x-crud.page-header title="Editar usuario" :subtitle="'Actualiza ' . $user->name">
        <x-slot:actions>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('users.update', $user) }}" novalidate>
                @csrf
                @method('PUT')
                @include('users.form', [
                    'submitLabel' => 'Actualizar usuario',
                    'selectedRole' => old('role', $user->roles->first()?->name),
                    'isCreate' => false,
                ])
            </form>
        </div>
    </div>
@endsection