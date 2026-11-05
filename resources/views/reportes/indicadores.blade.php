@extends('layouts.crud')

@section('title', 'Indicadores de gestión')

@php
    // [clave, etiqueta, valor formateado, icono, ¿subir es bueno?, ayuda]
    $kpis = [
        'Ventas' => [
            ['ingresos', 'Ingresos por ventas', 'Bs ' . number_format($actual['ingresos'], 2), 'fa-solid fa-sack-dollar', true, 'Total de ventas activas del período.'],
            ['num_ventas', 'N° de ventas', number_format($actual['num_ventas']), 'fa-solid fa-receipt', true, 'Cantidad de ventas registradas.'],
            ['ticket_promedio', 'Ticket promedio', 'Bs ' . number_format($actual['ticket_promedio'], 2), 'fa-solid fa-tag', true, 'Ingresos ÷ N° de ventas.'],
            ['clientes_atendidos', 'Clientes atendidos', number_format($actual['clientes_atendidos']), 'fa-solid fa-users', true, 'Clientes distintos que compraron.'],
        ],
        'Rentabilidad' => [
            ['gasto_insumos', 'Compras de insumos', 'Bs ' . number_format($actual['gasto_insumos'], 2), 'fa-solid fa-truck-ramp-box', false, 'Cantidad × precio unitario de los ingresos de insumos.'],
            ['resultado_bruto', 'Resultado bruto', 'Bs ' . number_format($actual['resultado_bruto'], 2), 'fa-solid fa-scale-balanced', true, 'Ingresos por ventas − compras de insumos. Margen: ' . number_format($actual['margen_bruto'], 1) . '%.'],
            [null, 'Descuentos otorgados', 'Bs ' . number_format($actual['descuentos'], 2), 'fa-solid fa-percent', false, 'Suma de descuentos aplicados en ventas.'],
            [null, 'Valor del inventario', 'Bs ' . number_format($valorInventario, 2), 'fa-solid fa-warehouse', true, 'Stock actual de productos × precio de venta.'],
        ],
        'Producción' => [
            ['cantidad_obtenida', 'Producto obtenido', number_format($actual['cantidad_obtenida'], 2), 'fa-solid fa-drumstick-bite', true, 'Unidades producidas en el período.'],
            ['rendimiento', 'Rendimiento', number_format($actual['rendimiento'], 2) . ' x', 'fa-solid fa-gauge-high', true, 'Producto obtenido ÷ insumo consumido (' . number_format($actual['cantidad_insumo'], 2) . ').'],
            ['pct_merma', '% de merma', number_format($actual['pct_merma'], 1) . '%', 'fa-solid fa-trash-can', false, 'Mermas (' . number_format($actual['mermas'], 2) . ') ÷ producto obtenido.'],
            [null, 'Cumplimiento de órdenes', number_format($actual['cumplimiento'], 0) . '%', 'fa-solid fa-clipboard-check', true, $actual['ordenes_ejecutadas'] . ' de ' . $actual['ordenes_programadas'] . ' órdenes programadas ejecutadas. Pendientes totales: ' . $ordenesPendientes . '.'],
        ],
    ];

    $maxProducto = (float) ($topProductos->max('monto_total') ?: 1);
    $maxCliente = (float) ($topClientes->max('monto_total') ?: 1);
    $totalPagos = (float) ($metodosPago->sum('monto_total') ?: 1);
@endphp

