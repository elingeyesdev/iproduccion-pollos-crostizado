<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;
use Spatie\Permission\Exceptions\RoleAlreadyExists;

class RoleController extends Controller
{
    #[OA\Get(
        path: '/api/roles',
        summary: 'Listar roles activos con sus permisos',
        security: [['sanctum' => []]],
        tags: ['Roles'],
        responses: [
            new OA\Response(response: 200, description: 'Roles obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(): JsonResponse
    {
        $roles = Role::where('estado', 1)
            ->with('permissions')
            ->get();

        return response()->json($roles);
    }

    #[OA\Post(
        path: '/api/roles',
        summary: 'Registrar un rol con permisos',
        security: [['sanctum' => []]],
        tags: ['Roles'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'guard_name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Supervisor de producción'),
                    new OA\Property(property: 'guard_name', type: 'string', enum: ['web', 'api'], example: 'web'),
                    new OA\Property(property: 'permissions', type: 'array', nullable: true, items: new OA\Items(type: 'string'), example: ['ver produccion', 'crear produccion']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Rol creado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación o rol ya existente para el guard indicado'),
        ]
    )]
    public function store(RoleRequest $request): JsonResponse
    {
        $data = $request->validated();
        $permissions = $data['permissions'] ?? [];
        unset($data['permissions']);

        try {
            $role = Role::create($data);
        } catch (RoleAlreadyExists $e) {
            return response()->json([
                'message' => 'Ya existe un rol con ese nombre para el guard indicado.',
            ], 422);
        }

        if (!empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return response()->json($role->fresh(['permissions']), 201);
    }

    #[OA\Get(
        path: '/api/roles/{role}',
        summary: 'Obtener un rol con sus permisos',
        security: [['sanctum' => []]],
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rol obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $role = Role::where('estado', 1)
            ->with('permissions')
            ->findOrFail($id);

        return response()->json($role);
    }

    #[OA\Put(
        path: '/api/roles/{role}',
        summary: 'Actualizar un rol y sus permisos',
        security: [['sanctum' => []]],
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'guard_name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', maxLength: 100, example: 'Supervisor de producción'),
                    new OA\Property(property: 'guard_name', type: 'string', enum: ['web', 'api'], example: 'web'),
                    new OA\Property(property: 'permissions', type: 'array', nullable: true, items: new OA\Items(type: 'string'), example: ['ver produccion', 'crear produccion']),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Rol actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(RoleRequest $request, string $id): JsonResponse
    {
        $role = Role::where('estado', 1)->findOrFail($id);

        $data = $request->validated();
        $permissions = $data['permissions'] ?? null;
        unset($data['permissions']);

        $role->update($data);

        if ($permissions !== null) {
            $role->syncPermissions($permissions);
        }

        return response()->json($role->fresh(['permissions']));
    }

    #[OA\Delete(
        path: '/api/roles/{role}',
        summary: 'Eliminar (desactivar) un rol',
        security: [['sanctum' => []]],
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'role', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Rol eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $role = Role::where('estado', 1)->findOrFail($id);
        $role->update(['estado' => 0]);

        return response()->json([
            'message' => 'Rol eliminado',
        ]);
    }
}
