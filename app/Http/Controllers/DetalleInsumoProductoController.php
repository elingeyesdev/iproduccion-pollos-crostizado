<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetalleInsumoProductoRequest;
use App\Models\DetalleInsumoProducto;
use App\Models\Insumo;
use App\Models\Produccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DetalleInsumoProductoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $detalles = DetalleInsumoProducto::query()
            ->with(['produccion:id,orden_produccion_id', 'insumo:id,nombre'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('insumo', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('detalle-insumo-producto.index', compact('detalles', 'search'));
    }

    public function create(): View
    {
        $detalleInsumoProducto = new DetalleInsumoProducto();
        $producciones = Produccion::where('estado', 1)->orderByDesc('created_at')->get();
        $insumos = Insumo::where('estado', 1)->orderBy('nombre')->get();

        return view('detalle-insumo-producto.create', compact('detalleInsumoProducto', 'producciones', 'insumos'));
    }

    public function store(DetalleInsumoProductoRequest $request): RedirectResponse
    {
        DetalleInsumoProducto::create($request->validated());

        return Redirect::route('detalle-insumo-productos.index')
            ->with('success', 'Detalle insumo-producto creado correctamente.');
    }

    public function show(DetalleInsumoProducto $detalleInsumoProducto): View
    {
        $detalleInsumoProducto->load(['produccion', 'insumo']);

        return view('detalle-insumo-producto.show', compact('detalleInsumoProducto'));
    }

    public function edit(DetalleInsumoProducto $detalleInsumoProducto): View
    {
        $producciones = Produccion::where('estado', 1)->orderByDesc('created_at')->get();
        $insumos = Insumo::where('estado', 1)->orderBy('nombre')->get();

        return view('detalle-insumo-producto.edit', compact('detalleInsumoProducto', 'producciones', 'insumos'));
    }

    public function update(DetalleInsumoProductoRequest $request, DetalleInsumoProducto $detalleInsumoProducto): RedirectResponse
    {
        $detalleInsumoProducto->update($request->validated());

        return Redirect::route('detalle-insumo-productos.index')
            ->with('success', 'Detalle insumo-producto actualizado correctamente.');
    }

    public function destroy(DetalleInsumoProducto $detalleInsumoProducto): RedirectResponse
    {
        $detalleInsumoProducto->update(['estado' => 0]);

        return Redirect::route('detalle-insumo-productos.index')
            ->with('success', 'Detalle insumo-producto eliminado correctamente.');
    }
}
