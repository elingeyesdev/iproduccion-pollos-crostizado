<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsumoRequest;
use App\Models\Insumo;
use OpenApi\Attributes as OA;

class InsumoApiController extends Controller
{
    #[OA\Get(
        path: '/api/insumos',
        summary: 'Listar insumos activos',
        security: [['sanctum' => []]],
        tags: ['Insumos'],
        responses: [
            new OA\Response(response: 200, description: 'Insumos obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index()
    {
        return response()->json(
            Insumo::with(['unidadMedida:id,nombre,abreviatura'])
                ->where('estado', 1)
                ->get()
        );
    }

    #[OA\Post(
        path: '/api/insumos',
        summary: 'Registrar un insumo',
        security: [['sanctum' => []]],
        tags: ['Insumos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'unidad_medida_id', 'stock_actual', 'stock_minimo'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 150, example: 'Pechuga de pollo'),
                    new OA\Property(property: 'unidad_medida_id', type: 'integer', example: 1),
                    new OA\Property(property: 'stock_actual', type: 'number', format: 'float', minimum: 0, example: 50.5),
                    new OA\Property(property: 'stock_minimo', type: 'number', format: 'float', minimum: 0, example: 10),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Insumo creado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(InsumoRequest $request)
    {
        $insumo = Insumo::create($request->validated());

        return response()->json($insumo, 201);
    }

    #[OA\Get(
        path: '/api/insumos/{insumo}',
        summary: 'Obtener un insumo',
        security: [['sanctum' => []]],
        tags: ['Insumos'],
        parameters: [
            new OA\Parameter(name: 'insumo', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Insumo obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $insumo = Insumo::with(['unidadMedida:id,nombre,abreviatura'])
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($insumo);
    }

    #[OA\Put(
        path: '/api/insumos/{insumo}',
        summary: 'Actualizar un insumo',
        security: [['sanctum' => []]],
        tags: ['Insumos'],
        parameters: [
            new OA\Parameter(name: 'insumo', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'unidad_medida_id', 'stock_actual', 'stock_minimo'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 150, example: 'Pechuga de pollo'),
                    new OA\Property(property: 'unidad_medida_id', type: 'integer', example: 1),
                    new OA\Property(property: 'stock_actual', type: 'number', format: 'float', minimum: 0, example: 45),
                    new OA\Property(property: 'stock_minimo', type: 'number', format: 'float', minimum: 0, example: 10),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Insumo actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(InsumoRequest $request, string $id)
    {
        $insumo = Insumo::where('estado', 1)->findOrFail($id);
        $insumo->update($request->validated());

        return response()->json($insumo);
    }

    #[OA\Delete(
        path: '/api/insumos/{insumo}',
        summary: 'Eliminar (desactivar) un insumo',
        security: [['sanctum' => []]],
        tags: ['Insumos'],
        parameters: [
            new OA\Parameter(name: 'insumo', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Insumo eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id)
    {
        $insumo = Insumo::where('estado', 1)->findOrFail($id);
        $insumo->update(['estado' => 0]);

        return response()->json([
            'message' => 'Insumo eliminado',
        ]);
    }
}
