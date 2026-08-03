<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Models\Insumo;
use App\Models\Produccion;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function insumos(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $insumos = Insumo::query()
            ->with('unidadMedida:id,nombre,abreviatura')
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('reportes.insumos', compact('insumos', 'search'));
    }

    public function productos(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $productos = Producto::query()
            ->with('tipoProducto:id,nombre')
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('reportes.productos', compact('productos', 'search'));
    }

    public function stock(): View
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

        return view('reportes.stock', compact('insumosBajoMinimo', 'productos'));
    }

    public function ventasMensual(Request $request): View
    {
        $mes = (int) ($request->input('mes') ?: now()->month);
        $anio = (int) ($request->input('anio') ?: now()->year);

        $ventasDelMes = Venta::query()
            ->where('estado', 1)
            ->whereYear('fecha_venta', $anio)
            ->whereMonth('fecha_venta', $mes);

        $totalVentas = (clone $ventasDelMes)->count();
        $totalMonto = (clone $ventasDelMes)->sum('total');

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

        return view('reportes.ventas-mensual', compact('mes', 'anio', 'totalVentas', 'totalMonto', 'productosVendidos'));
    }

    public function produccionMensual(Request $request): View
    {
        $mes = (int) ($request->input('mes') ?: now()->month);
        $anio = (int) ($request->input('anio') ?: now()->year);

        $producciones = Produccion::query()
            ->where('estado', 1)
            ->whereYear('created_at', $anio)
            ->whereMonth('created_at', $mes)
            ->with(['ordenProduccionInsumo.insumo:id,nombre', 'producto', 'reglaConversion'])
            ->get();

        $totalOrdenes = $producciones->pluck('orden_produccion_id')->unique()->count();

        $insumosConsumidos = $producciones
            ->groupBy(fn (Produccion $p) => $p->ordenProduccionInsumo?->insumo?->nombre ?? 'Sin insumo')
            ->map(fn ($grupo, $nombre) => (object) [
                'nombre' => $nombre,
                'cantidad_total' => $grupo->sum(fn (Produccion $p) => (float) $p->cantidad_insumo),
            ])
            ->sortByDesc('cantidad_total')
            ->values();

        // Dentro de una misma orden puede haber tanto ejecuciones por lote (varias líneas
        // que comparten el mismo cantidad_obtenida, incrementado una sola vez en Producto)
        // como ejecuciones individuales (cada una con su propio cantidad_obtenida). Se
        // agrupa por (orden, cantidad_obtenida) para contar cada lote una sola vez sin
        // perder las ejecuciones individuales que coinciden en la misma orden.
        $productosObtenidos = $producciones
            ->groupBy(fn (Produccion $p) => $p->orden_produccion_id . '|' . $p->cantidad_obtenida)
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

        $totalCantidadObtenida = $productosObtenidos->sum('cantidad_total');

        return view('reportes.produccion-mensual', compact(
            'mes', 'anio', 'totalOrdenes', 'insumosConsumidos', 'productosObtenidos', 'totalCantidadObtenida'
        ));
    }
}
