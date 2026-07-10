@extends('layouts.crud')

@section('title', 'Órdenes de Producción')

@section('content')
    <x-crud.page-header title="Órdenes de producción" subtitle="Programación de lotes de producción.">
        <x-slot:actions>
            <a href="{{ route('orden-producciones.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Nueva orden</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('orden-producciones.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por número o fecha">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('orden-producciones.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Número de orden</th>
                        <th>Fecha programada</th>
                        <th>Insumos planificados</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ordenProducciones as $ordenProduccion)
                        <tr>
                            <td>{{ $ordenProducciones->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $ordenProduccion->numero_orden }}</td>
                            <td>{{ $ordenProduccion->fecha_programada ? \Illuminate\Support\Carbon::parse($ordenProduccion->fecha_programada)->format('d/m/Y') : '-' }}</td>
                            <td>{{ (int) ($ordenProduccion->insumos_count ?? 0) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ (int) $ordenProduccion->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                    {{ (int) $ordenProduccion->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('orden-producciones.show', $ordenProduccion) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('orden-producciones.edit', $ordenProduccion) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-orden-produccion-{{ $ordenProduccion->id }}" action="{{ route('orden-producciones.destroy', $ordenProduccion) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOrdenProduccionModal{{ $ordenProduccion->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteOrdenProduccionModal' . $ordenProduccion->id"
                                        title="Eliminar orden de producción"
                                        :message="'¿Seguro que deseas eliminar la orden ' . $ordenProduccion->numero_orden . '?'"
                                        :form-id="'delete-form-orden-produccion-' . $ordenProduccion->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No hay órdenes registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $ordenProducciones->links() }}</div>
        </div>
    </div>
@endsection
