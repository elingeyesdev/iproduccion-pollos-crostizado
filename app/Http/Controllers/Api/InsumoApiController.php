<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\InsumoRequest;
use App\Models\Insumo;

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

    public function store(InsumoRequest $request)
    {
        $insumo = Insumo::create($request->validated());

        return response()->json($insumo, 201);
    }

    public function show(string $id)
    {
        $insumo = Insumo::with(['unidadMedida:id,nombre,abreviatura'])
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($insumo);
    }

    public function update(InsumoRequest $request, string $id)
    {
        $insumo = Insumo::where('estado', 1)->findOrFail($id);
        $insumo->update($request->validated());

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
