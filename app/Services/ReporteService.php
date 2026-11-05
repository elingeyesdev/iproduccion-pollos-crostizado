<?php

namespace App\Services;

use App\Models\DetalleVenta;
use App\Models\IngresoInsumoDetalle;
use App\Models\Insumo;
use App\Models\Merma;
use App\Models\OrdenProduccion;
use App\Models\Produccion;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaPago;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Cálculo de los reportes, compartido por el panel web y la API.
 */
class ReporteService
{
    public function insumos(string $search = '', int $porPagina = 15): LengthAwarePaginator
    {
        return Insumo::query()
            ->with('unidadMedida:id,nombre,abreviatura')
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function productos(string $search = '', int $porPagina = 15): LengthAwarePaginator
    {
        return Producto::query()
            ->with('tipoProducto:id,nombre')
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate($porPagina)
            ->withQueryString();
    }

    public function stock(): array
    {
        $insumosBajoMinimo = Insumo::query()
            ->with('unidadMedida:id,nombre,abreviatura')
            ->where('estado', 1)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderBy('nombre')
            ->get();

        $productos = Producto::query()
            ->with('tipoProducto:id,nombre')
            ->where('estado', 1)
            ->orderBy('cantidad_actual')
            ->get();

        return [
            'insumos_bajo_minimo' => $insumosBajoMinimo,
            'productos' => $productos,
        ];
    }

    public function ventasMensual(int $mes, int $anio): array
    {
        $ventasDelMes = Venta::query()
            ->where('estado', 1)
            ->whereYear('fecha_venta', $anio)
            ->whereMonth('fecha_venta', $mes);

        $productosVendidos = DetalleVenta::query()
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->where('ventas.estado', 1)
            ->where('detalle_ventas.estado', 1)
            ->whereYear('ventas.fecha_venta', $anio)
            ->whereMonth('ventas.fecha_venta', $mes)
            ->selectRaw('productos.nombre as producto_nombre, SUM(detalle_ventas.cantidad) as cantidad_total, SUM(detalle_ventas.sub_total) as monto_total')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('cantidad_total')
            ->get();

        return [
            'mes' => $mes,
            'anio' => $anio,
            'total_ventas' => (clone $ventasDelMes)->count(),
            'total_monto' => (float) (clone $ventasDelMes)->sum('total'),
            'productos_vendidos' => $productosVendidos,
        ];
    }

    public function produccionMensual(int $mes, int $anio): array
    {
        $producciones = Produccion::query()
            ->where('estado', 1)
            ->whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->with(['ordenProduccionInsumo.insumo:id,nombre', 'producto', 'reglaConversion'])
            ->get();

        $totalOrdenes = $producciones
            ->pluck('ordenProduccionInsumo.orden_produccion_id')
            ->filter()
            ->unique()
            ->count();

        $insumosConsumidos = $producciones
            ->groupBy(fn (Produccion $p) => $p->ordenProduccionInsumo?->insumo?->nombre ?? 'Sin insumo')
            ->map(fn ($grupo, $nombre) => (object) [
                'nombre' => $nombre,
                'cantidad_total' => $grupo->sum(fn (Produccion $p) => (float) $p->cantidad_insumo),
            ])
            ->sortByDesc('cantidad_total')
            ->values();

        $productosObtenidos = $this->productosObtenidos($producciones);

        return [
            'mes' => $mes,
            'anio' => $anio,
            'total_ordenes' => $totalOrdenes,
            'insumos_consumidos' => $insumosConsumidos,
            'productos_obtenidos' => $productosObtenidos,
            'total_cantidad_obtenida' => (float) $productosObtenidos->sum('cantidad_total'),
        ];
    }

    /**
     * Normaliza el rango de fechas de los indicadores: por defecto el mes en curso hasta hoy,
     * e intercambia los extremos si vienen invertidos.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function rangoIndicadores(?string $desde, ?string $hasta): array
    {
        $desde = $desde ? Carbon::parse($desde)->startOfDay() : now()->startOfMonth();
        $hasta = $hasta ? Carbon::parse($hasta)->endOfDay() : now()->endOfDay();

        if ($hasta->lt($desde)) {
            [$desde, $hasta] = [$hasta->copy()->startOfDay(), $desde->copy()->endOfDay()];
        }

        return [$desde, $hasta];
    }

    public function indicadores(Carbon $desde, Carbon $hasta): array
    {
        // Período anterior de igual duración, para comparar tendencias.
        $dias = (int) $desde->diffInDays($hasta) + 1;
        $antHasta = $desde->copy()->subDay()->endOfDay();
        $antDesde = $antHasta->copy()->subDays($dias - 1)->startOfDay();

        $actual = $this->resumenPeriodo($desde, $hasta);
        $anterior = $this->resumenPeriodo($antDesde, $antHasta);

        $variaciones = collect($actual)
            ->only(['ingresos', 'num_ventas', 'ticket_promedio', 'clientes_atendidos', 'gasto_insumos', 'resultado_bruto', 'cantidad_obtenida', 'rendimiento', 'pct_merma'])
            ->map(fn ($valor, $clave) => $this->variacion((float) $valor, (float) $anterior[$clave]));

        $ventasPorDia = Venta::query()
            ->where('estado', 1)
            ->whereBetween('fecha_venta', [$desde->toDateString(), $hasta->toDateString()])
            ->selectRaw('fecha_venta, SUM(total) as monto, COUNT(*) as cantidad')
            ->groupBy('fecha_venta')
            ->orderBy('fecha_venta')
            ->get()
            ->keyBy(fn ($fila) => Carbon::parse($fila->fecha_venta)->toDateString());

        $serieVentas = collect(CarbonPeriod::create($desde->copy()->startOfDay(), $hasta->copy()->startOfDay()))
            ->map(fn (Carbon $dia) => [
                'dia' => $dia->toDateString(),
                'fecha' => $dia->format('d/m'),
                'monto' => (float) ($ventasPorDia[$dia->toDateString()]->monto ?? 0),
            ])
            ->values();

        $topProductos = DetalleVenta::query()
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->join('productos', 'productos.id', '=', 'detalle_ventas.producto_id')
            ->where('ventas.estado', 1)
            ->where('detalle_ventas.estado', 1)
            ->whereBetween('ventas.fecha_venta', [$desde->toDateString(), $hasta->toDateString()])
            ->selectRaw('productos.nombre, SUM(detalle_ventas.cantidad) as cantidad_total, SUM(detalle_ventas.sub_total) as monto_total')
            ->groupBy('productos.id', 'productos.nombre')
            ->orderByDesc('monto_total')
            ->limit(5)
            ->get();

        $topClientes = Venta::query()
            ->join('clientes', 'clientes.id', '=', 'ventas.cliente_id')
            ->where('ventas.estado', 1)
            ->whereBetween('ventas.fecha_venta', [$desde->toDateString(), $hasta->toDateString()])
            ->selectRaw('clientes.nombre, COUNT(ventas.id) as num_compras, SUM(ventas.total) as monto_total')
            ->groupBy('clientes.id', 'clientes.nombre')
            ->orderByDesc('monto_total')
            ->limit(5)
            ->get();

        $metodosPago = VentaPago::query()
            ->join('ventas', 'ventas.id', '=', 'venta_pagos.venta_id')
            ->where('ventas.estado', 1)
            ->where('venta_pagos.estado', 1)
            ->whereBetween('ventas.fecha_venta', [$desde->toDateString(), $hasta->toDateString()])
            ->selectRaw('venta_pagos.metodo_pago, SUM(venta_pagos.monto) as monto_total')
            ->groupBy('venta_pagos.metodo_pago')
            ->orderByDesc('monto_total')
            ->get();

        // Inventario: foto del momento actual, no depende del rango de fechas.
        $insumosBajoMinimo = Insumo::query()
            ->with('unidadMedida:id,abreviatura')
            ->where('estado', 1)
            ->whereColumn('stock_actual', '<=', 'stock_minimo')
            ->orderByRaw('stock_actual - stock_minimo')
            ->get();

        $productosActivos = Producto::query()->where('estado', 1);
        $productosSinStock = (clone $productosActivos)->where('cantidad_actual', '<=', 0)->orderBy('nombre')->get(['id', 'nombre']);
        $valorInventario = (float) (clone $productosActivos)->where('cantidad_actual', '>', 0)->sum(DB::raw('cantidad_actual * precio'));

        $productosVendidosIds = DetalleVenta::query()
            ->join('ventas', 'ventas.id', '=', 'detalle_ventas.venta_id')
            ->where('ventas.estado', 1)
            ->where('detalle_ventas.estado', 1)
            ->whereBetween('ventas.fecha_venta', [$desde->toDateString(), $hasta->toDateString()])
            ->distinct()
            ->pluck('detalle_ventas.producto_id');

        $productosSinRotacion = (clone $productosActivos)
            ->where('cantidad_actual', '>', 0)
            ->whereNotIn('id', $productosVendidosIds)
            ->orderByDesc('cantidad_actual')
            ->get(['id', 'nombre', 'cantidad_actual', 'precio']);

        $ordenesPendientes = OrdenProduccion::query()
            ->where('estado', 1)
            ->whereHas('insumos', fn ($q) => $q->where('estado', 1)->whereDoesntHave('produccion'))
            ->count();

        return [
            'desde' => $desde,
            'hasta' => $hasta,
            'ant_desde' => $antDesde,
            'ant_hasta' => $antHasta,
            'actual' => $actual,
            'anterior' => $anterior,
            'variaciones' => $variaciones,
            'serie_ventas' => $serieVentas,
            'top_productos' => $topProductos,
            'top_clientes' => $topClientes,
            'metodos_pago' => $metodosPago,
            'insumos_bajo_minimo' => $insumosBajoMinimo,
            'productos_sin_stock' => $productosSinStock,
            'valor_inventario' => $valorInventario,
            'productos_sin_rotacion' => $productosSinRotacion,
            'ordenes_pendientes' => $ordenesPendientes,
        ];
    }

    private function resumenPeriodo(Carbon $desde, Carbon $hasta): array
    {
        $ventas = Venta::query()
            ->where('estado', 1)
            ->whereBetween('fecha_venta', [$desde->toDateString(), $hasta->toDateString()]);

        $ingresos = (float) (clone $ventas)->sum('total');
        $numVentas = (clone $ventas)->count();

        $gastoInsumos = (float) IngresoInsumoDetalle::query()
            ->join('ingreso_insumos', 'ingreso_insumos.id', '=', 'ingreso_insumo_detalle.ingreso_insumo_id')
            ->where('ingreso_insumos.estado', 1)
            ->where('ingreso_insumo_detalle.estado', 1)
            ->whereBetween('ingreso_insumos.fecha_ingreso', [$desde->toDateString(), $hasta->toDateString()])
            ->sum(DB::raw('ingreso_insumo_detalle.cantidad * ingreso_insumo_detalle.precio_unitario'));

        $producciones = Produccion::query()
            ->where('estado', 1)
            ->whereBetween('created_at', [$desde, $hasta])
            ->with(['ordenProduccionInsumo', 'producto', 'reglaConversion'])
            ->get();

        $cantidadInsumo = (float) $producciones->sum(fn (Produccion $p) => (float) $p->cantidad_insumo);
        $cantidadObtenida = (float) $this->productosObtenidos($producciones)->sum('cantidad_total');

        $mermas = (float) Merma::query()
            ->where('estado', 1)
            ->whereBetween('created_at', [$desde, $hasta])
            ->sum('cantidad');

        $ordenesProgramadas = OrdenProduccion::query()
            ->where('estado', 1)
            ->whereBetween('fecha_programada', [$desde->toDateString(), $hasta->toDateString()]);

        $totalProgramadas = (clone $ordenesProgramadas)->count();
        $ejecutadas = (clone $ordenesProgramadas)
            ->whereHas('insumos', fn ($q) => $q->whereHas('produccion'))
            ->count();

        return [
            'ingresos' => $ingresos,
            'num_ventas' => $numVentas,
            'ticket_promedio' => $numVentas > 0 ? $ingresos / $numVentas : 0,
            'clientes_atendidos' => (clone $ventas)->distinct()->count('cliente_id'),
            'descuentos' => (float) (clone $ventas)->sum('descuento'),
            'gasto_insumos' => $gastoInsumos,
            'resultado_bruto' => $ingresos - $gastoInsumos,
            'margen_bruto' => $ingresos > 0 ? (($ingresos - $gastoInsumos) / $ingresos) * 100 : 0,
            'cantidad_insumo' => $cantidadInsumo,
            'cantidad_obtenida' => $cantidadObtenida,
            'rendimiento' => $cantidadInsumo > 0 ? $cantidadObtenida / $cantidadInsumo : 0,
            'mermas' => $mermas,
            'pct_merma' => $cantidadObtenida > 0 ? ($mermas / $cantidadObtenida) * 100 : 0,
            'ordenes_programadas' => $totalProgramadas,
            'ordenes_ejecutadas' => $ejecutadas,
            'cumplimiento' => $totalProgramadas > 0 ? ($ejecutadas / $totalProgramadas) * 100 : 0,
        ];
    }

    private function variacion(float $actual, float $anterior): ?float
    {
        if ($anterior == 0.0) {
            return null;
        }

        return round((($actual - $anterior) / abs($anterior)) * 100, 1);
    }

    // Dentro de una misma orden puede haber tanto ejecuciones por lote (varias líneas
    // que comparten el mismo cantidad_obtenida, incrementado una sola vez en Producto)
    // como ejecuciones individuales (cada una con su propio cantidad_obtenida). Se
    // agrupa por (orden, cantidad_obtenida) para contar cada lote una sola vez sin
    // perder las ejecuciones individuales que coinciden en la misma orden.
    private function productosObtenidos(Collection $producciones): Collection
    {
        return $producciones
            ->groupBy(fn (Produccion $p) => ($p->ordenProduccionInsumo?->orden_produccion_id ?? 'sin-orden') . '|' . $p->cantidad_obtenida)
            ->map(function ($grupoLote) {
                $primera = $grupoLote->first();

                $producto = $primera->producto ?? $primera->reglaConversion?->resolverProducto();

                return (object) [
                    'producto_nombre' => $producto?->nombre ?? 'Producto no determinado',
                    'cantidad' => (float) $primera->cantidad_obtenida,
                ];
            })
            ->groupBy('producto_nombre')
            ->map(fn ($grupo, $nombre) => (object) [
                'nombre' => $nombre,
                'cantidad_total' => $grupo->sum('cantidad'),
            ])
            ->sortByDesc('cantidad_total')
            ->values();
    }
}