@section('content')
    <x-crud.page-header title="Indicadores de gestión" subtitle="Métricas clave para la toma de decisiones del administrador.">
        <x-slot:actions>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('reportes.indicadores') }}" class="row g-2 align-items-end">
                <div class="col-md-4">
                    <label for="desde" class="form-label fw-semibold">Desde</label>
                    <input id="desde" type="date" name="desde" value="{{ $desde->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="hasta" class="form-label fw-semibold">Hasta</label>
                    <input id="hasta" type="date" name="hasta" value="{{ $hasta->toDateString() }}" class="form-control">
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Consultar</button>
                    <a href="{{ route('reportes.indicadores') }}" class="btn btn-outline-secondary">Mes actual</a>
                </div>
            </form>
            <p class="text-muted small mb-0 mt-2">
                Las variaciones comparan contra el período anterior de igual duración
                ({{ $antDesde->format('d/m/Y') }} – {{ $antHasta->format('d/m/Y') }}).
            </p>
        </div>
    </div>

    {{-- ===== ALERTAS ===== --}}
    @if ($insumosBajoMinimo->isNotEmpty() || $productosSinStock->isNotEmpty() || $ordenesPendientes > 0)
        <div class="alert alert-warning d-flex flex-column gap-1 mb-4">
            <strong><i class="fa-solid fa-triangle-exclamation me-1"></i> Requiere atención</strong>
            @if ($insumosBajoMinimo->isNotEmpty())
                <span>{{ $insumosBajoMinimo->count() }} insumo(s) en o bajo el stock mínimo — planificar compra.</span>
            @endif
            @if ($productosSinStock->isNotEmpty())
                <span>{{ $productosSinStock->count() }} producto(s) sin stock — posible venta perdida.</span>
            @endif
            @if ($ordenesPendientes > 0)
                <span>{{ $ordenesPendientes }} orden(es) de producción pendientes de ejecutar.</span>
            @endif
        </div>
    @endif

    {{-- ===== KPIs ===== --}}
    @foreach ($kpis as $grupo => $tarjetas)
        <h5 class="fw-bold mb-2">{{ $grupo }}</h5>
        <div class="row g-3 mb-4">
            @foreach ($tarjetas as [$clave, $etiqueta, $valor, $icono, $subirEsBueno, $ayuda])
                @php
                    $pct = $clave ? $variaciones[$clave] : null;
                    $color = $pct === null || $pct == 0 ? 'text-muted' : (($pct > 0) === $subirEsBueno ? 'text-success' : 'text-danger');
                @endphp
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="card card-elevated h-100">
                        <div class="card-body">
                            <p class="text-muted small mb-1"><i class="{{ $icono }} me-1"></i> {{ $etiqueta }}</p>
                            <h3 class="fw-bold mb-1">{{ $valor }}</h3>
                            @if ($clave)
                                <span class="small fw-semibold {{ $color }}">
                                    @if ($pct === null)
                                        Sin datos del período anterior
                                    @else
                                        <i class="fa-solid {{ $pct > 0 ? 'fa-arrow-trend-up' : ($pct < 0 ? 'fa-arrow-trend-down' : 'fa-minus') }}"></i>
                                        {{ $pct > 0 ? '+' : '' }}{{ $pct }}% vs período anterior
                                    @endif
                                </span>
                            @endif
                            <p class="text-muted small mb-0 mt-1">{{ $ayuda }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach

    {{-- ===== TENDENCIA DE VENTAS ===== --}}
    <div class="card card-elevated mb-4">
        <div class="card-body">
            <h5 class="fw-bold mb-3">Ventas diarias (Bs)</h5>
            <div style="position: relative; height: 280px;">
                <canvas id="chartVentas"></canvas>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        {{-- ===== TOP PRODUCTOS ===== --}}
        <div class="col-lg-6">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top 5 productos por ingresos</h5>
                    @forelse ($topProductos as $fila)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small">
                                <span class="fw-semibold">{{ $fila->nombre }}</span>
                                <span>Bs {{ number_format((float) $fila->monto_total, 2) }} · {{ number_format((float) $fila->cantidad_total, 2) }} u.</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: {{ ((float) $fila->monto_total / $maxProducto) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay ventas en este período.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- ===== TOP CLIENTES ===== --}}
        <div class="col-lg-6">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Top 5 clientes</h5>
                    @forelse ($topClientes as $fila)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small">
                                <span class="fw-semibold">{{ $fila->nombre }}</span>
                                <span>Bs {{ number_format((float) $fila->monto_total, 2) }} · {{ $fila->num_compras }} compra(s)</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-danger" style="width: {{ ((float) $fila->monto_total / $maxCliente) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted mb-0">No hay ventas en este período.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- ===== MÉTODOS DE PAGO ===== --}}
        <div class="col-lg-4">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Ingresos por método de pago</h5>
                    <table class="table table-sm align-middle mb-0">
                        <tbody>
                            @forelse ($metodosPago as $fila)
                                <tr>
                                    <td class="fw-semibold">{{ $fila->metodo_pago }}</td>
                                    <td class="text-end">Bs {{ number_format((float) $fila->monto_total, 2) }}</td>
                                    <td class="text-end text-muted">{{ number_format(((float) $fila->monto_total / $totalPagos) * 100, 1) }}%</td>
                                </tr>
                            @empty
                                <tr><td class="text-muted">Sin pagos registrados.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===== INSUMOS CRÍTICOS ===== --}}
        <div class="col-lg-4">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">Insumos bajo stock mínimo</h5>
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr><th>Insumo</th><th class="text-end">Actual</th><th class="text-end">Mínimo</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($insumosBajoMinimo as $insumo)
                                <tr>
                                    <td class="fw-semibold">{{ $insumo->nombre }}</td>
                                    <td class="text-end text-danger">{{ $insumo->stock_actual }} {{ $insumo->unidadMedida?->abreviatura }}</td>
                                    <td class="text-end">{{ $insumo->stock_minimo }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Todos los insumos están sobre el mínimo.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ===== PRODUCTOS SIN ROTACIÓN ===== --}}
        <div class="col-lg-4">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <h5 class="fw-bold mb-1">Productos sin rotación</h5>
                    <p class="text-muted small">Con stock, pero sin ventas en el período.</p>
                    <table class="table table-sm align-middle mb-0">
                        <thead>
                            <tr><th>Producto</th><th class="text-end">Stock</th><th class="text-end">Valor</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($productosSinRotacion as $producto)
                                <tr>
                                    <td class="fw-semibold">{{ $producto->nombre }}</td>
                                    <td class="text-end">{{ $producto->cantidad_actual }}</td>
                                    <td class="text-end">Bs {{ number_format($producto->cantidad_actual * (float) $producto->precio, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-muted">Todos los productos con stock tuvieron ventas.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
        (function () {
            const serie = @json($serieVentas);

            new Chart(document.getElementById('chartVentas'), {
                type: 'bar',
                data: {
                    labels: serie.map(d => d.fecha),
                    datasets: [{
                        label: 'Ventas (Bs)',
                        data: serie.map(d => d.monto),
                        backgroundColor: 'rgba(179, 0, 0, 0.75)',
                        borderRadius: 4,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { y: { beginAtZero: true } },
                },
            });
        })();
    </script>
@endpush
