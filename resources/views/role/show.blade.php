@extends('layouts.crud')

@section('title', 'Detalle de Rol')

@section('content')
    <x-crud.page-header title="Ficha del rol" :subtitle="'Detalle de ' . $role->name">
        <x-slot:actions>
            <a href="{{ route('roles.edit', $role) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Nombre del rol</small>
                        <strong class="fs-5">{{ $role->name }}</strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Guard</small>
                        <strong class="fs-5 text-accent">{{ $role->guard_name }}</strong>
                    </div>
                </div>
            </div>

            <hr class="my-4">
            <h5 class="mb-3">Permisos asignados</h5>
            <div class="d-flex flex-wrap gap-2">
                @forelse ($role->permissions->sortBy('name') as $permission)
                    <span class="badge text-bg-light border">{{ $permission->name }}</span>
                @empty
                    <span class="text-muted">Este rol no tiene permisos asignados.</span>
                @endforelse
            </div>
        </div>
    </div>
@endsection
