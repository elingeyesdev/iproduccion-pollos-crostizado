<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReporteService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class ReporteApiController extends Controller
{
    public function __construct(private readonly ReporteService $reportes)
    {
    }

    #[OA\Get(
        path: '/api/reportes/insumos',
        summary: 'Reporte de insumos',
        description: 'Insumos activos con su stock y unidad de medida. Paginado (15 por página); permite buscar por nombre.',
        tags: ['Reportes'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Filtra por nombre del insumo', schema: new OA\Schema(type: 'string', example: 'Pollo')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Insumos obtenidos correctamente (paginados)'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function insumos(Request $request)
    {
        return response()->json($this->reportes->insumos(trim((string) $request->input('search'))));
    }

    #[OA\Get(
        path: '/api/reportes/productos',
        summary: 'Reporte de productos',
        description: 'Productos activos con su stock, precio y tipo. Paginado (15 por página); permite buscar por nombre.',
        tags: ['Reportes'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Filtra por nombre del producto', schema: new OA\Schema(type: 'string', example: 'Porción')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Productos obtenidos correctamente (paginados)'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function productos(Request $request)
    {
        return response()->json($this->reportes->productos(trim((string) $request->input('search'))));
    }

    #[OA\Get(
        path: '/api/reportes/stock',
        summary: 'Reporte de stock',
        description: 'Insumos con stock igual o menor al mínimo y todos los productos activos ordenados por menor stock.',
        tags: ['Reportes'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Stock obtenido correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'insumos_bajo_minimo', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'productos', type: 'array', items: new OA\Items(type: 'object')),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function stock()
    {
        return response()->json($this->reportes->stock());
    }

    #[OA\Get(
        path: '/api/reportes/ventas-mensual',
        summary: 'Reporte mensual de ventas',
        description: 'Cantidad de ventas, monto total y productos vendidos del mes indicado (por defecto, el mes actual).',
        tags: ['Reportes'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'mes', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 12, example: 10)),
            new OA\Parameter(name: 'anio', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 2026)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reporte de ventas obtenido correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'mes', type: 'integer', example: 10),
                        new OA\Property(property: 'anio', type: 'integer', example: 2026),
                        new OA\Property(property: 'total_ventas', type: 'integer', example: 120),
                        new OA\Property(property: 'total_monto', type: 'number', format: 'float', example: 8450.5),
                        new OA\Property(property: 'productos_vendidos', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'producto_nombre', type: 'string'),
                                new OA\Property(property: 'cantidad_total', type: 'number'),
                                new OA\Property(property: 'monto_total', type: 'number'),
                            ]
                        )),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Mes o año inválido'),
        ]
    )]
    public function ventasMensual(Request $request)
    {
        [$mes, $anio] = $this->mesAnio($request);

        return response()->json($this->reportes->ventasMensual($mes, $anio));
    }

    #[OA\Get(
        path: '/api/reportes/produccion-mensual',
        summary: 'Reporte mensual de producción',
        description: 'Órdenes ejecutadas, insumos consumidos y productos obtenidos en el mes indicado (por defecto, el mes actual).',
        tags: ['Reportes'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'mes', in: 'query', required: false, schema: new OA\Schema(type: 'integer', minimum: 1, maximum: 12, example: 10)),
            new OA\Parameter(name: 'anio', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 2026)),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Reporte de producción obtenido correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'mes', type: 'integer', example: 10),
                        new OA\Property(property: 'anio', type: 'integer', example: 2026),
                        new OA\Property(property: 'total_ordenes', type: 'integer', example: 15),
                        new OA\Property(property: 'insumos_consumidos', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'nombre', type: 'string'),
                                new OA\Property(property: 'cantidad_total', type: 'number'),
                            ]
                        )),
                        new OA\Property(property: 'productos_obtenidos', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'nombre', type: 'string'),
                                new OA\Property(property: 'cantidad_total', type: 'number'),
                            ]
                        )),
                        new OA\Property(property: 'total_cantidad_obtenida', type: 'number', example: 600),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Mes o año inválido'),
        ]
    )]
    public function produccionMensual(Request $request)
    {
        [$mes, $anio] = $this->mesAnio($request);

        return response()->json($this->reportes->produccionMensual($mes, $anio));
    }

    #[OA\Get(
        path: '/api/reportes/indicadores',
        summary: 'Indicadores del negocio',
        description: 'KPIs de ventas, producción e inventario del rango indicado (por defecto, del inicio del mes hasta hoy), con su variación porcentual respecto al período anterior de igual duración. La variación es null cuando el período anterior vale 0.',
        tags: ['Reportes'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'desde', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-10-01')),
            new OA\Parameter(name: 'hasta', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date', example: '2026-10-28')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Indicadores obtenidos correctamente',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'desde', type: 'string', format: 'date'),
                        new OA\Property(property: 'hasta', type: 'string', format: 'date'),
                        new OA\Property(property: 'ant_desde', type: 'string', format: 'date'),
                        new OA\Property(property: 'ant_hasta', type: 'string', format: 'date'),
                        new OA\Property(property: 'actual', type: 'object', description: 'ingresos, num_ventas, ticket_promedio, clientes_atendidos, descuentos, gasto_insumos, resultado_bruto, margen_bruto, cantidad_insumo, cantidad_obtenida, rendimiento, mermas, pct_merma, ordenes_programadas, ordenes_ejecutadas, cumplimiento'),
                        new OA\Property(property: 'anterior', type: 'object', description: 'Mismos campos que "actual" para el período anterior'),
                        new OA\Property(property: 'variaciones', type: 'object', description: 'Variación % por indicador (null si no hay base de comparación)'),
                        new OA\Property(property: 'serie_ventas', type: 'array', items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'dia', type: 'string', format: 'date'),
                                new OA\Property(property: 'fecha', type: 'string', example: '28/10'),
                                new OA\Property(property: 'monto', type: 'number'),
                            ]
                        )),
                        new OA\Property(property: 'top_productos', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'top_clientes', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'metodos_pago', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'insumos_bajo_minimo', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'productos_sin_stock', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'valor_inventario', type: 'number'),
                        new OA\Property(property: 'productos_sin_rotacion', type: 'array', items: new OA\Items(type: 'object')),
                        new OA\Property(property: 'ordenes_pendientes', type: 'integer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Fecha inválida'),
        ]
    )]
    public function indicadores(Request $request)
    {
        $data = $request->validate([
            'desde' => 'nullable|date',
            'hasta' => 'nullable|date',
        ]);

        [$desde, $hasta] = $this->reportes->rangoIndicadores($data['desde'] ?? null, $data['hasta'] ?? null);
        $reporte = $this->reportes->indicadores($desde, $hasta);

        foreach (['desde', 'hasta', 'ant_desde', 'ant_hasta'] as $clave) {
            $reporte[$clave] = $reporte[$clave]->toDateString();
        }

        return response()->json($reporte);
    }

    /**
     * @return array{0: int, 1: int}
     */
    private function mesAnio(Request $request): array
    {
        $data = $request->validate([
            'mes' => 'nullable|integer|between:1,12',
            'anio' => 'nullable|integer|between:2000,2100',
        ]);

        return [
            (int) ($data['mes'] ?? now()->month),
            (int) ($data['anio'] ?? now()->year),
        ];
    }
}
