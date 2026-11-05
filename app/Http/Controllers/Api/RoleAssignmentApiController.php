<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRoleAssignmentRequest;
use App\Models\UserRoleAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenApi\Attributes as OA;

class RoleAssignmentApiController extends Controller
{
    #[OA\Get(
        path: '/api/asignacion-roles',
        summary: 'Listar asignaciones de roles (paginado, 15 por página)',
        security: [['sanctum' => []]],
        tags: ['Asignación de roles'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Busca por nombre o email del usuario, o nombre del rol', schema: new OA\Schema(type: 'string'), example: 'admin'),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer'), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Asignaciones obtenidas correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search'));

        $assignments = UserRoleAssignment::searchAndPaginate($search, 15);

        return response()->json($assignments);
    }

    #[OA\Post(
        path: '/api/asignacion-roles',
        summary: 'Asignar un rol a un usuario',
        security: [['sanctum' => []]],
        tags: ['Asignación de roles'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_id', 'role_id'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'integer', example: 2),
                    new OA\Property(property: 'role_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Rol asignado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(UserRoleAssignmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $assignment = UserRoleAssignment::assignRoleWithAudit(
            (int) $data['user_id'],
            (int) $data['role_id'],
            Auth::id()
        );

        return response()->json(
            $assignment->load(['user', 'role', 'assignedBy']),
            201
        );
    }

    #[OA\Get(
        path: '/api/asignacion-roles/{assignment}',
        summary: 'Obtener una asignación de rol',
        security: [['sanctum' => []]],
        tags: ['Asignación de roles'],
        parameters: [
            new OA\Parameter(name: 'assignment', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Asignación obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(UserRoleAssignment $assignment): JsonResponse
    {
        $assignment->load(['user', 'role', 'assignedBy']);

        return response()->json($assignment);
    }

    #[OA\Put(
        path: '/api/asignacion-roles/{assignment}',
        summary: 'Cambiar el rol de una asignación',
        security: [['sanctum' => []]],
        tags: ['Asignación de roles'],
        parameters: [
            new OA\Parameter(name: 'assignment', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_id', 'role_id'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'integer', example: 2),
                    new OA\Property(property: 'role_id', type: 'integer', example: 3),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Asignación actualizada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(UserRoleAssignmentRequest $request, UserRoleAssignment $assignment): JsonResponse
    {
        $data = $request->validated();

        $assignment = $assignment->changeRoleWithAudit(
            (int) $data['user_id'],
            (int) $data['role_id'],
            Auth::id()
        );

        return response()->json($assignment->load(['user', 'role', 'assignedBy']));
    }

    #[OA\Delete(
        path: '/api/asignacion-roles/{assignment}',
        summary: 'Revocar una asignación de rol',
        security: [['sanctum' => []]],
        tags: ['Asignación de roles'],
        parameters: [
            new OA\Parameter(name: 'assignment', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Asignación eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(UserRoleAssignment $assignment): JsonResponse
    {
        $assignment->revokeRole();

        return response()->json([
            'message' => 'Asignación eliminada',
        ]);
    }
}
