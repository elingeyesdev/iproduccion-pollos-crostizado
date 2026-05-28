@extends('layouts.crud')

@section('title', 'Detalle de Venta #' . $venta->id)

@section('content')
    <x-crud.page-header title="Detalle de Venta" :subtitle="'Comprobante de venta #' . $venta->id">
        <x-slot:actions>
            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <button onclick="window.print()" class="btn btn-app-primary"><i class="fa-solid fa-print me-1"></i> Imprimir</button>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="row fade-slide">
        <div class="col-md-4">
            <div class="card card-elevated mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-circle-info me-2"></i>Información General</h5>
                    
                    <div class="mb-3">
                        <small class="text-muted d-block">Cliente</small>
                        <span class="fw-bold fs-5 text-dark">{{ $venta->cliente?->nombre ?? 'N/A' }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Fecha de Venta</small>
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</span>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">Pagos</small>
                        @forelse($venta->pagos as $pago)
                            <span class="badge bg-danger fs-6 me-1 mb-1">
                                <i class="fa-solid {{ $pago->metodo_pago === 'QR' ? 'fa-qrcode' : 'fa-money-bill-1-wave' }} me-1"></i>
                                {{ $pago->metodo_pago }}: Bs {{ number_format($pago->monto, 2) }}
                            </span>
                        @empty
                            <span class="text-muted">Sin pagos registrados</span>
                        @endforelse
                    </div>

                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-elevated">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-list-ul me-2"></i>Productos Vendidos</h5>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Producto</th>
                                    <th class="text-center">Cant.</th>
                                    <th class="text-end">Precio Unit.</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($venta->detalles as $detalle)
                                    <tr>
                                        <td>
                                            <div class="fw-bold">{{ $detalle->producto?->nombre }}</div>
                                            <small class="text-muted">{{ $detalle->producto?->tipoProducto?->nombre }}</small>
                                        </td>
                                        <td class="text-center">{{ number_format($detalle->cantidad, 0) }}</td>
                                        <td class="text-end">Bs {{ number_format($detalle->precio_unitario, 2) }}</td>
                                        <td class="text-end fw-bold">Bs {{ number_format($detalle->sub_total, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold pt-4">SUBTOTAL</td>
                                    <td class="text-end pt-4">Bs {{ number_format($venta->total + $venta->descuento, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold text-muted">DESCUENTO</td>
                                    <td class="text-end text-muted">- Bs {{ number_format($venta->descuento, 2) }}</td>
                                </tr>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold fs-4 text-danger">TOTAL</td>
                                    <td class="text-end fw-bold fs-4 text-danger">Bs {{ number_format($venta->total, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    @media print {
        .app-sidebar, .app-navbar, .btn-outline-secondary, .btn-app-primary {
            display: none !important;
        }
        .app-main {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        .card-elevated {
            box-shadow: none !important;
            border: 1px solid #eee !important;
        }
    }
</style>
@endpush
