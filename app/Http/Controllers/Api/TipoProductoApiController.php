<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TipoProductoRequest;
use App\Models\TipoProducto;
use Illuminate\Http\JsonResponse;

class TipoProductoApiController extends Controller
{
    public function index(): JsonResponse
    {
        $tipoProductos = TipoProducto::query()
            ->where('estado', 1)
            ->get();

        return response()->json($tipoProductos);
    }

    public function store(TipoProductoRequest $request): JsonResponse
    {
        $tipoProducto = TipoProducto::create($request->validated());

        return response()->json($tipoProducto, 201);
    }

    public function show(string $id): JsonResponse
    {
        $tipoProducto = TipoProducto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($tipoProducto);
    }

    public function update(TipoProductoRequest $request, string $id): JsonResponse
    {
        $tipoProducto = TipoProducto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $tipoProducto->update($request->validated());

        return response()->json($tipoProducto);
    }

    public function destroy(string $id): JsonResponse
    {
        $tipoProducto = TipoProducto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $tipoProducto->update(['estado' => 0]);

        return response()->json([
            'message' => 'Tipo de producto eliminado',
        ]);
    }
}
