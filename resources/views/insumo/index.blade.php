@extends('layouts.crud')

@section('title', 'Insumos')

@section('content')
    <x-crud.page-header title="Insumos" subtitle="Catálogo de insumos para producción.">
        <x-slot:actions>
            <a href="{{ route('insumos.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Nuevo insumo</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('insumos.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre o unidad">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('insumos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Unidad</th>
                        <th>Stock actual</th>
                        <th>Stock mínimo</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($insumos as $insumo)
                        <tr>
                            <td>{{ $insumos->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $insumo->nombre }}</td>
                            <td>{{ $insumo->unidadMedida?->abreviatura ?? $insumo->unidadMedida?->nombre ?? '-' }}</td>
                            <td>{{ (int) $insumo->stock_actual }}</td>
                            <td>{{ (int) $insumo->stock_minimo }}</td>
                            <td>
                                <span class="badge rounded-pill {{ (int) $insumo->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                    {{ (int) $insumo->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('insumos.show', $insumo) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('insumos.edit', $insumo) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-insumo-{{ $insumo->id }}" action="{{ route('insumos.destroy', $insumo) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteInsumoModal{{ $insumo->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteInsumoModal' . $insumo->id"
                                        title="Eliminar insumo"
                                        :message="'¿Seguro que deseas eliminar \'' . $insumo->nombre . '\'?'"
                                        :form-id="'delete-form-insumo-' . $insumo->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-4 text-muted">No hay insumos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $insumos->links() }}</div>
        </div>
    </div>
@endsection
