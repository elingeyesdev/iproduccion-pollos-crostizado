<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UnidadMedidaRequest;
use App\Models\UnidadMedida;
use OpenApi\Attributes as OA;

class UnidadMedidaApiController extends Controller
{
    #[OA\Get(
        path: '/api/unidad-medidas',
        summary: 'Listar unidades de medida activas',
        security: [['sanctum' => []]],
        tags: ['Unidades de medida'],
        responses: [
            new OA\Response(response: 200, description: 'Unidades de medida obtenidas correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index()
    {
        return response()->json(UnidadMedida::where('estado', 1)->get());
    }

    #[OA\Post(
        path: '/api/unidad-medidas',
        summary: 'Registrar una unidad de medida',
        security: [['sanctum' => []]],
        tags: ['Unidades de medida'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'abreviatura', 'factor_conversion'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Kilogramo'),
                    new OA\Property(property: 'abreviatura', type: 'string', maxLength: 50, example: 'kg'),
                    new OA\Property(property: 'factor_conversion', type: 'number', format: 'float', minimum: 0, example: 1000),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Unidad de medida creada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(UnidadMedidaRequest $request)
    {
        $unidadMedida = UnidadMedida::create($request->validated());

        return response()->json($unidadMedida, 201);
    }

    #[OA\Get(
        path: '/api/unidad-medidas/{unidad_medida}',
        summary: 'Obtener una unidad de medida',
        security: [['sanctum' => []]],
        tags: ['Unidades de medida'],
        parameters: [
            new OA\Parameter(name: 'unidad_medida', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Unidad de medida obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $unidadMedida = UnidadMedida::where('estado', 1)->findOrFail($id);

        return response()->json($unidadMedida);
    }

    #[OA\Put(
        path: '/api/unidad-medidas/{unidad_medida}',
        summary: 'Actualizar una unidad de medida',
        security: [['sanctum' => []]],
        tags: ['Unidades de medida'],
        parameters: [
            new OA\Parameter(name: 'unidad_medida', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'abreviatura', 'factor_conversion'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Kilogramo'),
                    new OA\Property(property: 'abreviatura', type: 'string', maxLength: 50, example: 'kg'),
                    new OA\Property(property: 'factor_conversion', type: 'number', format: 'float', minimum: 0, example: 1000),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Unidad de medida actualizada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(UnidadMedidaRequest $request, string $id)
    {
        $unidadMedida = UnidadMedida::where('estado', 1)->findOrFail($id);
        $unidadMedida->update($request->validated());

        return response()->json($unidadMedida);
    }

    #[OA\Delete(
        path: '/api/unidad-medidas/{unidad_medida}',
        summary: 'Eliminar (desactivar) una unidad de medida',
        security: [['sanctum' => []]],
        tags: ['Unidades de medida'],
        parameters: [
            new OA\Parameter(name: 'unidad_medida', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Unidad de medida eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id)
    {
        $unidadMedida = UnidadMedida::where('estado', 1)->findOrFail($id);
        $unidadMedida->update(['estado' => 0]);

        return response()->json([
            'message' => 'Unidad de medida eliminada',
        ]);
    }
}
