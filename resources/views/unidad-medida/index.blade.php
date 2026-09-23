@extends('layouts.crud')

@section('title', 'Unidades de Medida')

@section('content')
    <x-crud.page-header title="Unidades de Medida" subtitle="Catálogo de unidades para la gestión de insumos.">
        <x-slot:actions>
            <a href="{{ route('unidad-medidas.create') }}" class="btn btn-app-primary">
                <i class="fa-solid fa-plus me-1"></i> Crear unidad
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('unidad-medidas.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input
                        id="search"
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        class="form-control"
                        placeholder="Buscar por nombre o abreviatura"
                    >
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit">
                        <i class="fa-solid fa-magnifying-glass me-1"></i> Buscar
                    </button>
                    <a href="{{ route('unidad-medidas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                            <th>Abreviatura</th>
                            <th>Factor conversión</th>
                            <th>Estado</th>
                            <th class="text-end">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($unidadMedidas as $unidadMedida)
                            <tr>
                                <td>{{ $unidadMedidas->firstItem() + $loop->index }}</td>
                                <td class="fw-semibold">{{ $unidadMedida->nombre }}</td>
                                <td>{{ $unidadMedida->abreviatura }}</td>
                                <td>{{ number_format((float) $unidadMedida->factor_conversion, 4) }}</td>
                                <td>
                                    <span class="badge rounded-pill {{ (int) $unidadMedida->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                        {{ (int) $unidadMedida->estado === 1 ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex justify-content-end gap-2">
                                        <a href="{{ route('unidad-medidas.show', $unidadMedida) }}" class="btn btn-sm btn-light border" title="Ver">
                                            <i class="fa-solid fa-eye me-1"></i> Ver
                                        </a>
                                        <a href="{{ route('unidad-medidas.edit', $unidadMedida) }}" class="btn btn-sm btn-app-outline" title="Editar">
                                            <i class="fa-solid fa-pen me-1"></i> Editar
                                        </a>

                                        <form id="delete-form-{{ $unidadMedida->id }}" action="{{ route('unidad-medidas.destroy', $unidadMedida) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $unidadMedida->id }}" title="Eliminar">
                                                <i class="fa-solid fa-trash me-1"></i> Eliminar
                                            </button>
                                        </form>

                                        <x-crud.delete-modal
                                            :id="'deleteModal' . $unidadMedida->id"
                                            title="Eliminar unidad de medida"
                                            :message="'¿Seguro que deseas eliminar \'' . $unidadMedida->nombre . '\'? Esta acción no se puede deshacer.'"
                                            :form-id="'delete-form-' . $unidadMedida->id"
                                        />
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay registros para mostrar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end">
                {{ $unidadMedidas->links() }}
            </div>
        </div>
    </div>
@endsection
