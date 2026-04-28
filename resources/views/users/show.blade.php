@extends('layouts.crud')

@section('title', 'Detalle de Usuario')

@section('content')
    <x-crud.page-header title="Ficha de usuario" :subtitle="'Detalle de ' . $user->name">
        <x-slot:actions>
            <a href="{{ route('users.edit', $user) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Nombre</small><strong class="fs-5">{{ $user->name }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Correo</small><strong class="fs-5 text-accent">{{ $user->email }}</strong></div></div>
                <div class="col-md-12">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Roles asignados</small>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse ($user->getRoleNames() as $roleName)
                                <span class="badge text-bg-secondary">{{ $roleName }}</span>
                            @empty
                                <span class="text-muted">Sin roles asignados</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
