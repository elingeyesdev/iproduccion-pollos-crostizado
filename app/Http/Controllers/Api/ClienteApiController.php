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
        summary: 'Listar clientes',
        tags: ['Clientes'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Clientes obtenidos correctamente'
            )
        ]
    )]
public function index(): JsonResponse
    {
        $clientes = Cliente::query()
            ->where('estado', 1)
            ->get();
        
        return response()->json($clientes);
    }

    public function store(ClienteRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['estado'] = $data['estado'] ?? 1;
        
        $cliente = Cliente::create($data);

        return response()->json($cliente, 201);
    }

    public function show(string $id): JsonResponse
    {
        $cliente = Cliente::query()
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($cliente);
    }
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
