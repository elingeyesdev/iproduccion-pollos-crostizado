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
use OpenApi\Attributes as OA;

class ProduccionApiController extends Controller
{
    /**
     * Órdenes de producción con insumos pendientes de ejecutar.
     */
    #[OA\Get(
        path: '/api/producciones/pendientes',
        summary: 'Listar órdenes pendientes de producción',
        description: 'Órdenes de producción que todavía tienen insumos pendientes de ejecutar. Paginado (10 por página); permite buscar por número de orden.',
        tags: ['Producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Filtra por número de orden', schema: new OA\Schema(type: 'string', example: 'OR-PRO-2026-10')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Órdenes pendientes obtenidas correctamente (paginadas)'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
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
    #[OA\Get(
        path: '/api/producciones/lote/{ordenProduccion}',
        summary: 'Obtener datos para ejecutar el lote de una orden',
        description: 'Devuelve la orden, su receta (regla de conversión), el producto resultante, los insumos pendientes y el indicador lote_ejecutable.',
        tags: ['Producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'ordenProduccion', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Datos del lote obtenidos correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'orden_produccion', type: 'object'),
                        new OA\Property(property: 'regla', type: 'object', nullable: true),
                        new OA\Property(property: 'producto', type: 'object', nullable: true),
                        new OA\Property(property: 'detalles', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'lote_ejecutable', type: 'boolean', example: true),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
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
    #[OA\Post(
        path: '/api/producciones/lote/{ordenProduccion}',
        summary: 'Ejecutar la producción del lote de una orden',
        description: 'Crea un registro de producción por cada insumo pendiente de la orden, descuenta el stock (stock_actual) de cada insumo consumido y suma la cantidad obtenida al stock (cantidad_actual) del producto resultante. La cantidad obtenida debe estar dentro del rango de rendimiento de la receta.',
        tags: ['Producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'ordenProduccion', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['cantidad_obtenida'],
                properties: [
                    new OA\Property(property: 'cantidad_obtenida', type: 'number', format: 'float', minimum: 0.01, example: 40),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Producción ejecutada correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string', example: 'Producción ejecutada correctamente. Se descontaron 2 insumo(s) y se generaron 40.00 unidades de Pollo frito.'),
                        new OA\Property(property: 'data', type: 'array', items: new OA\Items(type: 'object')),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Stock insuficiente de algún insumo'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación (orden sin receta, sin producto resultante, sin insumos pendientes o cantidad fuera del rango de rendimiento)'),
        ]
    )]
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
    #[OA\Get(
        path: '/api/producciones',
        summary: 'Listar historial de producciones',
        description: 'Producciones ejecutadas activas, paginadas (10 por página); permite buscar por número de orden.',
        tags: ['Producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Filtra por número de orden', schema: new OA\Schema(type: 'string', example: 'OR-PRO-2026-10')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producciones obtenidas correctamente (paginadas)'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
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

    #[OA\Get(
        path: '/api/producciones/{produccion}',
        summary: 'Ver producción',
        description: 'Devuelve la producción con su receta, producto, orden de producción, insumo y mermas.',
        tags: ['Producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'produccion', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producción obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
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
