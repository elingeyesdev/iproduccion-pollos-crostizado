@extends('layouts.crud')

@section('title', 'Roles')

@section('content')
    <x-crud.page-header title="Administrar Roles" subtitle="Administra los roles del sistema.">
        <x-slot:actions>
            <a href="{{ route('roles.create') }}" class="btn btn-app-primary">
                <i class="fa-solid fa-plus me-1"></i> Crear rol
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('roles.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre o guard">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Nombre Usuario</th>
                        <th>Tipo de Rol</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($roles as $role)
                        <tr>
                            <td>{{ $roles->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $role->name }}</td>
                            <td>{{ $role->guard_name }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('roles.show', $role) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-role-{{ $role->id }}" action="{{ route('roles.destroy', $role) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteRoleModal{{ $role->id }}">
                                            <i class="fa-solid fa-trash me-1"></i> Eliminar
                                        </button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteRoleModal' . $role->id"
                                        title="Eliminar rol"
                                        :message="'¿Seguro que deseas eliminar el rol \'' . $role->name . '\'?'"
                                        :form-id="'delete-form-role-' . $role->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">No hay roles registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $roles->links() }}
            </div>
        </div>
    </div>
@endsection
