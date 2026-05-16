@extends('layouts.crud')

@section('title', 'Detalle de Asignación')

@section('content')
    <x-crud.page-header title="Detalle de asignación" :subtitle="'Historial de ' . $assignment->user?->name">
        <x-slot:actions>
            <a href="{{ route('role-assignments.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Nueva asignación</a>
            <a href="{{ route('role-assignments.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Usuario</small><strong class="fs-5">{{ $assignment->user?->name }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Rol asignado</small><strong class="fs-5 text-accent">{{ $assignment->role?->name }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Asignado por</small><strong class="fs-5">{{ $assignment->assignedBy?->name ?? 'Sistema' }}</strong></div></div>
                <div class="col-md-6"><div class="border rounded-3 p-3 h-100"><small class="text-muted d-block">Fecha</small><strong class="fs-5">{{ $assignment->created_at?->format('d/m/Y H:i') }}</strong></div></div>
                <div class="col-md-12">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Roles antes</small>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse (($assignment->previous_roles ?? []) as $roleName)
                                <span class="badge text-bg-secondary">{{ $roleName }}</span>
                            @empty
                                <span class="text-muted">Sin roles previos</span>
                            @endforelse
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="border rounded-3 p-3 h-100">
                        <small class="text-muted d-block">Roles después</small>
                        <div class="d-flex flex-wrap gap-2 mt-2">
                            @forelse (($assignment->resulting_roles ?? []) as $roleName)
                                <span class="badge text-bg-success">{{ $roleName }}</span>
                            @empty
                                <span class="text-muted">Sin roles registrados</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection