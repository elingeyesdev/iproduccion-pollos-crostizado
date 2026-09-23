@extends('layouts.crud')

@section('title', 'Reporte de Insumos')

@section('content')
    <x-crud.page-header title="Reporte de insumos" subtitle="Listado completo de insumos y su stock actual.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.insumos') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Buscar por nombre">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('reportes.insumos') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Insumo</th>
                        <th>Unidad</th>
                        <th class="text-end">Stock actual</th>
                        <th class="text-end">Stock mínimo</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($insumos as $insumo)
                        <tr>
                            <td>{{ $insumos->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $insumo->nombre }}</td>
                            <td>{{ $insumo->unidadMedida?->abreviatura ?? $insumo->unidadMedida?->nombre ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $insumo->stock_actual, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $insumo->stock_minimo, 2) }}</td>
                            <td>
                                @if ((float) $insumo->stock_actual <= (float) $insumo->stock_minimo)
                                    <span class="badge bg-danger">Bajo mínimo</span>
                                @else
                                    <span class="badge bg-success">OK</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No hay insumos registrados.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $insumos->links() }}</div>
        </div>
    </div>
@endsection
