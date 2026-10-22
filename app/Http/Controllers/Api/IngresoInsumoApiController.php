<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IngresoInsumo;
use App\Models\IngresoInsumoDetalle;
use App\Models\Insumo;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class IngresoInsumoApiController extends Controller
{
    public function __construct(private StockService $stockService)
    {
    }

    #[OA\Get(
        path: '/api/ingreso-insumos',
        summary: 'Listar ingresos de insumos activos con sus detalles',
        security: [['sanctum' => []]],
        tags: ['Ingresos de insumos'],
        responses: [
            new OA\Response(response: 200, description: 'Ingresos de insumos obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index()
    {
        return response()->json(
            IngresoInsumo::with(['detalles' => function ($q) {
                $q->where('estado', 1)->with('insumo');
            }])->where('estado', 1)->get()
        );
    }

    #[OA\Post(
        path: '/api/ingreso-insumos',
        summary: 'Registrar un ingreso de insumos',
        description: 'Crea el ingreso con sus detalles e incrementa el stock_actual de cada insumo.',
        security: [['sanctum' => []]],
        tags: ['Ingresos de insumos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_ingreso', 'detalles'],
                properties: [
                    new OA\Property(property: 'fecha_ingreso', type: 'string', format: 'date', example: '2026-10-03'),
                    new OA\Property(property: 'descripcion', type: 'string', nullable: true, example: 'Compra semanal al proveedor'),
                    new OA\Property(
                        property: 'detalles',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['insumo_id', 'cantidad', 'precio_unitario'],
                            properties: [
                                new OA\Property(property: 'insumo_id', type: 'integer', example: 1),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.01, example: 25),
                                new OA\Property(property: 'precio_unitario', type: 'number', format: 'float', minimum: 0, example: 18.5),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ingreso de insumos creado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(Request $request)
    {
        $request->validate([
            'fecha_ingreso' => 'required|date',
            'descripcion' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.insumo_id' => 'required|exists:insumos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $ingresoInsumo = DB::transaction(function () use ($request) {
            $ingreso = IngresoInsumo::create([
                'fecha_ingreso' => $request->fecha_ingreso,
                'descripcion' => $request->descripcion,
            ]);

            foreach ($request->detalles as $detalle) {
                IngresoInsumoDetalle::create([
                    'ingreso_insumo_id' => $ingreso->id,
                    'insumo_id' => $detalle['insumo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                ]);

                $insumo = Insumo::findOrFail($detalle['insumo_id']);
                $insumo->update([
                    'stock_actual' => $insumo->stock_actual + $detalle['cantidad'],
                ]);
            }

            return $ingreso;
        });

        $ingresoInsumo->load(['detalles' => function ($q) {
            $q->where('estado', 1)->with('insumo');
        }]);

        return response()->json($ingresoInsumo, 201);
    }

    #[OA\Get(
        path: '/api/ingreso-insumos/{ingreso_insumo}',
        summary: 'Obtener un ingreso de insumos con sus detalles',
        security: [['sanctum' => []]],
        tags: ['Ingresos de insumos'],
        parameters: [
            new OA\Parameter(name: 'ingreso_insumo', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ingreso de insumos obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $ingresoInsumo = IngresoInsumo::with(['detalles' => function ($q) {
            $q->where('estado', 1)->with('insumo');
        }])->where('estado', 1)->findOrFail($id);

        return response()->json($ingresoInsumo);
    }

    #[OA\Put(
        path: '/api/ingreso-insumos/{ingreso_insumo}',
        summary: 'Actualizar un ingreso de insumos',
        description: 'Reemplaza los detalles del ingreso y aplica al stock solo la diferencia neta por insumo. Falla con 422 si el stock ya fue consumido en producción.',
        security: [['sanctum' => []]],
        tags: ['Ingresos de insumos'],
        parameters: [
            new OA\Parameter(name: 'ingreso_insumo', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_ingreso', 'detalles'],
                properties: [
                    new OA\Property(property: 'fecha_ingreso', type: 'string', format: 'date', example: '2026-10-03'),
                    new OA\Property(property: 'descripcion', type: 'string', nullable: true, example: 'Corrección de cantidades'),
                    new OA\Property(
                        property: 'detalles',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['insumo_id', 'cantidad', 'precio_unitario'],
                            properties: [
                                new OA\Property(property: 'insumo_id', type: 'integer', example: 1),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.01, example: 20),
                                new OA\Property(property: 'precio_unitario', type: 'number', format: 'float', minimum: 0, example: 18.5),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ingreso de insumos actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación o stock insuficiente para revertir el ingreso'),
        ]
    )]
    public function update(Request $request, string $id)
    {
        $request->validate([
            'fecha_ingreso' => 'required|date',
            'descripcion' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.insumo_id' => 'required|exists:insumos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        try {
            $ingresoInsumo = DB::transaction(function () use ($request, $id) {
                $ingreso = IngresoInsumo::where('estado', 1)->lockForUpdate()->findOrFail($id);
                $anteriores = $ingreso->detalles()->where('estado', 1)->get();

                // Solo se aplica la diferencia neta por insumo; falla si ya se consumió en producción
                $this->stockService->aplicarDiferenciaInsumos($anteriores, $request->detalles);

                $ingreso->update([
                    'fecha_ingreso' => $request->fecha_ingreso ?? $ingreso->fecha_ingreso,
                    'descripcion' => $request->descripcion ?? $ingreso->descripcion,
                ]);

                foreach ($anteriores as $detalle) {
                    $detalle->update(['estado' => 0]);
                }

                foreach ($request->detalles as $detalle) {
                    IngresoInsumoDetalle::create([
                        'ingreso_insumo_id' => $ingreso->id,
                        'insumo_id' => $detalle['insumo_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                    ]);
                }

                return $ingreso;
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $ingresoInsumo->load(['detalles' => function ($q) {
            $q->where('estado', 1)->with('insumo');
        }]);

        return response()->json($ingresoInsumo);
    }

    #[OA\Delete(
        path: '/api/ingreso-insumos/{ingreso_insumo}',
        summary: 'Eliminar (desactivar) un ingreso de insumos',
        description: 'Revierte el stock de los insumos ingresados. Falla con 422 si el stock ya fue consumido en producción.',
        security: [['sanctum' => []]],
        tags: ['Ingresos de insumos'],
        parameters: [
            new OA\Parameter(name: 'ingreso_insumo', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ingreso de insumos eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Stock insuficiente para revertir el ingreso'),
        ]
    )]
    public function destroy(string $id)
    {
        try {
            DB::transaction(function () use ($id) {
                $ingreso = IngresoInsumo::where('estado', 1)->lockForUpdate()->findOrFail($id);
                $detalles = $ingreso->detalles()->where('estado', 1)->get();

                $this->stockService->aplicarDiferenciaInsumos($detalles, []);

                foreach ($detalles as $detalle) {
                    $detalle->update(['estado' => 0]);
                }

                $ingreso->update(['estado' => 0]);
            });
        } catch (\RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Ingreso de insumos eliminado',
        ]);
    }
}
