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
            Insumo::with(['unidadMedida:id,nombre,abreviatura'])
                ->where('estado', 1)
                ->get()
        );
    }

    public function store(Request $request)
    {
        $insumo = Insumo::create($request->all());

        return response()->json($insumo, 201);
    }

    public function show(string $id)
    {
        $insumo = Insumo::with(['unidadMedida:id,nombre,abreviatura'])
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($insumo);
    }

    public function update(Request $request, string $id)
    {
        $insumo = Insumo::where('estado', 1)->findOrFail($id);
        $insumo->update($request->all());

        return response()->json($insumo);
    }

    public function destroy(string $id)
    {
        $insumo = Insumo::where('estado', 1)->findOrFail($id);
        $insumo->update(['estado' => 0]);

        return response()->json([
            'message' => 'Insumo eliminado',
        ]);
    }
}
