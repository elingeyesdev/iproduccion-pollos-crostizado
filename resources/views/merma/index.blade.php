@extends('layouts.crud')

@section('title', 'Mermas')

@section('content')
    <x-crud.page-header title="Mermas" subtitle="Control de mermas por producción.">
        <x-slot:actions>
            <a href="{{ route('mermas.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Crear merma</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('mermas.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por cantidad">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('mermas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Producción</th>
                        <th>Producto afectado</th>
                        <th>Cantidad</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($mermas as $merma)
                        <tr>
                            <td>{{ $mermas->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $merma->produccion?->ordenProduccion?->numero_orden ?? ('Producción #' . $merma->produccion_id) }}</td>
                            <td>{{ $merma->produccion?->producto_afectado?->nombre ?? '-' }}</td>
                            <td>{{ number_format((float) $merma->cantidad, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ (int) $merma->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                                    {{ (int) $merma->estado === 1 ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('mermas.show', $merma) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('mermas.edit', $merma) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form id="delete-form-merma-{{ $merma->id }}" action="{{ route('mermas.destroy', $merma) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#deleteMermaModal{{ $merma->id }}"><i class="fa-solid fa-trash me-1"></i> Eliminar</button>
                                    </form>
                                    <x-crud.delete-modal
                                        :id="'deleteMermaModal' . $merma->id"
                                        title="Eliminar merma"
                                        :message="'¿Seguro que deseas eliminar este registro de merma?'"
                                        :form-id="'delete-form-merma-' . $merma->id"
                                    />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No hay mermas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $mermas->links() }}</div>
        </div>
    </div>
@endsection
