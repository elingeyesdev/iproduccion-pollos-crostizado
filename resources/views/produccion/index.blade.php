@extends('layouts.crud')

@section('title', 'Producciones')

@section('content')
    <x-crud.page-header title="Producciones" subtitle="Visualiza y ejecuta producciones registradas.">
        <x-slot:actions>
            <a href="{{ route('producciones.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-play me-1"></i> Ejecutar producción</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('producciones.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por número de orden">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Orden de producción</th>
                        <th>Detalle de orden</th>
                        <th class="text-end">Cant. insumo</th>
                        <th class="text-end">Cant. obtenida</th>
                        <th>Fecha</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($producciones as $produccion)
                        <tr>
                            <td>{{ $producciones->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $produccion->ordenProduccion?->numero_orden ?? '-' }}</td>
                            <td>
                                @php($detalle = $produccion->detalle_orden)
                                @if($detalle)
                                    <span class="badge bg-light text-dark border">
                                        {{ $detalle->insumo?->nombre ?? 'Insumo' }}
                                    </span>
                                @else
                                    <span class="text-muted small">Sin detalle asociado</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $produccion->cantidad_insumo, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $produccion->cantidad_obtenida, 2) }}</td>
                            <td>{{ $produccion->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('producciones.show', $produccion) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-4 text-muted">No hay producciones registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $producciones->links() }}</div>
        </div>
    </div>
@endsection
