<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngresoInsumoRequest;
use App\Models\IngresoInsumo;
use App\Models\Insumo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class IngresoInsumoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $ingresos = IngresoInsumo::query()
            ->with(['detalles.insumo'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('descripcion', 'like', "%{$search}%")
                    ->orWhere('fecha_ingreso', 'like', "%{$search}%");
            })
            ->orderByDesc('fecha_ingreso')
            ->paginate(10)
            ->withQueryString();

        return view('ingreso-insumo.index', compact('ingresos', 'search'));
    }

    public function create(): View
    {
        $ingreso = new IngresoInsumo();
        $insumos = Insumo::where('estado', 1)->orderBy('nombre')->get();
        $detalleRows = $this->buildDetalleRows();

        return view('ingreso-insumo.create', compact('ingreso', 'insumos', 'detalleRows'));
    }

    public function store(IngresoInsumoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $ingreso = IngresoInsumo::create([
                'fecha_ingreso' => $data['fecha_ingreso'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            foreach ($data['detalles'] as $detalle) {
                $ingreso->detalles()->create([
                    'insumo_id' => $detalle['insumo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                ]);

                Insumo::where('id', $detalle['insumo_id'])
                    ->increment('stock_actual', (float) $detalle['cantidad']);
            }
        });

        return redirect()->route('ingreso-insumos.index')
            ->with('success', 'Ingreso de insumos registrado correctamente.');
    }

    public function show(IngresoInsumo $ingresoInsumo): View
    {
        $ingresoInsumo->load('detalles.insumo');

        return view('ingreso-insumo.show', ['ingreso' => $ingresoInsumo]);
    }

    public function edit(IngresoInsumo $ingresoInsumo): View
    {
        $ingresoInsumo->load('detalles.insumo');
        $insumos = Insumo::where('estado', 1)->orderBy('nombre')->get();
        $detalleRows = $this->buildDetalleRows($ingresoInsumo);

        return view('ingreso-insumo.edit', ['ingreso' => $ingresoInsumo, 'insumos' => $insumos, 'detalleRows' => $detalleRows]);
    }

    public function update(IngresoInsumoRequest $request, IngresoInsumo $ingresoInsumo): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $ingresoInsumo) {
            foreach ($ingresoInsumo->detalles as $detalle) {
                Insumo::where('id', $detalle->insumo_id)
                    ->decrement('stock_actual', (float) $detalle->cantidad);
            }

            $ingresoInsumo->update([
                'fecha_ingreso' => $data['fecha_ingreso'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            $ingresoInsumo->detalles()->delete();

            foreach ($data['detalles'] as $detalle) {
                $ingresoInsumo->detalles()->create([
                    'insumo_id' => $detalle['insumo_id'],
                    'cantidad' => $detalle['cantidad'],
                    'precio_unitario' => $detalle['precio_unitario'],
                ]);

                Insumo::where('id', $detalle['insumo_id'])
                    ->increment('stock_actual', (float) $detalle['cantidad']);
            }
        });

        return redirect()->route('ingreso-insumos.index')
            ->with('success', 'Ingreso de insumos actualizado correctamente.');
    }

    public function destroy(IngresoInsumo $ingresoInsumo): RedirectResponse
    {
        DB::transaction(function () use ($ingresoInsumo) {
            foreach ($ingresoInsumo->detalles as $detalle) {
                Insumo::where('id', $detalle->insumo_id)
                    ->decrement('stock_actual', (float) $detalle->cantidad);
            }

            $ingresoInsumo->update(['estado' => 0]);
        });

        return redirect()->route('ingreso-insumos.index')
            ->with('success', 'Ingreso de insumos eliminado correctamente.');
    }

    private function buildDetalleRows(?IngresoInsumo $ingreso = null): array
    {
        if (!$ingreso || !$ingreso->exists) {
            return [[
                'insumo_id' => '',
                'cantidad' => '',
                'precio_unitario' => '',
            ]];
        }

        return $ingreso->detalles->map(fn ($detalle) => [
            'insumo_id' => $detalle->insumo_id,
            'cantidad' => $detalle->cantidad,
            'precio_unitario' => $detalle->precio_unitario,
        ])->values()->all();
    }
}
