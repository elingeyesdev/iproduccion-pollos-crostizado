<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaApiController extends Controller
{
    public function index()
    {
        return response()->json(UnidadMedida::where('estado', 1)->get());
    }

    public function store(Request $request)
    {
        $unidadMedida = UnidadMedida::create($request->all());

        return response()->json($unidadMedida, 201);
    }

    public function show(string $id)
    {
        $unidadMedida = UnidadMedida::where('estado', 1)->findOrFail($id);

        return response()->json($unidadMedida);
    }

    public function update(Request $request, string $id)
    {
        $unidadMedida = UnidadMedida::where('estado', 1)->findOrFail($id);
        $unidadMedida->update($request->all());

        return response()->json($unidadMedida);
    }

    public function destroy(string $id)
    {
        $unidadMedida = UnidadMedida::where('estado', 1)->findOrFail($id);
        $unidadMedida->update(['estado' => 0]);

        return response()->json([
            'message' => 'Unidad de medida eliminada',
        ]);
    }
}
