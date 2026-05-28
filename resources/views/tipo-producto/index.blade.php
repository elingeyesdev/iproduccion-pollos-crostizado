@extends('layouts.crud')

@section('title', 'Tipos de Producto')

@section('content')
    <x-crud.page-header title="Tipos de producto" subtitle="Clasifica los productos por categoria para su gestion.">
        <x-slot:actions>
            <a href="{{ route('tipo-productos.create') }}" class="btn btn-app-primary">
                <i class="fa-solid fa-plus me-1"></i> Nuevo tipo
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('tipo-productos.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="form-control"
                        placeholder="Buscar por nombre o descripcion"
                    >
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
                    </button>
                    <a href="{{ route('tipo-productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table align-middle table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>Descripcion</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tipoProductos as $tipoProducto)
                            <tr>
                                <td>{{ $tipoProductos->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $tipoProducto->nombre }}</td>
                                <td>{{ $tipoProducto->descripcion ?: '-' }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ (int) $tipoProducto->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                        {{ (int) $tipoProducto->estado === 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('tipo-productos.show', $tipoProducto) }}" class="btn btn-sm btn-light border" title="Ver">
                                            <i class="fa-solid fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('tipo-productos.edit', $tipoProducto) }}" class="btn btn-sm btn-app-outline" title="Editar">
                                            <i class="fa-solid fa-pen me-1"></i> Editar
                                        </a>

                                        <form id="delete-form-{{ $tipoProducto->id }}" action="{{ route('tipo-productos.destroy', $tipoProducto) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $tipoProducto->id }}" title="Eliminar">
                                                <i class="fa-solid fa-trash me-1"></i> Eliminar
                                            </button>
                                        </form>

                                        <x-crud.delete-modal
                                            :id="'deleteModal' . $tipoProducto->id"
                                            title="Eliminar tipo de producto"
                                            :message="'¿Seguro que deseas eliminar \'' . $tipoProducto->nombre . '\'? Esta accion no se puede deshacer.'"
                                            :form-id="'delete-form-' . $tipoProducto->id"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No hay registros para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $tipoProductos->links() }}
            </div>
        </div>
    </div>
@endsection
