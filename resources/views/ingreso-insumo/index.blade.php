@extends('layouts.crud')

@section('title', 'Ingresos de Insumos')

@section('content')
    <x-crud.page-header title="Ingresos de insumos" subtitle="Registro de ingresos con detalle por insumo.">
        <x-slot:actions>
            <a href="{{ route('ingreso-insumos.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Crear ingreso</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ingreso-insumos.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por fecha o descripción">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('ingreso-insumos.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Detalles</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ingresos as $ingreso)
                        <tr>
                            <td>{{ $ingresos->firstItem() + $loop->index }}</td>
                            <td>{{ $ingreso->fecha_ingreso?->format('d/m/Y') }}</td>
                            <td>{{ $ingreso->descripcion ?? '-' }}</td>
                            <td>{{ $ingreso->detalles->count() }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('ingreso-insumos.show', $ingreso) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('ingreso-insumos.edit', $ingreso) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-ingreso-{{ $ingreso->id }}" action="{{ route('ingreso-insumos.destroy', $ingreso) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteIngresoModal{{ $ingreso->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteIngresoModal' . $ingreso->id"
                                        title="Eliminar ingreso"
                                        :message="'¿Seguro que deseas eliminar el ingreso del ' . $ingreso->fecha_ingreso?->format('d/m/Y') . '?'"
                                        :form-id="'delete-form-ingreso-' . $ingreso->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay ingresos registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $ingresos->links() }}</div>
        </div>
    </div>
@endsection
