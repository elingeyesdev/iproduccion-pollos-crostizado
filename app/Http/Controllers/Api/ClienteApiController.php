<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteApiController extends Controller
{
    public function index()
    {
        return response()->json(Cliente::all());
    }

    public function store(Request $request)
    {
        $cliente = Cliente::create($request->all());
        return response()->json($cliente, 201);
    }

    public function show(string $id)
    {
        return response()->json(Cliente::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->update($request->all());
        return response()->json($cliente);
    }

    public function destroy(string $id)
    {
        Cliente::destroy($id);
        return response()->json([
            'message' => 'Cliente eliminado',
        ]);
    }
}