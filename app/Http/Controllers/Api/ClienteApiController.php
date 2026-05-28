<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteApiController extends Controller
{
    public function index()
    {
        return response()->json(Cliente::where('estado', 1)->get());
    }

    public function store(Request $request)
    {
        $cliente = Cliente::create($request->all());

        return response()->json($cliente, 201);
    }

    public function show(string $id)
    {
        $cliente = Cliente::where('estado', 1)->findOrFail($id);

        return response()->json($cliente);
    }

    public function update(Request $request, string $id)
    {
        $cliente = Cliente::where('estado', 1)->findOrFail($id);
        $cliente->update($request->all());

        return response()->json($cliente);
    }

    public function destroy(string $id)
    {
        $cliente = Cliente::where('estado', 1)->findOrFail($id);
        $cliente->update(['estado' => 0]);

        return response()->json([
            'message' => 'Cliente eliminado',
        ]);
    }
}
