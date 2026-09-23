<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IngresoInsumo;
use App\Models\IngresoInsumoDetalle;
use App\Models\Insumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IngresoInsumoApiController extends Controller
{
    public function index()
    {
        return response()->json(
            IngresoInsumo::with(['detalles' => function ($q) {
                $q->where('estado', 1)->with('insumo');
            }])->where('estado', 1)->get()
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'fecha_ingreso' => 'required|date',
            'descripcion' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.insumo_id' => 'required|exists:insumos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $ingresoInsumo = DB::transaction(function () use ($request) {
            $ingreso = IngresoInsumo::create([
                'fecha_ingreso' => $request->fecha_ingreso,
                'descripcion' => $request->descripcion,
            ]);

            foreach ($request->detalles as $detalle) {
                IngresoInsumoDetalle::create([
                    'ingreso_insumo_id' => $ingreso->id,
                    'insumo_id' => $detalle['insumo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                ]);

                $insumo = Insumo::findOrFail($detalle['insumo_id']);
                $insumo->update([
                    'stock_actual' => $insumo->stock_actual + $detalle['cantidad'],
                ]);
            }

            return $ingreso;
        });

        $ingresoInsumo->load(['detalles' => function ($q) {
            $q->where('estado', 1)->with('insumo');
        }]);

        return response()->json($ingresoInsumo, 201);
    }

    public function show(string $id)
    {
        $ingresoInsumo = IngresoInsumo::with(['detalles' => function ($q) {
            $q->where('estado', 1)->with('insumo');
        }])->where('estado', 1)->findOrFail($id);

        return response()->json($ingresoInsumo);
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'fecha_ingreso' => 'required|date',
            'descripcion' => 'nullable|string',
            'detalles' => 'required|array|min:1',
            'detalles.*.insumo_id' => 'required|exists:insumos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.precio_unitario' => 'required|numeric|min:0',
        ]);

        $ingresoInsumo = DB::transaction(function () use ($request, $id) {
            $ingreso = IngresoInsumo::where('estado', 1)->findOrFail($id);

            $ingreso->update([
                'fecha_ingreso' => $request->fecha_ingreso ?? $ingreso->fecha_ingreso,
                'descripcion' => $request->descripcion ?? $ingreso->descripcion,
            ]);

            if ($request->has('detalles')) {
                foreach ($ingreso->detalles()->where('estado', 1)->get() as $detalle) {
                    $insumo = Insumo::findOrFail($detalle->insumo_id);
                    $insumo->update([
                        'stock_actual' => $insumo->stock_actual - $detalle->cantidad,
                    ]);
                    $detalle->update(['estado' => 0]);
                }

                foreach ($request->detalles as $detalle) {
                    IngresoInsumoDetalle::create([
                        'ingreso_insumo_id' => $ingreso->id,
                        'insumo_id' => $detalle['insumo_id'],
                        'cantidad' => $detalle['cantidad'],
                        'precio_unitario' => $detalle['precio_unitario'],
                    ]);

                    $insumo = Insumo::findOrFail($detalle['insumo_id']);
                    $insumo->update([
                        'stock_actual' => $insumo->stock_actual + $detalle['cantidad'],
                    ]);
                }
            }

            return $ingreso;
        });

        $ingresoInsumo->load(['detalles' => function ($q) {
            $q->where('estado', 1)->with('insumo');
        }]);

        return response()->json($ingresoInsumo);
    }

    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $ingreso = IngresoInsumo::where('estado', 1)->findOrFail($id);

            foreach ($ingreso->detalles()->where('estado', 1)->get() as $detalle) {
                $insumo = Insumo::findOrFail($detalle->insumo_id);
                $insumo->update([
                    'stock_actual' => max(0, $insumo->stock_actual - $detalle->cantidad),
                ]);
                $detalle->update(['estado' => 0]);
            }

            $ingreso->update(['estado' => 0]);
        });

        return response()->json([
            'message' => 'Ingreso de insumos eliminado',
        ]);
    }
}
