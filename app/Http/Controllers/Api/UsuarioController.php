<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UsuarioController extends Controller
{
    public function index(): JsonResponse
    {
        $usuarios = User::query()
            ->where('estado', 1)
            ->with('roles')
            ->get();

        return response()->json($usuarios);
    }

    public function store(UserRequest $request): JsonResponse
    {
        $usuario = User::createWithRoles($request->validated());

        return response()->json($usuario->fresh(['roles']), 201);
    }

    public function show(string $id): JsonResponse
    {
        $usuario = User::query()
            ->where('estado', 1)
            ->with('roles')
            ->findOrFail($id);

        return response()->json($usuario);
    }

    public function update(UserRequest $request, string $id): JsonResponse
    {
        $usuario = User::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $usuario->updateWithRoles($request->validated());

        return response()->json($usuario->fresh(['roles']));
    }

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
