@extends('layouts.crud')

@section('title', 'Usuarios')

@section('content')
    <x-crud.page-header title="Administrar Usuario" subtitle="Administración de cuentas del sistema.">
        <x-slot:actions>
            <a href="{{ route('users.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Nuevo usuario</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('users.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre o correo">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Roles</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse ($user->getRoleNames() as $roleName)
                                    <span class="badge text-bg-secondary">{{ $roleName }}</span>
                                @empty
                                    <span class="text-muted">Sin rol</span>
                                @endforelse
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-user-{{ $user->id }}" method="POST" action="{{ route('users.destroy', $user) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteUserModal{{ $user->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteUserModal' . $user->id"
                                        title="Eliminar usuario"
                                        :message="'¿Seguro que deseas eliminar a ' . $user->name . '?'"
                                        :form-id="'delete-form-user-' . $user->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No se encontraron usuarios.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $users->links() }}</div>
        </div>
    </div>
@endsection