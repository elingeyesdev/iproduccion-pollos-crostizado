@extends('layouts.crud')

@section('title', 'Detalle Producto')

@section('content')
    <x-crud.page-header title="Detalle del producto" subtitle="Información del producto registrado.">
        <x-slot:actions>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('producto-composicion.edit', $producto) }}" class="btn btn-app-outline"><i class="fa-solid fa-sitemap me-1"></i> Composición</a>
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Nombre</dt>
                <dd class="col-sm-9">{{ $producto->nombre }}</dd>

                <dt class="col-sm-3">Precio</dt>
                <dd class="col-sm-9">Bs {{ number_format((float) $producto->precio, 2) }}</dd>

                <dt class="col-sm-3">Tipo de producto</dt>
                <dd class="col-sm-9">{{ $producto->tipoProducto?->nombre ?? '-' }}</dd>

                <dt class="col-sm-3">Cantidad actual</dt>
                <dd class="col-sm-9">{{ (int) $producto->cantidad_actual }}</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    <span class="badge rounded-pill {{ (int) $producto->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                        {{ (int) $producto->estado === 1 ? 'Activo' : 'Inactivo' }}
                    </span>
                </dd>

                <dt class="col-sm-3">Creado</dt>
                <dd class="col-sm-9">{{ $producto->created_at?->format('d/m/Y H:i') }}</dd>
            </dl>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-header bg-white border-0 pb-0">
            <h5 class="mb-0 fw-bold">Composición del producto</h5>
            <p class="text-muted small mb-0">Componentes que conforman este producto final.</p>
        </div>
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Unidad</th>
                        <th>Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($producto->componentes as $componente)
                        <tr>
                            <td class="fw-semibold">{{ $componente->nombre }}</td>
                            <td>{{ $componente->equivalencia ?? '-' }}</td>
                            <td>{{ number_format((float) $componente->pivot->cantidad, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">Este producto todavía no tiene composición registrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
