@extends('layouts.crud')

@section('title', 'Asignación de Roles')

@section('content')
    <x-crud.page-header title="Asignación de roles" subtitle="Registro histórico de roles asignados a usuarios.">
        <x-slot:actions>
            <a href="{{ route('role-assignments.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Nueva asignación</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('role-assignments.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por usuario o rol">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('role-assignments.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-body table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Usuario</th>
                        <th>Rol asignado</th>
                        <th>Asignado por</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assignments as $assignment)
                        <tr>
                            <td>{{ $assignments->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $assignment->user?->name }}</td>
                            <td><span class="badge text-bg-secondary">{{ $assignment->role?->name }}</span></td>
                            <td>{{ $assignment->assignedBy?->name ?? 'Sistema' }}</td>
                            <td>{{ $assignment->created_at?->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('role-assignments.show', $assignment) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay asignaciones registradas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $assignments->links() }}</div>
        </div>
    </div>
@endsection