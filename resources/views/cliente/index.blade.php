@extends('layouts.crud')

@section('title', 'Clientes')

@section('content')
    <x-crud.page-header title="Clientes" subtitle="Listado de clientes registrados.">
        <x-slot:actions>
            <a href="{{ route('clientes.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Crear cliente</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('clientes.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre o teléfono">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Teléfono</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($clientes as $cliente)
                        <tr>
                            <td>{{ $clientes->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $cliente->nombre }}</td>
                            <td>{{ $cliente->telefono }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-cliente-{{ $cliente->id }}" action="{{ route('clientes.destroy', $cliente) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteClienteModal{{ $cliente->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteClienteModal' . $cliente->id"
                                        title="Eliminar cliente"
                                        :message="'¿Seguro que deseas eliminar a \'' . $cliente->nombre . '\'?'"
                                        :form-id="'delete-form-cliente-' . $cliente->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No hay clientes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $clientes->links() }}</div>
        </div>
    </div>
@endsection
