@extends('layouts.crud')

@section('title', 'Composición de Productos')

@section('content')
    <x-crud.page-header title="Composición de productos" subtitle="Arma la composición de Económico, Cuarto y otros productos.">
        <x-slot:actions>
            <a href="{{ route('componente-productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver a componentes</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('componente-productos.composiciones') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar producto</label>
                    <input id="search" type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Ejemplo: Económico, Cuarto">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('componente-productos.composiciones') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th>Componentes</th>
                        <th class="text-end">Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($productos as $producto)
                        <tr>
                            <td>{{ $productos->firstItem() + $loop->index }}</td>
                            <td class="fw-semibold">{{ $producto->nombre }}</td>
                            <td>{{ $producto->tipoProducto?->nombre ?? '-' }}</td>
                            <td>
                                <span class="badge rounded-pill text-bg-light border">
                                    {{ $producto->componentes->count() }} registrados
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('componente-productos.composiciones.edit', $producto) }}" class="btn btn-sm btn-app-primary">
                                    <i class="fa-solid fa-sitemap me-1"></i> Armar composición
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">No hay productos para configurar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $productos->links() }}</div>
        </div>
    </div>
@endsection
