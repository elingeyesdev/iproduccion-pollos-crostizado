<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductoComposicionRequest;
use App\Models\Producto;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ComponenteProductoApiController extends Controller
{
    public function index(): JsonResponse
    {
        $productos = Producto::query()
            ->with(['ingredientes', 'tipoProducto'])
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return response()->json($productos);
    }

    public function show(Producto $producto): JsonResponse
    {
        $producto->load(['ingredientes', 'tipoProducto']);

        return response()->json($producto);
    }

    public function store(ProductoComposicionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $ingredientes = (array) ($validated['ingredientes'] ?? []);
        $productoId = $validated['producto_id'];

        DB::transaction(function () use ($productoId, $ingredientes): void {
            DB::table('composicion_productos')
                ->where('producto_padre_id', $productoId)
                ->update(['estado' => 0]);

            foreach ($ingredientes as $ingrediente) {
                DB::table('composicion_productos')->updateOrInsert(
                    [
                        'producto_padre_id' => $productoId,
                        'producto_hijo_id' => $ingrediente['id'],
                    ],
                    [
                        'cantidad' => $ingrediente['cantidad'],
                        'estado' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        $producto = Producto::with(['ingredientes', 'tipoProducto'])->findOrFail($productoId);

        return response()->json($producto, 201);
    }

    public function update(ProductoComposicionRequest $request, Producto $producto): JsonResponse
    {
        $validated = $request->validated();
        $ingredientes = (array) ($validated['ingredientes'] ?? []);

        DB::transaction(function () use ($producto, $ingredientes): void {
            DB::table('composicion_productos')
                ->where('producto_padre_id', $producto->id)
                ->update(['estado' => 0]);

            foreach ($ingredientes as $ingrediente) {
                DB::table('composicion_productos')->updateOrInsert(
                    [
                        'producto_padre_id' => $producto->id,
                        'producto_hijo_id' => $ingrediente['id'],
                    ],
                    [
                        'cantidad' => $ingrediente['cantidad'],
                        'estado' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }
        });

        $producto->load(['ingredientes', 'tipoProducto']);

        return response()->json($producto);
    }
}
