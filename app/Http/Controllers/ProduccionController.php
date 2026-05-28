<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProduccionRequest;
use App\Models\Produccion;
use App\Models\OrdenProduccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProduccionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $producciones = Produccion::query()
            ->with(['ordenProduccion:id,numero_orden,fecha_programada', 'insumos'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('ordenProduccion', function ($q) use ($search) {
                    $q->where('numero_orden', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('produccion.index', compact('producciones', 'search'));
    }

    public function create(): View
    {
        $produccion = new Produccion();
        $ordenProducciones = OrdenProduccion::where('estado', 1)->orderByDesc('fecha_programada')->get();

        return view('produccion.create', compact('produccion', 'ordenProducciones'));
    }

    public function store(ProduccionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $ordenProduccion = OrdenProduccion::with(['insumos.insumo'])->findOrFail($data['orden_produccion_id']);

            $produccion = Produccion::create([
                'orden_produccion_id' => $ordenProduccion->id,
                'cantidad_insumo' => $data['cantidad_insumo'],
                'cantidad_obtenida' => $data['cantidad_obtenida'],
                'estado' => 1,
            ]);

            foreach ($ordenProduccion->insumos as $detalleOrden) {
                $insumo = $detalleOrden->insumo;
                $cantidadADescontar = (float) $detalleOrden->cantidad;

                if ((float) $insumo->stock_actual < $cantidadADescontar) {
                    throw new \RuntimeException("Stock insuficiente para el insumo: {$insumo->nombre}");
                }

                $insumo->stock_actual = (float) $insumo->stock_actual - $cantidadADescontar;
                $insumo->save();

                $produccion->insumos()->attach($insumo->id, [
                    'cantidad' => $cantidadADescontar,
                    'estado' => 1,
                ]);
            }
        });

            return Redirect::route('producciones.index')
                ->with('success', 'Producción confirmada. Inventario descontado y stock de productos actualizado.');
    }

    public function show(Produccion $produccione): View
    {
        $produccione->load(['ordenProduccion', 'insumos', 'mermas']);
        return view('produccion.show', compact('produccione'));
    }

    public function edit(Produccion $produccione): View
    {
        $ordenProducciones = OrdenProduccion::where('estado', 1)->orderByDesc('fecha_programada')->get();

        return view('produccion.edit', compact('produccione', 'ordenProducciones'));
    }

    public function update(ProduccionRequest $request, Produccion $produccione): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $produccione) {
            $produccione->loadMissing(['insumos']);

            foreach ($produccione->insumos as $insumoActual) {
                $insumoActual->stock_actual = (float) $insumoActual->stock_actual + (float) $insumoActual->pivot->cantidad;
                $insumoActual->save();
            }

            $produccione->insumos()->detach();

            $ordenProduccion = OrdenProduccion::with(['insumos.insumo'])->findOrFail($data['orden_produccion_id']);

            $produccione->update([
                'orden_produccion_id' => $ordenProduccion->id,
                'cantidad_insumo' => $data['cantidad_insumo'],
                'cantidad_obtenida' => $data['cantidad_obtenida'],
                'estado' => 1,
            ]);

            foreach ($ordenProduccion->insumos as $detalleOrden) {
                $insumo = $detalleOrden->insumo;
                $cantidadADescontar = (float) $detalleOrden->cantidad;

                if ((float) $insumo->stock_actual < $cantidadADescontar) {
                    throw new \RuntimeException("Stock insuficiente para el insumo: {$insumo->nombre}");
                }

                $insumo->stock_actual = (float) $insumo->stock_actual - $cantidadADescontar;
                $insumo->save();

                $produccione->insumos()->attach($insumo->id, [
                    'cantidad' => $cantidadADescontar,
                    'estado' => 1,
                ]);
            }
        });

        return Redirect::route('producciones.index')
            ->with('success', 'Producción actualizada correctamente.');
    }

    public function destroy(Produccion $produccione): RedirectResponse
    {
        $produccione->update(['estado' => 0]);

        return Redirect::route('producciones.index')
            ->with('success', 'Producción eliminada correctamente.');
    }
}
