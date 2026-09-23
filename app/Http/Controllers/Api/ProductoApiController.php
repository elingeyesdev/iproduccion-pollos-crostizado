<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;

class ProductoApiController extends Controller
{
    public function index(): JsonResponse
    {
        $productos = Producto::query()
            ->with('tipoProducto')
            ->where('estado', 1)
            ->get();

        return response()->json($productos);
    }

    public function store(ProductoRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['estado'] = $data['estado'] ?? 1;

        $producto = Producto::create($data);

        return response()->json($producto, 201);
    }

    public function show(string $id): JsonResponse
    {
        $producto = Producto::query()
            ->with('tipoProducto')
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($producto);
    }

    public function update(ProductoRequest $request, string $id): JsonResponse
    {
        $producto = Producto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $data = $request->validated();
        $data['estado'] = $data['estado'] ?? $producto->estado;

        $producto->update($data);

        return response()->json($producto);
    }

    public function destroy(string $id): JsonResponse
    {
        $producto = Producto::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $producto->update(['estado' => 0]);

        return response()->json([
            'message' => 'Producto eliminado',
        ]);
    }
}
