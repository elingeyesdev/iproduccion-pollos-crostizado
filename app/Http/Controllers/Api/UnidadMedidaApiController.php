<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaApiController extends Controller
{
    public function index()
    {
        return response()->json(UnidadMedida::all());
    }

    public function store(Request $request)
    {
        $unidadMedida = UnidadMedida::createFromData($request->all());

        return response()->json($unidadMedida, 201);
    }

    public function show(string $id)
    {
        return response()->json(UnidadMedida::findOrFail($id));
    }

    public function update(Request $request, string $id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);
        $unidadMedida->updateFromData($request->all());

        return response()->json($unidadMedida);
    }

    public function destroy(string $id)
    {
        $unidadMedida = UnidadMedida::findOrFail($id);
        $unidadMedida->deleteRecord();

        return response()->json([
            'message' => 'Unidad de medida eliminada',
        ]);
    }
}
