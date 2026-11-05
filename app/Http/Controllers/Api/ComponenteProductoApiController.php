<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoComposicionRequest;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class ComponenteProductoApiController extends Controller
{
    #[OA\Get(
        path: '/api/componentes-producto',
        summary: 'Listar productos activos con su composición',
        security: [['sanctum' => []]],
        tags: ['Composición de productos'],
        responses: [
            new OA\Response(response: 200, description: 'Productos con ingredientes obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        $productos = Producto::query()
            ->with(['ingredientes', 'tipoProducto'])
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return response()->json($productos);
    }

    #[OA\Get(
        path: '/api/componentes-producto/{producto}',
        summary: 'Obtener la composición de un producto',
        security: [['sanctum' => []]],
        tags: ['Composición de productos'],
        parameters: [
            new OA\Parameter(name: 'producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Composición obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(Producto $producto): JsonResponse
    {
        $producto->load(['ingredientes', 'tipoProducto']);

        return response()->json($producto);
    }

    #[OA\Post(
        path: '/api/componentes-producto',
        summary: 'Registrar la composición de un producto',
        description: 'Reemplaza los ingredientes activos del producto indicado. Un producto no puede incluirse a sí mismo como ingrediente.',
        security: [['sanctum' => []]],
        tags: ['Composición de productos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['producto_id'],
                properties: [
                    new OA\Property(property: 'producto_id', type: 'integer', example: 5),
                    new OA\Property(
                        property: 'ingredientes',
                        type: 'array',
                        nullable: true,
                        items: new OA\Items(
                            required: ['id', 'cantidad'],
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', description: 'ID del producto ingrediente', example: 2),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.01, example: 1.5),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Composición registrada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(ProductoComposicionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $ingredientes = (array) ($validated['ingredientes'] ?? []);
        $productoId = $validated['producto_id'];

        DB::transaction(function () use ($productoId, $ingredientes): void {
            DB::table('composicion_productos')
                ->where('producto_padre_id', $productoId)
                ->update(['estado' => 0]);

            foreach ($ingredientes as $ingrediente) {
                DB::table('composicion_productos')->updateOrInsert(
                    [
                        'producto_padre_id' => $productoId,
                        'producto_hijo_id' => $ingrediente['id'],
                    ],
                    [
                        'cantidad' => $ingrediente['cantidad'],
                        'estado' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        $producto = Producto::with(['ingredientes', 'tipoProducto'])->findOrFail($productoId);

        return response()->json($producto, 201);
    }

    #[OA\Put(
        path: '/api/componentes-producto/{producto}',
        summary: 'Actualizar la composición de un producto',
        description: 'Reemplaza los ingredientes activos del producto. Un producto no puede incluirse a sí mismo como ingrediente.',
        security: [['sanctum' => []]],
        tags: ['Composición de productos'],
        parameters: [
            new OA\Parameter(name: 'producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        property: 'ingredientes',
                        type: 'array',
                        nullable: true,
                        items: new OA\Items(
                            required: ['id', 'cantidad'],
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', description: 'ID del producto ingrediente', example: 2),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.01, example: 2),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Composición actualizada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(ProductoComposicionRequest $request, Producto $producto): JsonResponse
    {
        $validated = $request->validated();
        $ingredientes = (array) ($validated['ingredientes'] ?? []);

        DB::transaction(function () use ($producto, $ingredientes): void {
            DB::table('composicion_productos')
                ->where('producto_padre_id', $producto->id)
                ->update(['estado' => 0]);

            foreach ($ingredientes as $ingrediente) {
                DB::table('composicion_productos')->updateOrInsert(
                    [
                        'producto_padre_id' => $producto->id,
                        'producto_hijo_id' => $ingrediente['id'],
                    ],
                    [
                        'cantidad' => $ingrediente['cantidad'],
                        'estado' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        $producto->load(['ingredientes', 'tipoProducto']);

        return response()->json($producto);
    }

    #[OA\Delete(
        path: '/api/componentes-producto/{producto}',
        summary: 'Eliminar (desactivar) la composición de un producto',
        security: [['sanctum' => []]],
        tags: ['Composición de productos'],
        parameters: [
            new OA\Parameter(name: 'producto', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Composición eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(Producto $producto): JsonResponse
    {
        DB::table('composicion_productos')
            ->where('producto_padre_id', $producto->id)
            ->update(['estado' => 0, 'updated_at' => now()]);

        return response()->json([
            'message' => 'Composición eliminada',
        ]);
    }
}
