<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\OrdenProduccion;
use App\Models\OrdenProduccionInsumo;
use App\Models\Produccion;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProduccionController extends Controller
{
    public function loteCreate(OrdenProduccion $ordenProduccion): View
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

        $detallePrincipal = $detalles->firstWhere('insumo_id', $regla?->insumo_id) ?? $detalles->first();
        $cantidadPlanificada = (float) ($detallePrincipal?->cantidad ?? 0);
        $rangoRendimiento = $regla?->rangoRendimiento($cantidadPlanificada) ?? ['minimo' => 0, 'maximo' => 0];
        $loteEjecutable = (bool) $regla && (bool) $producto && $detalles->isNotEmpty();

        return view('produccion.lote-create', compact('ordenProduccion', 'detalles', 'regla', 'producto', 'cantidadPlanificada', 'rangoRendimiento', 'loteEjecutable'));
    }

    public function loteStore(Request $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $data = $request->validate([
            'cantidad_obtenida' => 'required|numeric|min:0.01',
        ]);

        $cantidadObtenida = (float) $data['cantidad_obtenida'];

        $regla = $ordenProduccion->reglaConversion;

        if (!$regla) {
            return Redirect::back()
                ->withErrors(['cantidad_obtenida' => 'Esta orden no tiene una receta (regla de conversión) asignada.']);
        }

        $producto = $regla->resolverProducto();

        if (!$producto) {
            return Redirect::back()
                ->withErrors(['cantidad_obtenida' => 'No se pudo determinar el producto resultante de la receta asignada.']);
        }

        $detalles = OrdenProduccionInsumo::query()
            ->where('orden_produccion_id', $ordenProduccion->id)
            ->where('estado', 1)
            ->whereDoesntHave('produccion')
            ->with('insumo')
            ->orderBy('id')
            ->get();

        if ($detalles->isEmpty()) {
            return Redirect::back()
                ->withErrors(['cantidad_obtenida' => 'Esta orden no tiene insumos pendientes de producción.']);
        }

        $detallePrincipal = $detalles->firstWhere('insumo_id', $regla->insumo_id) ?? $detalles->first();
        $cantidadPlanificada = (float) ($detallePrincipal?->cantidad ?? 0);

        if (!$regla->aceptaCantidadObtenida($cantidadObtenida, $cantidadPlanificada)) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['cantidad_obtenida' => $regla->mensajeRendimiento($cantidadObtenida, $cantidadPlanificada)]);
        }

        try {
            DB::transaction(function () use ($detalles, $cantidadObtenida, $ordenProduccion, $regla, $producto) {
                foreach ($detalles as $detalle) {
                    Produccion::create([
                        'orden_produccion_insumo_id' => $detalle->id,
                        'regla_conversion_id' => $regla->id,
                        'producto_id' => $producto->id,
                        'cantidad_insumo' => (float) $detalle->cantidad,
                        'cantidad_obtenida' => $cantidadObtenida,
                        'nivel' => 0,
                        'estado' => 1,
                    ]);

                    $this->descontarStockInsumo($detalle);
                }

                Producto::whereKey($producto->id)->increment('cantidad_actual', (int) round($cantidadObtenida));
            });
        } catch (\RuntimeException $e) {
            return Redirect::back()
                ->withErrors(['cantidad_obtenida' => $e->getMessage()]);
        }

        return Redirect::route('orden-producciones.show', $ordenProduccion)
            ->with('success', 'Producción ejecutada correctamente. Se descontaron ' . count($detalles) . ' insumo(s) y se generaron ' . number_format($cantidadObtenida, 2) . ' unidades de ' . $producto->nombre . '.');
    }

    public function index(Request $request): View
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
            ->paginate(10, ['*'], 'pendientes')
            ->withQueryString();

        $producciones = Produccion::query()
            ->conDetalleOrden()
            ->where('estado', 1)
            ->orderByDesc('id')
            ->paginate(10, ['*'], 'historial');

        return view('produccion.index', compact('ordenesPendientes', 'producciones', 'search'));
    }

    public function show(Produccion $produccion): View
    {
        $relations = [
            'reglaConversion.insumo.unidadMedida',
            'reglaConversion.unidadEntrada',
            'reglaConversion.unidadSalida',
            'reglaConversion.producto',
            'producto',
            'ordenProduccion.insumos.insumo.unidadMedida',
            'ordenProduccionInsumo.insumo.unidadMedida',
            'mermas',
        ];

        $produccion->loadMissing($relations);

        return view('produccion.show', compact('produccion'));
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
