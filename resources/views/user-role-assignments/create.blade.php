@extends('layouts.crud')

@section('title', 'Nueva Asignación')

@section('content')
    <x-crud.page-header title="Nueva asignación" subtitle="Asigna un rol a un usuario y deja trazabilidad del cambio.">
        <x-slot:actions>
            <a href="{{ route('role-assignments.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('role-assignments.store') }}" novalidate>
                @csrf
                @include('user-role-assignments.form', [
                    'submitLabel' => 'Asignar rol',
                    'selectedUserId' => old('user_id'),
                    'selectedRoleId' => old('role_id'),
                ])
            </form>
        </div>
    </div>
@endsection