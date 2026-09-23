@extends('layouts.crud')

@section('title', 'Ingresos de Bebidas')

@section('content')
    <x-crud.page-header title="Ingresos de bebidas" subtitle="Registro de ingresos de bebidas.">
        <x-slot:actions>
            <a href="{{ route('ingreso-bebidas.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Crear ingreso</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ingreso-bebidas.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre o descripción">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('ingreso-bebidas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Fecha ingreso</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ingresoBebidas as $ingresoBebida)
                        <tr>
                            <td>{{ $ingresoBebidas->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $ingresoBebida->nombre }}</td>
                            <td>{{ $ingresoBebida->descripcion ?? '-' }}</td>
                            <td>{{ $ingresoBebida->fecha_ingreso ? \Illuminate\Support\Carbon::parse($ingresoBebida->fecha_ingreso)->format('d/m/Y') : '-' }}</td>
                            <td>
                                <span class="badge rounded-pill {{ (int) $ingresoBebida->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                    {{ (int) $ingresoBebida->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('ingreso-bebidas.show', $ingresoBebida) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('ingreso-bebidas.edit', $ingresoBebida) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-ingreso-bebida-{{ $ingresoBebida->id }}" action="{{ route('ingreso-bebidas.destroy', $ingresoBebida) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteIngresoBebidaModal{{ $ingresoBebida->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteIngresoBebidaModal' . $ingresoBebida->id"
                                        title="Eliminar ingreso de bebida"
                                        :message="'¿Seguro que deseas eliminar el ingreso: ' . $ingresoBebida->nombre . '?'"
                                        :form-id="'delete-form-ingreso-bebida-' . $ingresoBebida->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No hay ingresos de bebidas registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $ingresoBebidas->links() }}</div>
        </div>
    </div>
@endsection
