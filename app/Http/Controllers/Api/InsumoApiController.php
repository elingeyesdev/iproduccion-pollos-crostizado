<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Insumo;
use Illuminate\Http\Request;

class InsumoApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Insumo::with(['categoriaInsumo:id,nombre', 'unidadMedida:id,nombre,abreviatura'])->get()
        );
    }

    public function store(Request $request)
    {
        $insumo = Insumo::createFromData($request->all());

        return response()->json($insumo, 201);
    }

    public function show(string $id)
    {
        $insumo = Insumo::with(['categoriaInsumo:id,nombre', 'unidadMedida:id,nombre,abreviatura'])
            ->findOrFail($id);

        return response()->json($insumo);
    }

    public function update(Request $request, string $id)
    {
        $insumo = Insumo::findOrFail($id);
        $insumo->updateFromData($request->all());

        return response()->json($insumo);
    }

    public function destroy(string $id)
    {
        $insumo = Insumo::findOrFail($id);
        $insumo->deleteRecord();

        return response()->json([
            'message' => 'Insumo eliminado',
        ]);
    }
}
