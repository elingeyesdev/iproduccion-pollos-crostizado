@extends('layouts.crud')

@section('title', 'Detalle de Venta #' . $venta->id)

@section('content')
<div class="venta-screen">
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
</div>

    {{-- Ticket de venta: solo visible al imprimir (ancho 12 cm) --}}
    <div class="ticket-print">
        <div class="ticket-center">
            <div class="ticket-title">{{ config('app.name') === 'Laravel' ? 'KROSTY-SADO' : config('app.name', 'KROSTY-SADO') }}</div>
            <div>Comprobante de venta #{{ $venta->id }}</div>
            <div>Fecha: {{ \Carbon\Carbon::parse($venta->fecha_venta)->format('d/m/Y') }}</div>
            <div>Cliente: {{ $venta->cliente?->nombre ?? 'N/A' }}</div>
        </div>

        <hr class="ticket-sep">

        <table class="ticket-table">
            <thead>
                <tr>
                    <th class="text-start">Producto</th>
                    <th class="text-center">Cant.</th>
                    <th class="text-end">P. Unit.</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($venta->detalles as $detalle)
                    <tr>
                        <td>{{ $detalle->producto?->nombre }}</td>
                        <td class="text-center">{{ number_format($detalle->cantidad, 0) }}</td>
                        <td class="text-end">{{ number_format($detalle->precio_unitario, 2) }}</td>
                        <td class="text-end">{{ number_format($detalle->sub_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <hr class="ticket-sep">

        <table class="ticket-table">
            <tr><td>SUBTOTAL</td><td class="text-end">Bs {{ number_format($venta->total + $venta->descuento, 2) }}</td></tr>
            <tr><td>DESCUENTO</td><td class="text-end">- Bs {{ number_format($venta->descuento, 2) }}</td></tr>
            <tr class="ticket-total"><td>TOTAL</td><td class="text-end">Bs {{ number_format($venta->total, 2) }}</td></tr>
        </table>

        @if($venta->pagos->isNotEmpty())
            <hr class="ticket-sep">
            <table class="ticket-table">
                @foreach($venta->pagos as $pago)
                    <tr><td>Pago {{ $pago->metodo_pago }}</td><td class="text-end">Bs {{ number_format($pago->monto, 2) }}</td></tr>
                @endforeach
            </table>
        @endif

        <hr class="ticket-sep">
        <div class="ticket-center">¡Gracias por su compra!</div>
    </div>
@endsection

@push('styles')
<style>
    .ticket-print { display: none; }

    @media print {
        @page {
            size: 120mm auto;
            margin: 4mm;
        }
        html, body {
            width: 120mm;
            margin: 0 !important;
            padding: 0 !important;
            background: #fff !important;
        }
        .app-sidebar, .app-navbar, .app-footer, .offcanvas, .venta-screen {
            display: none !important;
        }
        .app-shell, .app-main, main, main > .container-fluid {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
            min-height: 0 !important;
            width: auto !important;
        }
        .ticket-print {
            display: block;
            width: 112mm;
            font-family: 'Courier New', monospace;
            font-size: 9pt;
            color: #000;
        }
        .ticket-center { text-align: center; }
        .ticket-title { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .ticket-sep { border: 0; border-top: 1px dashed #000; margin: 2mm 0; opacity: 1; }
        .ticket-table { width: 100%; border-collapse: collapse; }
        .ticket-table th, .ticket-table td { padding: 0.5mm 0; vertical-align: top; }
        .ticket-table th { border-bottom: 1px dashed #000; }
        .ticket-total td { font-size: 11pt; font-weight: bold; }
    }
</style>
@endpush
