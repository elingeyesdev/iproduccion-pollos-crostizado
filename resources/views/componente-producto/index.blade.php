@extends('layouts.crud')

@section('title', 'Componentes de producto')

@section('content')
    <x-crud.page-header title="Componentes de producto" subtitle="Catálogo de componentes.">
        <x-slot:actions>
            <a href="{{ route('componente-productos.composiciones') }}" class="btn btn-app-outline"><i class="fa-solid fa-sitemap me-1"></i> Armar composiciones</a>
            <a href="{{ route('componente-productos.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Nuevo componente</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('componente-productos.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('componente-productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Equivalencia</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($componenteProductos as $componenteProducto)
                        <tr>
                            <td>{{ $componenteProductos->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $componenteProducto->nombre }}</td>
                            <td>{{ $componenteProducto->equivalencia ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ (int) $componenteProducto->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                    {{ (int) $componenteProducto->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('componente-productos.show', $componenteProducto) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('componente-productos.edit', $componenteProducto) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-componente-producto-{{ $componenteProducto->id }}" action="{{ route('componente-productos.destroy', $componenteProducto) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteComponenteProductoModal{{ $componenteProducto->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteComponenteProductoModal' . $componenteProducto->id"
                                        title="Eliminar componente"
                                        :message="'¿Seguro que deseas eliminar \'' . $componenteProducto->nombre . '\'?'"
                                        :form-id="'delete-form-componente-producto-' . $componenteProducto->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center py-4 text-muted">No hay componentes registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $componenteProductos->links() }}</div>
        </div>
    </div>
@endsection
