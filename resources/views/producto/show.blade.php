@extends('layouts.crud')

@section('title', 'Detalle Producto')

@section('content')
    <x-crud.page-header title="Detalle del producto" subtitle="Información del producto registrado.">
        <x-slot:actions>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('productos-composicion.edit', $producto) }}" class="btn btn-app-outline"><i class="fa-solid fa-sitemap me-1"></i> Composición</a>
            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Foto</dt>
                <dd class="col-sm-9">
                    @if ($producto->imagen_url)
                        <img src="{{ $producto->imagen_url }}" alt="{{ $producto->nombre }}" class="rounded border" style="width: 160px; height: 160px; object-fit: cover;">
                    @else
                        <span class="text-muted">Sin foto</span>
                    @endif
                </dd>

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

    <div class="card card-elevated mb-4">
        <div class="card-header bg-white border-0 pb-0">
            <h5 class="mb-0 fw-bold">Composición del combo</h5>
            <p class="text-muted small mb-0">Productos que se descuentan del stock cuando se vende este producto.</p>
        </div>
        <div class="card-body table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Tipo</th>
                        <th class="text-end">Cantidad por unidad</th>
                        <th class="text-end">Stock actual</th>
                        <th class="text-end">Alcanza para</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($producto->ingredientes as $ingrediente)
                        @php
                            $cantidad = (float) $ingrediente->pivot->cantidad;
                            $alcanza = $cantidad > 0 ? (int) floor((float) $ingrediente->cantidad_actual / $cantidad) : 0;
                        @endphp
                        <tr>
                            <td class="fw-semibold">
                                <a href="{{ route('productos.show', $ingrediente) }}" class="text-decoration-none">{{ $ingrediente->nombre }}</a>
                            </td>
                            <td>{{ $ingrediente->tipoProducto?->nombre ?? '-' }}</td>
                            <td class="text-end">{{ rtrim(rtrim(number_format($cantidad, 2), '0'), '.') }}</td>
                            <td class="text-end">{{ (int) $ingrediente->cantidad_actual }}</td>
                            <td class="text-end {{ $alcanza <= 0 ? 'text-danger fw-semibold' : '' }}">{{ $alcanza }} {{ $alcanza === 1 ? 'combo' : 'combos' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                Este producto no es un combo: se vende y descuenta con su propio stock.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if ($producto->ingredientes->isNotEmpty())
                <p class="small text-muted mb-0">
                    Al vender este combo no se usa su stock propio; se descuentan los productos de la tabla.
                </p>
            @endif
        </div>
    </div>

    @if ($producto->esParteDe->isNotEmpty())
        <div class="card card-elevated">
            <div class="card-header bg-white border-0 pb-0">
                <h5 class="mb-0 fw-bold">Forma parte de</h5>
                <p class="text-muted small mb-0">Combos que incluyen este producto.</p>
            </div>
            <div class="card-body table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Combo</th>
                            <th class="text-end">Cantidad que usa</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($producto->esParteDe as $combo)
                            <tr>
                                <td class="fw-semibold"><a href="{{ route('productos.show', $combo) }}" class="text-decoration-none">{{ $combo->nombre }}</a></td>
                                <td class="text-end">{{ rtrim(rtrim(number_format((float) $combo->pivot->cantidad, 2), '0'), '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection
