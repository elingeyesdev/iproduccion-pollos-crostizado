<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Exceptions\RoleAlreadyExists;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::where('estado', 1)
            ->with('permissions')
            ->get();

        return response()->json($roles);
    }

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

    public function show(string $id): JsonResponse
    {
        $role = Role::where('estado', 1)
            ->with('permissions')
            ->findOrFail($id);

        return response()->json($role);
    }

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

    public function destroy(string $id): JsonResponse
    {
        $role = Role::where('estado', 1)->findOrFail($id);
        $role->update(['estado' => 0]);

        return response()->json([
            'message' => 'Rol eliminado',
        ]);
    }
}
