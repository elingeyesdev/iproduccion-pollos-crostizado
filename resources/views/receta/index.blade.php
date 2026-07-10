@extends('layouts.crud')

@section('title', 'Recetas y Rendimientos')

@section('content')
    <x-crud.page-header title="Recetas" subtitle="Gestión de rendimientos de producción.">
        <x-slot:actions>
            <a href="{{ route('recetas.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Nueva receta</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4 fade-slide">
        <div class="card-body">
            <form method="GET" action="{{ route('recetas.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Buscar por nombre o producto">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('recetas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elevated fade-slide">
        <div class="card-body table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nombre</th>
                        <th>Producto Final</th>
                        <th>Cantidad Base</th>
                        <th>Rendimiento</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recetas as $receta)
                        <tr>
                            <td>{{ $recetas->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $receta->nombre }}</td>
                            <td>{{ $receta->producto?->nombre }}</td>
                            <td>{{ number_format($receta->cantidad_base, 2) }}</td>
                            <td class="text-success fw-bold">{{ number_format($receta->rendimiento_esperado, 2) }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('recetas.show', $receta) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <a href="{{ route('recetas.edit', $receta) }}" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
                                    <form action="{{ route('recetas.destroy', $receta) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de eliminar esta receta?')">
                                            <i class="fa-solid fa-trash me-1"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron recetas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $recetas->links() }}</div>
        </div>
    </div>
@endsection
