@extends('layouts.crud')

@section('title', 'Productos')

@section('content')
    <x-crud.page-header title="Productos" subtitle="Catálogo de productos.">
        <x-slot:actions>
            <a href="{{ route('productos.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Crear producto</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('productos.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre o tipo">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Precio</th>
                        <th>Tipo</th>
                        <th>Cantidad actual</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td>{{ $productos->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td>Bs {{ number_format((float) $producto->precio, 2) }}</td>
                            <td>{{ $producto->tipoProducto?->nombre ?? '-' }}</td>
                            <td>{{ (int) $producto->cantidad_actual }}</td>
                            <td>
                                <span class="badge rounded-pill {{ (int) $producto->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                    {{ (int) $producto->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-producto-{{ $producto->id }}" action="{{ route('productos.destroy', $producto) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductoModal{{ $producto->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteProductoModal' . $producto->id"
                                        title="Eliminar producto"
                                        :message="'¿Seguro que deseas eliminar \'' . $producto->nombre . '\'?'"
                                        :form-id="'delete-form-producto-' . $producto->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No hay productos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $productos->links() }}</div>
        </div>
    </div>
@endsection
