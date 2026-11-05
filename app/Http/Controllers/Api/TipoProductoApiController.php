<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoProductoRequest;
use App\Models\TipoProducto;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class TipoProductoApiController extends Controller
{
    #[OA\Get(
        path: '/api/tipo-productos',
        summary: 'Listar tipos de producto activos',
        security: [['sanctum' => []]],
        tags: ['Tipos de producto'],
        responses: [
            new OA\Response(response: 200, description: 'Tipos de producto obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        $tipoProductos = TipoProducto::query()
            ->where('estado', 1)
            ->get();

        return response()->json($tipoProductos);
    }

    #[OA\Post(
        path: '/api/tipo-productos',
        summary: 'Registrar un tipo de producto',
        security: [['sanctum' => []]],
        tags: ['Tipos de producto'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Combos'),
                    new OA\Property(property: 'descripcion', type: 'string', maxLength: 255, nullable: true, example: 'Combos de pollo con acompañamientos'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Tipo de producto creado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(TipoProductoRequest $request): JsonResponse
    {
        $tipoProducto = TipoProducto::create($request->validated());

        return response()->json($tipoProducto, 201);
    }

    #[OA\Get(
        path: '/api/tipo-productos/{tipo_producto}',
        summary: 'Obtener un tipo de producto',
        security: [['sanctum' => []]],
        tags: ['Tipos de producto'],
        parameters: [
            new OA\Parameter(name: 'tipo_producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Tipo de producto obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $tipoProducto = TipoProducto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($tipoProducto);
    }

    #[OA\Put(
        path: '/api/tipo-productos/{tipo_producto}',
        summary: 'Actualizar un tipo de producto',
        security: [['sanctum' => []]],
        tags: ['Tipos de producto'],
        parameters: [
            new OA\Parameter(name: 'tipo_producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Bebidas'),
                    new OA\Property(property: 'descripcion', type: 'string', maxLength: 255, nullable: true, example: 'Gaseosas y jugos'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Tipo de producto actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(TipoProductoRequest $request, string $id): JsonResponse
    {
        $tipoProducto = TipoProducto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $tipoProducto->update($request->validated());

        return response()->json($tipoProducto);
    }

    #[OA\Delete(
        path: '/api/tipo-productos/{tipo_producto}',
        summary: 'Eliminar (desactivar) un tipo de producto',
        security: [['sanctum' => []]],
        tags: ['Tipos de producto'],
        parameters: [
            new OA\Parameter(name: 'tipo_producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Tipo de producto eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $tipoProducto = TipoProducto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $tipoProducto->update(['estado' => 0]);

        return response()->json([
            'message' => 'Tipo de producto eliminado',
        ]);
    }
}
