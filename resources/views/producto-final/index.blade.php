@extends('layouts.crud')

@section('title', 'Productos Finales')

@section('content')
    <x-crud.page-header title="Productos finales" subtitle="Gestión de productos terminados.">
        <x-slot:actions>
            <a href="{{ route('producto-finals.create') }}" class="btn btn-app-primary">
                <i class="fa-solid fa-plus me-1"></i> Nuevo producto
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('producto-finals.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre o descripción">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('producto-finals.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productoFinals as $productoFinal)
                        <tr>
                            <td>{{ $productoFinals->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $productoFinal->nombre }}</td>
                            <td>{{ $productoFinal->descripcion }}</td>
                            <td>S/ {{ number_format((float) $productoFinal->precio, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ (int) $productoFinal->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                    {{ (int) $productoFinal->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('producto-finals.show', $productoFinal) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('producto-finals.edit', $productoFinal) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-producto-{{ $productoFinal->id }}" action="{{ route('producto-finals.destroy', $productoFinal) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteProductoModal{{ $productoFinal->id }}">
                                            <i class="fa-solid fa-trash me-1"></i> Eliminar
                                        </button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteProductoModal' . $productoFinal->id"
                                        title="Eliminar producto"
                                        :message="'¿Seguro que deseas eliminar \'' . $productoFinal->nombre . '\'?'"
                                        :form-id="'delete-form-producto-' . $productoFinal->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No hay productos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                {{ $productoFinals->links() }}
            </div>
        </div>
    </div>
@endsection
