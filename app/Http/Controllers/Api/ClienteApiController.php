<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;


class ClienteApiController extends Controller
{
    #[OA\Get(
        path: '/api/clientes',
        summary: 'Listar clientes activos',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        responses: [
            new OA\Response(response: 200, description: 'Clientes obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        $clientes = Cliente::query()
            ->where('estado', 1)
            ->get();
        
        return response()->json($clientes);
    }

    #[OA\Post(
        path: '/api/clientes',
        summary: 'Registrar un cliente',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'telefono'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Juan Pérez'),
                    new OA\Property(property: 'telefono', type: 'string', maxLength: 255, example: '76543210'),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Cliente creado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(ClienteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['estado'] = $data['estado'] ?? 1;
        
        $cliente = Cliente::create($data);

        return response()->json($cliente, 201);
    }

    #[OA\Get(
        path: '/api/clientes/{cliente}',
        summary: 'Obtener un cliente',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(name: 'cliente', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cliente obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $cliente = Cliente::query()
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($cliente);
    }
    #[OA\Put(
        path: '/api/clientes/{cliente}',
        summary: 'Actualizar un cliente',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(name: 'cliente', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nombre', 'telefono'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, example: 'Juan Pérez'),
                    new OA\Property(property: 'telefono', type: 'string', maxLength: 255, example: '76543210'),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cliente actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(ClienteRequest $request, string $id): JsonResponse
    {
        $cliente = Cliente::query()
            ->where('estado', 1)
            ->findOrFail($id);
        
        $data = $request->validated();
        $data['estado'] = $data['estado'] ?? $cliente->estado;
        
        $cliente->update($data);

        return response()->json($cliente);
    }

    #[OA\Delete(
        path: '/api/clientes/{cliente}',
        summary: 'Eliminar (desactivar) un cliente',
        security: [['sanctum' => []]],
        tags: ['Clientes'],
        parameters: [
            new OA\Parameter(name: 'cliente', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Cliente eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $cliente = Cliente::query()
            ->where('estado', 1)
            ->findOrFail($id);
        
        $cliente->update(['estado' => 0]);

        return response()->json([
            'message' => 'Cliente eliminado',
        ]);
    }
}
