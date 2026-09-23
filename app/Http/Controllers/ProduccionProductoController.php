<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProduccionProductoRequest;
use App\Models\Produccion;
use App\Models\ProduccionProducto;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProduccionProductoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $produccionProductos = ProduccionProducto::query()
            ->with(['produccion:id,orden_produccion_insumo_id', 'producto:id,nombre'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('producto', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('produccion-producto.index', compact('produccionProductos', 'search'));
    }

    public function create(): View
    {
        $produccionProducto = new ProduccionProducto();
        $producciones = Produccion::where('estado', 1)->orderByDesc('created_at')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        return view('produccion-producto.create', compact('produccionProducto', 'producciones', 'productos'));
    }

    public function store(ProduccionProductoRequest $request): RedirectResponse
    {
        ProduccionProducto::create($request->validated());

        return Redirect::route('produccion-productos.index')
            ->with('success', 'Producción-producto creada correctamente.');
    }

    public function show(ProduccionProducto $produccionProducto): View
    {
        $produccionProducto->load(['produccion', 'producto']);

        return view('produccion-producto.show', compact('produccionProducto'));
    }

    public function edit(ProduccionProducto $produccionProducto): View
    {
        $producciones = Produccion::where('estado', 1)->orderByDesc('created_at')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        return view('produccion-producto.edit', compact('produccionProducto', 'producciones', 'productos'));
    }

    public function update(ProduccionProductoRequest $request, ProduccionProducto $produccionProducto): RedirectResponse
    {
        $produccionProducto->update($request->validated());

        return Redirect::route('produccion-productos.index')
            ->with('success', 'Producción-producto actualizada correctamente.');
    }

    public function destroy(ProduccionProducto $produccionProducto): RedirectResponse
    {
        $produccionProducto->update(['estado' => 0]);

        return Redirect::route('produccion-productos.index')
            ->with('success', 'Producción-producto eliminada correctamente.');
    }
}
