<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use App\Models\OrdenProduccion;
use App\Models\OrdenProduccionInsumo;
use App\Models\Produccion;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProduccionApiController extends Controller
{
    /**
     * Órdenes de producción con insumos pendientes de ejecutar.
     */
    public function pendientes(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $ordenesPendientes = OrdenProduccion::query()
            ->with('reglaConversion.producto')
            ->withCount(['insumos as insumos_pendientes_count' => function ($query) {
                $query->where('estado', 1)->whereDoesntHave('produccion');
            }])
            ->having('insumos_pendientes_count', '>', 0)
            ->when($search, function ($query) use ($search) {
                $query->where('numero_orden', 'like', "%{$search}%");
            })
            ->orderByDesc('fecha_programada')
            ->paginate(10)
            ->withQueryString();

        return response()->json($ordenesPendientes);
    }

    /**
     * Datos necesarios para ejecutar el lote completo de una orden de producción.
     */
    public function loteCreate(OrdenProduccion $ordenProduccion)
    {
        $ordenProduccion->loadMissing([
            'reglaConversion.insumo',
            'reglaConversion.producto',
            'reglaConversion.recetaConversiones.insumo',
            'reglaConversion.recetaConversiones.unidadMedida',
        ]);

        $regla = $ordenProduccion->reglaConversion;
        $producto = $regla?->resolverProducto();

        $detalles = OrdenProduccionInsumo::query()
            ->where('orden_produccion_id', $ordenProduccion->id)
            ->where('estado', 1)
            ->whereDoesntHave('produccion')
            ->with('insumo.unidadMedida')
            ->orderBy('id')
            ->get();

        $loteEjecutable = (bool) $regla && (bool) $producto && $detalles->isNotEmpty();

        return response()->json([
            'orden_produccion' => $ordenProduccion,
            'regla' => $regla,
            'producto' => $producto,
            'detalles' => $detalles,
            'lote_ejecutable' => $loteEjecutable,
        ]);
    }

    /**
     * Ejecuta la producción del lote completo pendiente de una orden.
     */
    public function loteStore(Request $request, OrdenProduccion $ordenProduccion)
    {
        $data = $request->validate([
            'cantidad_obtenida' => 'required|numeric|min:0.01',
        ]);

        $cantidadObtenida = (float) $data['cantidad_obtenida'];

        $regla = $ordenProduccion->reglaConversion;

        if (!$regla) {
            return response()->json([
                'message' => 'Esta orden no tiene una receta (regla de conversión) asignada.',
                'errors' => ['cantidad_obtenida' => ['Esta orden no tiene una receta (regla de conversión) asignada.']],
            ], 422);
        }

        $producto = $regla->resolverProducto();

        if (!$producto) {
            return response()->json([
                'message' => 'No se pudo determinar el producto resultante de la receta asignada.',
                'errors' => ['cantidad_obtenida' => ['No se pudo determinar el producto resultante de la receta asignada.']],
            ], 422);
        }

        $detalles = OrdenProduccionInsumo::query()
            ->where('orden_produccion_id', $ordenProduccion->id)
            ->where('estado', 1)
            ->whereDoesntHave('produccion')
            ->with('insumo')
            ->orderBy('id')
            ->get();

        if ($detalles->isEmpty()) {
            return response()->json([
                'message' => 'Esta orden no tiene insumos pendientes de producción.',
                'errors' => ['cantidad_obtenida' => ['Esta orden no tiene insumos pendientes de producción.']],
            ], 422);
        }

        $detallePrincipal = $detalles->firstWhere('insumo_id', $regla->insumo_id) ?? $detalles->first();
        $cantidadPlanificada = (float) ($detallePrincipal?->cantidad ?? 0);

        if (!$regla->aceptaCantidadObtenida($cantidadObtenida, $cantidadPlanificada)) {
            $mensajeRendimiento = $regla->mensajeRendimiento($cantidadObtenida, $cantidadPlanificada);
            $rango = $regla->rangoRendimiento($cantidadPlanificada);

            return response()->json([
                'message' => $mensajeRendimiento,
                'errors' => ['cantidad_obtenida' => [$mensajeRendimiento]],
                'rendimiento' => [
                    'cantidad_planificada' => $cantidadPlanificada,
                    'minimo' => $rango['minimo'],
                    'maximo' => $rango['maximo'],
                ],
            ], 422);
        }

        try {
            $producciones = DB::transaction(function () use ($detalles, $cantidadObtenida, $ordenProduccion, $regla, $producto) {
                $creadas = collect();

                foreach ($detalles as $detalle) {
                    $produccion = Produccion::create([
                        'orden_produccion_insumo_id' => $detalle->id,
                        'regla_conversion_id' => $regla->id,
                        'producto_id' => $producto->id,
                        'cantidad_insumo' => (float) $detalle->cantidad,
                        'cantidad_obtenida' => $cantidadObtenida,
                        'nivel' => 0,
                        'estado' => 1,
                    ]);

                    $creadas->push($produccion);

                    $this->descontarStockInsumo($detalle);
                }

                Producto::whereKey($producto->id)->increment('cantidad_actual', (int) round($cantidadObtenida));

                return $creadas;
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
                'errors' => ['cantidad_obtenida' => [$e->getMessage()]],
            ], 400);
        }

        $producciones->load([
            'reglaConversion.insumo.unidadMedida',
            'reglaConversion.producto',
            'producto',
            'ordenProduccionInsumo.insumo.unidadMedida',
        ]);

        return response()->json([
            'message' => 'Producción ejecutada correctamente. Se descontaron ' . $producciones->count() . ' insumo(s) y se generaron ' . number_format($cantidadObtenida, 2) . ' unidades de ' . $producto->nombre . '.',
            'data' => $producciones,
        ], 201);
    }

    /**
     * Historial de producciones ejecutadas.
     */
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $producciones = Produccion::query()
            ->conDetalleOrden()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('ordenProduccion', function ($q) use ($search) {
                    $q->where('numero_orden', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return response()->json($producciones);
    }

    public function show(Produccion $produccion)
    {
        $produccion->loadMissing([
            'reglaConversion.insumo.unidadMedida',
            'reglaConversion.unidadEntrada',
            'reglaConversion.unidadSalida',
            'reglaConversion.producto',
            'producto',
            'ordenProduccion.insumos.insumo.unidadMedida',
            'ordenProduccionInsumo.insumo.unidadMedida',
            'mermas',
        ]);

        return response()->json($produccion);
    }

    private function descontarStockInsumo(OrdenProduccionInsumo $detalle): void
    {
        $insumo = Insumo::whereKey($detalle->insumo_id)->lockForUpdate()->firstOrFail();

        $cantidadConsumida = round((float) $detalle->cantidad, 2);

        if ((float) $insumo->stock_actual < $cantidadConsumida) {
            throw new \RuntimeException(
                "Stock insuficiente para el insumo \"{$insumo->nombre}\". Disponible: {$insumo->stock_actual}, requerido: {$cantidadConsumida}."
            );
        }

        $insumo->decrement('stock_actual', $cantidadConsumida);
    }
}
