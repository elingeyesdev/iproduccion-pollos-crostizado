<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaApiController extends Controller
{
    public function index()
    {
        return response()->json(Venta::with('cliente')->get());
    }

    public function store(Request $request)
    {
        $venta = Venta::createFromData($request->all());

        return response()->json($venta, 201);
    }

    public function show(string $id)
    {
        $venta = Venta::with('cliente')->findOrFail($id);

        return response()->json($venta);
    }

    public function update(Request $request, string $id)
    {
        $venta = Venta::findByIdOrFail($id);
        $venta->updateFromData($request->all());

        return response()->json($venta);
    }

    public function destroy(string $id)
    {
        Venta::deleteById($id);

        return response()->json([
            'message' => 'Venta eliminada',
        ]);
    }
}
