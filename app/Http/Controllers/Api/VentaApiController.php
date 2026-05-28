<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use Illuminate\Http\Request;

class VentaApiController extends Controller
{
    public function index()
    {
        return response()->json(Venta::with('cliente')->where('estado', 1)->get());
    }

    public function store(Request $request)
    {
        $venta = Venta::create($request->all());

        return response()->json($venta, 201);
    }

    public function show(string $id)
    {
        $venta = Venta::with('cliente')->where('estado', 1)->findOrFail($id);

        return response()->json($venta);
    }

    public function update(Request $request, string $id)
    {
        $venta = Venta::where('estado', 1)->findOrFail($id);
        $venta->update($request->all());

        return response()->json($venta);
    }

    public function destroy(string $id)
    {
        $venta = Venta::where('estado', 1)->findOrFail($id);
        $venta->update(['estado' => 0]);

        return response()->json([
            'message' => 'Venta eliminada',
        ]);
    }
}
