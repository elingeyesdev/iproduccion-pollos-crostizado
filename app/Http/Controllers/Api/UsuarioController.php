<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        return response()->json([
            User::where('estado', 1)->get()
        ]);
    }

    public function store(Request $request)
    {
        $usuario = User::create($request->all());

        return response()->json($usuario, 201);
    }

    public function show(string $id)
    {
        $usuario = User::where('estado', 1)->findOrFail($id);

        return response()->json($usuario);
      
    }

    public function update(Request $request, string $id)
    {
        $usuario = User::where('estado', 1)->findOrFail($id);
        $usuario->update($request->all());

        return response()->json($usuario);
    }

    public function destroy(string $id)
    {
        $usuario = User::where('estado', 1)->findOrFail($id);
        $usuario->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
