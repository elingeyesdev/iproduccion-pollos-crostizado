<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrdenProduccionRequest;
use App\Models\OrdenProduccion;
use App\Models\Insumo;
use App\Models\OrdenProduccionInsumo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class OrdenProduccionController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $ordenProducciones = OrdenProduccion::query()
            ->withCount('insumos')
            ->withCount(['insumos as insumos_pendientes_count' => function ($query) {
                $query->where('estado', 1)->whereDoesntHave('produccion');
            }])
            ->when($search, function ($query) use ($search) {
                $query->where('numero_orden', 'like', "%{$search}%")
                    ->orWhere('fecha_programada', 'like', "%{$search}%");
            })
            ->orderByDesc('fecha_programada')
            ->paginate(10)
            ->withQueryString();

        return view('orden-produccion.index', compact('ordenProducciones', 'search'));
    }

    public function create(): View
    {
        $ordenProduccion = new OrdenProduccion();
        $insumos = Insumo::with('unidadMedida')
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('orden-produccion.create', compact('ordenProduccion', 'insumos'));
    }

    public function store(OrdenProduccionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $ordenProduccion = OrdenProduccion::create([
                'numero_orden' => $data['numero_orden'],
                'fecha_programada' => $data['fecha_programada'],
                'estado' => 1,
            ]);

            $detalles = collect($data['insumos'])->map(function ($insumoData) {
                return new OrdenProduccionInsumo([
                    'insumo_id' => $insumoData['insumo_id'],
                    'cantidad' => $insumoData['cantidad'],
                    'estado' => 1,
                ]);
            });

            $ordenProduccion->insumos()->saveMany($detalles->all());
        });

        return Redirect::route('orden-producciones.index')
            ->with('success', 'Orden de producción creada correctamente.');
    }

    public function show(OrdenProduccion $ordenProduccion): View
    {
        $ordenProduccion->loadMissing(['insumos.insumo.unidadMedida']);
        $ordenProduccion->loadCount(['insumos as insumos_pendientes_count' => function ($query) {
            $query->where('estado', 1)->whereDoesntHave('produccion');
        }]);

        return view('orden-produccion.show', compact('ordenProduccion'));
    }

    public function edit(OrdenProduccion $ordenProduccion): View
    {
        $ordenProduccion->load('insumos.insumo.unidadMedida');
        $insumos = Insumo::with('unidadMedida')
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('orden-produccion.edit', compact('ordenProduccion', 'insumos'));
    }

    public function update(OrdenProduccionRequest $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $ordenProduccion) {
            $ordenProduccion->update([
                'numero_orden' => $data['numero_orden'],
                'fecha_programada' => $data['fecha_programada'],
                'estado' => 1,
            ]);

            $ordenProduccion->insumos()->delete();

            $detalles = collect($data['insumos'])->map(function ($insumoData) {
                return new OrdenProduccionInsumo([
                    'insumo_id' => $insumoData['insumo_id'],
                    'cantidad' => $insumoData['cantidad'],
                    'estado' => 1,
                ]);
            });

            $ordenProduccion->insumos()->saveMany($detalles->all());
        });

        return Redirect::route('orden-producciones.index')
            ->with('success', 'Orden de producción actualizada correctamente.');
    }

    public function destroy(OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $ordenProduccion->update(['estado' => 0]);

        return Redirect::route('orden-producciones.index')
            ->with('success', 'Orden de producción eliminada correctamente.');
    }
}
