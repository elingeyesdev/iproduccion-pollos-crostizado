@extends('layouts.crud')

@section('title', 'Composición de Combos')

@section('content')
    <x-crud.page-header title="Armado de Combos" subtitle="Gestiona la composición de platos que contienen otros productos.">
        <x-slot:actions>
            <a href="{{ route('productos-composicion.create') }}" class="btn btn-app-primary">
                <i class="fa-solid fa-plus me-1"></i> Crear
            </a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4 fade-slide">
        <div class="card-body">
            <form method="GET" action="{{ route('productos-composicion.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar producto</label>
                    <input id="search" type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Nombre del producto">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('productos-composicion.index') }}" class="btn btn-outline-secondary">Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elevated fade-slide">
        <div class="card-body table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>Producto (Combo)</th>
                        <th>Tipo</th>
                        <th>Composición (Productos)</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td class="fw-bold fs-5 text-dark">{{ $producto->nombre }}</td>
                            <td>{{ $producto->tipoProducto?->nombre }}</td>
                            <td>
                                @if($producto->ingredientes->count() > 0)
                                    @foreach($producto->ingredientes as $ingrediente)
                                        <span class="badge bg-light text-dark border">
                                            {{ (float)$ingrediente->pivot->cantidad }} x {{ $ingrediente->nombre }}
                                        </span>
                                    @endforeach
                                @else
                                    <span class="text-muted italic small">Sin composición (Producto simple)</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('productos-composicion.edit', $producto) }}" class="btn btn-sm btn-app-outline">
                                        <i class="fa-solid fa-layer-group me-1"></i> Modificar Combo
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No se encontraron productos.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $productos->links() }}</div>
        </div>
    </div>
@endsection
