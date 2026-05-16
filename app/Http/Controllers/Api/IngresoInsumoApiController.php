<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IngresoInsumo;
use Illuminate\Http\Request;

class IngresoInsumoApiController extends Controller
{
    public function index()
    {
        return response()->json(IngresoInsumo::with('detalles.insumo')->get());
    }

    public function store(Request $request)
    {
        $ingresoInsumo = IngresoInsumo::createWithDetalles($request->all());
        $ingresoInsumo->load('detalles.insumo');

        return response()->json($ingresoInsumo, 201);
    }

    public function show(string $id)
    {
        $ingresoInsumo = IngresoInsumo::with('detalles.insumo')->findOrFail($id);

        return response()->json($ingresoInsumo);
    }

    public function update(Request $request, string $id)
    {
        $ingresoInsumo = IngresoInsumo::findOrFail($id);
        $ingresoInsumo->updateWithDetalles($request->all());
        $ingresoInsumo->load('detalles.insumo');

        return response()->json($ingresoInsumo);
    }

    public function destroy(string $id)
    {
        $ingresoInsumo = IngresoInsumo::findOrFail($id);
        $ingresoInsumo->deleteWithStockRollback();

        return response()->json([
            'message' => 'Ingreso de insumos eliminado',
        ]);
    }
}
