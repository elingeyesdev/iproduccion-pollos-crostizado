<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngresoBebidaRequest;
use App\Models\IngresoBebida;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;

class IngresoBebidasController extends Controller
{
    public function index()
    {
        return response()->json(
            IngresoBebida::with('productos')
                ->where('estado', 1)
                ->orderByDesc('fecha_ingreso')
                ->get()
        );
    }

    public function store(IngresoBebidaRequest $request)
    {
        $data = $request->validated();

        $ingresoBebida = DB::transaction(function () use ($data) {
            $nombreLote = !empty($data['nombre'])
                ? $data['nombre']
                : $this->generarNombreLote();

            $ingresoBebida = IngresoBebida::create([
                'nombre' => $nombreLote,
                'descripcion' => $data['descripcion'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
            ]);

            foreach ($data['productos'] as $item) {
                $ingresoBebida->productos()->attach($item['producto_id'], [
                    'cantidad_ingresa' => $item['cantidad_ingresa'],
                    'precio' => $item['precio'],
                ]);

                Producto::where('id', $item['producto_id'])
                    ->increment('cantidad_actual', (int) $item['cantidad_ingresa']);
            }

            return $ingresoBebida;
        });

        $ingresoBebida->load('productos');

        return response()->json($ingresoBebida, 201);
    }

    public function show(string $id)
    {
        $ingresoBebida = IngresoBebida::with('productos.tipoProducto')
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($ingresoBebida);
    }

    public function update(IngresoBebidaRequest $request, string $id)
    {
        $data = $request->validated();

        $ingresoBebida = DB::transaction(function () use ($data, $id) {
            $ingresoBebida = IngresoBebida::where('estado', 1)->findOrFail($id);
            $ingresoBebida->load('productos');

            // Revertir stock anterior
            foreach ($ingresoBebida->productos as $producto) {
                Producto::where('id', $producto->id)
                    ->decrement('cantidad_actual', (int) $producto->pivot->cantidad_ingresa);
            }

            $ingresoBebida->update([
                'nombre' => $data['nombre'] ?? $ingresoBebida->nombre,
                'descripcion' => $data['descripcion'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
            ]);

            $ingresoBebida->productos()->detach();

            foreach ($data['productos'] as $item) {
                $ingresoBebida->productos()->attach($item['producto_id'], [
                    'cantidad_ingresa' => $item['cantidad_ingresa'],
                    'precio' => $item['precio'],
                ]);

                Producto::where('id', $item['producto_id'])
                    ->increment('cantidad_actual', (int) $item['cantidad_ingresa']);
            }

            return $ingresoBebida;
        });

        $ingresoBebida->load('productos');

        return response()->json($ingresoBebida);
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $ingresoBebida = IngresoBebida::where('estado', 1)->findOrFail($id);
            $ingresoBebida->load('productos');

            foreach ($ingresoBebida->productos as $producto) {
                Producto::where('id', $producto->id)
                    ->decrement('cantidad_actual', (int) $producto->pivot->cantidad_ingresa);
            }

            $ingresoBebida->update(['estado' => 0]);
        });

        return response()->json([
            'message' => 'Ingreso de bebida eliminado',
        ]);
    }

    private function generarNombreLote(): string
    {
        $anioMes = now()->format('Y-m');
        $prefijo = 'LOTE-BEB-' . $anioMes . '-';

        $ultimoMes = IngresoBebida::where('nombre', 'like', $prefijo . '%')
            ->latest('id')
            ->first();

        $numero = 1;
        if ($ultimoMes && preg_match('/-(\d{3})$/', $ultimoMes->nombre, $matches)) {
            $numero = (int) $matches[1] + 1;
        }

        return $prefijo . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}
