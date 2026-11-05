<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class UsuarioController extends Controller
{
    #[OA\Get(
        path: '/api/usuarios',
        summary: 'Listar usuarios activos con sus roles',
        security: [['sanctum' => []]],
        tags: ['Usuarios'],
        responses: [
            new OA\Response(response: 200, description: 'Usuarios obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        $usuarios = User::query()
            ->where('estado', 1)
            ->with('roles')
            ->get();

        return response()->json($usuarios);
    }

    #[OA\Post(
        path: '/api/usuarios',
        summary: 'Registrar un usuario con roles',
        security: [['sanctum' => []]],
        tags: ['Usuarios'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'roles', 'password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 120, example: 'María López'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 180, example: 'maria.lopez@krosty.com'),
                    new OA\Property(property: 'roles', type: 'array', minItems: 1, items: new OA\Items(type: 'string'), example: ['Administrador']),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, description: 'Mínimo 8 caracteres, mayúsculas, minúsculas y números', example: 'Krosty2026Seg'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', example: 'Krosty2026Seg'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Usuario creado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(UserRequest $request): JsonResponse
    {
        $usuario = User::createWithRoles($request->validated());

        return response()->json($usuario->fresh(['roles']), 201);
    }

    #[OA\Get(
        path: '/api/usuarios/{user}',
        summary: 'Obtener un usuario con sus roles',
        security: [['sanctum' => []]],
        tags: ['Usuarios'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Usuario obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $usuario = User::query()
            ->where('estado', 1)
            ->with('roles')
            ->findOrFail($id);

        return response()->json($usuario);
    }

    #[OA\Put(
        path: '/api/usuarios/{user}',
        summary: 'Actualizar un usuario y sus roles',
        security: [['sanctum' => []]],
        tags: ['Usuarios'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'email', 'roles'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 120, example: 'María López'),
                    new OA\Property(property: 'email', type: 'string', format: 'email', maxLength: 180, example: 'maria.lopez@krosty.com'),
                    new OA\Property(property: 'roles', type: 'array', minItems: 1, items: new OA\Items(type: 'string'), example: ['Administrador']),
                    new OA\Property(property: 'password', type: 'string', format: 'password', minLength: 8, nullable: true, description: 'Opcional. Mínimo 8 caracteres, mayúsculas, minúsculas y números', example: 'Krosty2026Seg'),
                    new OA\Property(property: 'password_confirmation', type: 'string', format: 'password', nullable: true, example: 'Krosty2026Seg'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Usuario actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(UserRequest $request, string $id): JsonResponse
    {
        $usuario = User::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $usuario->updateWithRoles($request->validated());

        return response()->json($usuario->fresh(['roles']));
    }

    #[OA\Delete(
        path: '/api/usuarios/{user}',
        summary: 'Eliminar (desactivar) un usuario',
        security: [['sanctum' => []]],
        tags: ['Usuarios'],
        parameters: [
            new OA\Parameter(name: 'user', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Usuario eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $usuario = User::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $usuario->update(['estado' => 0]);

        return response()->json([
            'message' => 'Usuario eliminado',
        ]);
    }
}
