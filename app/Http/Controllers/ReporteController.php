<?php

namespace App\Http\Controllers;

use App\Services\ReporteService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReporteController extends Controller
{
    public function __construct(private readonly ReporteService $reportes)
    {
    }

    public function insumos(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $insumos = $this->reportes->insumos($search);

        return view('reportes.insumos', compact('insumos', 'search'));
    }

    public function productos(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $productos = $this->reportes->productos($search);

        return view('reportes.productos', compact('productos', 'search'));
    }

    public function stock(): View
    {
        $reporte = $this->reportes->stock();

        return view('reportes.stock', [
            'insumosBajoMinimo' => $reporte['insumos_bajo_minimo'],
            'productos' => $reporte['productos'],
        ]);
    }

    public function ventasMensual(Request $request): View
    {
        $reporte = $this->reportes->ventasMensual(
            (int) ($request->input('mes') ?: now()->month),
            (int) ($request->input('anio') ?: now()->year),
        );

        return view('reportes.ventas-mensual', [
            'mes' => $reporte['mes'],
            'anio' => $reporte['anio'],
            'totalVentas' => $reporte['total_ventas'],
            'totalMonto' => $reporte['total_monto'],
            'productosVendidos' => $reporte['productos_vendidos'],
        ]);
    }

    public function produccionMensual(Request $request): View
    {
        $reporte = $this->reportes->produccionMensual(
            (int) ($request->input('mes') ?: now()->month),
            (int) ($request->input('anio') ?: now()->year),
        );

        return view('reportes.produccion-mensual', [
            'mes' => $reporte['mes'],
            'anio' => $reporte['anio'],
            'totalOrdenes' => $reporte['total_ordenes'],
            'insumosConsumidos' => $reporte['insumos_consumidos'],
            'productosObtenidos' => $reporte['productos_obtenidos'],
            'totalCantidadObtenida' => $reporte['total_cantidad_obtenida'],
        ]);
    }

    public function indicadores(Request $request): View
    {
        [$desde, $hasta] = $this->reportes->rangoIndicadores($request->input('desde'), $request->input('hasta'));
        $reporte = $this->reportes->indicadores($desde, $hasta);

        return view('reportes.indicadores', [
            'desde' => $reporte['desde'],
            'hasta' => $reporte['hasta'],
            'antDesde' => $reporte['ant_desde'],
            'antHasta' => $reporte['ant_hasta'],
            'actual' => $reporte['actual'],
            'variaciones' => $reporte['variaciones'],
            'serieVentas' => $reporte['serie_ventas'],
            'topProductos' => $reporte['top_productos'],
            'topClientes' => $reporte['top_clientes'],
            'metodosPago' => $reporte['metodos_pago'],
            'insumosBajoMinimo' => $reporte['insumos_bajo_minimo'],
            'productosSinStock' => $reporte['productos_sin_stock'],
            'valorInventario' => $reporte['valor_inventario'],
            'productosSinRotacion' => $reporte['productos_sin_rotacion'],
            'ordenesPendientes' => $reporte['ordenes_pendientes'],
        ]);
    }
}
