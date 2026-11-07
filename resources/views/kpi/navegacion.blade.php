<div class="row g-3 mb-4">
    @php
        $secciones = [
            ['clave' => 'ventas', 'titulo' => 'Administración de Ventas', 'descripcion' => 'Ingresos, ventas, ticket promedio y clientes atendidos.', 'icono' => 'fa-solid fa-cash-register', 'ruta' => 'kpi.ventas'],
            ['clave' => 'finanzas', 'titulo' => 'Finanzas y Rentabilidad', 'descripcion' => 'Compras, resultado y margen bruto, descuentos e inventario.', 'icono' => 'fa-solid fa-chart-pie', 'ruta' => 'kpi.finanzas'],
            ['clave' => 'produccion', 'titulo' => 'Control de Producción', 'descripcion' => 'Producto obtenido, rendimiento, mermas y órdenes.', 'icono' => 'fa-solid fa-industry', 'ruta' => 'kpi.produccion'],
        ];
    @endphp

    @foreach ($secciones as $seccion)
        <div class="col-12 col-md-6 col-xl-4">
            <a href="{{ route($seccion['ruta'], request()->only('desde', 'hasta')) }}" class="card card-elevated h-100 text-decoration-none text-reset {{ $activo === $seccion['clave'] ? 'border-danger' : '' }}">
                <div class="card-body">
                    <h5 class="fw-bold"><i class="{{ $seccion['icono'] }} me-2 text-danger"></i>{{ $seccion['titulo'] }}</h5>
                    <p class="text-muted mb-0">{{ $seccion['descripcion'] }}</p>
                </div>
            </a>
        </div>
    @endforeach
</div>
