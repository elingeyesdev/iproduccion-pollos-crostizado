<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MermaRequest;
use App\Models\Merma;
use OpenApi\Attributes as OA;

class MermaController extends Controller
{
    #[OA\Get(
        path: '/api/mermas',
        summary: 'Listar mermas',
        description: 'Devuelve las mermas activas con su producción asociada.',
        tags: ['Mermas'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Mermas obtenidas correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index()
    {
        return response()->json(
            Merma::with('produccion')->where('estado', 1)->get()
        );
    }

    #[OA\Post(
        path: '/api/mermas',
        summary: 'Registrar merma',
        tags: ['Mermas'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['produccion_id', 'cantidad'],
                properties: [
                    new OA\Property(property: 'produccion_id', type: 'integer', example: 1),
                    new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0, example: 1.5),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Merma registrada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(MermaRequest $request)
    {
        $merma = Merma::create($request->validated() + ['estado' => 1]);

        return response()->json($merma, 201);
    }

    #[OA\Get(
        path: '/api/mermas/{merma}',
        summary: 'Ver merma',
        tags: ['Mermas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'merma', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Merma obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $merma = Merma::with('produccion')->where('estado', 1)->findOrFail($id);

        return response()->json($merma);
    }

    #[OA\Put(
        path: '/api/mermas/{merma}',
        summary: 'Actualizar merma',
        tags: ['Mermas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'merma', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['produccion_id', 'cantidad'],
                properties: [
                    new OA\Property(property: 'produccion_id', type: 'integer', example: 1),
                    new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0, example: 2),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Merma actualizada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(MermaRequest $request, string $id)
    {
        $merma = Merma::where('estado', 1)->findOrFail($id);
        $merma->update($request->validated());

        return response()->json($merma);
    }

    #[OA\Delete(
        path: '/api/mermas/{merma}',
        summary: 'Eliminar merma',
        description: 'Baja lógica: marca la merma con estado = 0. Responde sin contenido.',
        tags: ['Mermas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'merma', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Merma eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id)
    {
        $merma = Merma::where('estado', 1)->findOrFail($id);
        $merma->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
