@extends('layouts.crud')

@section('title', 'Ventas')

@section('content')
    <x-crud.page-header title="Ventas" subtitle="Listado de ventas realizadas.">
        <x-slot:actions>
            <a href="{{ route('ventas.create') }}" class="btn btn-app-primary"><i class="fa-solid fa-plus me-1"></i> Crear venta</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4 fade-slide">
        <div class="card-body">
            <form method="GET" action="{{ route('ventas.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar</label>
                    <input id="search" type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Buscar por cliente o fecha">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">Limpiar</a>
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
                        <th>Fecha</th>
                        <th>Cliente</th>
                        <th>Método Pago</th>
                        <th class="text-end">Total</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ventas as $venta)
                        <tr>
                            <td>{{ $ventas->firstItem() + $loop->index }}</td>
                            <td>{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</td>
                            <td class="fw-semibold">{{ $venta->cliente?->nombre ?? 'N/A' }}</td>
                            <td>
                                @forelse($venta->pagos as $pago)
                                    <span class="badge rounded-pill bg-light text-dark border me-1">
                                        <i class="fa-solid {{ $pago->metodo_pago === 'QR' ? 'fa-qrcode' : 'fa-money-bill-1-wave' }} me-1 text-danger"></i>
                                        {{ $pago->metodo_pago }}
                                    </span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="text-end fw-bold text-success">Bs {{ number_format($venta->total, 2) }}</td>
                            <td>
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-light border"><i class="fa-solid fa-eye me-1"></i> Ver</a>
                                    <form action="{{ route('ventas.destroy', $venta) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de anular esta venta? El stock de los productos será repuesto.')">
                                            <i class="fa-solid fa-ban me-1"></i> Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron ventas.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $ventas->links() }}</div>
        </div>
    </div>
@endsection
