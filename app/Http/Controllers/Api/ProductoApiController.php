<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductoApiController extends Controller
{
    #[OA\Get(
        path: '/api/productos',
        summary: 'Listar productos activos',
        security: [['sanctum' => []]],
        tags: ['Productos'],
        responses: [
            new OA\Response(response: 200, description: 'Productos obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        $productos = Producto::query()
            ->with('tipoProducto')
            ->where('estado', 1)
            ->get();

        return response()->json($productos);
    }

    #[OA\Post(
        path: '/api/productos',
        summary: 'Registrar un producto',
        security: [['sanctum' => []]],
        tags: ['Productos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'precio', 'tipo_producto_id', 'cantidad_actual'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Pollo broaster 1/4'),
                    new OA\Property(property: 'precio', type: 'number', format: 'float', minimum: 0, example: 25.5),
                    new OA\Property(property: 'tipo_producto_id', type: 'integer', example: 1),
                    new OA\Property(property: 'cantidad_actual', type: 'integer', minimum: 0, example: 20),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Producto creado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(ProductoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['estado'] = $data['estado'] ?? 1;

        $producto = Producto::create($data);

        return response()->json($producto, 201);
    }

    #[OA\Get(
        path: '/api/productos/{producto}',
        summary: 'Obtener un producto',
        security: [['sanctum' => []]],
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producto obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $producto = Producto::query()
            ->with('tipoProducto')
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($producto);
    }

    #[OA\Put(
        path: '/api/productos/{producto}',
        summary: 'Actualizar un producto',
        security: [['sanctum' => []]],
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'precio', 'tipo_producto_id', 'cantidad_actual'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Pollo broaster 1/4'),
                    new OA\Property(property: 'precio', type: 'number', format: 'float', minimum: 0, example: 27),
                    new OA\Property(property: 'tipo_producto_id', type: 'integer', example: 1),
                    new OA\Property(property: 'cantidad_actual', type: 'integer', minimum: 0, example: 15),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Producto actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(ProductoRequest $request, string $id): JsonResponse
    {
        $producto = Producto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $data = $request->validated();
        $data['estado'] = $data['estado'] ?? $producto->estado;

        $producto->update($data);

        return response()->json($producto);
    }

    #[OA\Delete(
        path: '/api/productos/{producto}',
        summary: 'Eliminar (desactivar) un producto',
        security: [['sanctum' => []]],
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producto eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $producto = Producto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $producto->update(['estado' => 0]);

        return response()->json([
            'message' => 'Producto eliminado',
        ]);
    }
}
