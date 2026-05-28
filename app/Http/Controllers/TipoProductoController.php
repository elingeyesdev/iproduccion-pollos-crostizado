<?php

namespace App\Http\Controllers;

use App\Http\Requests\TipoProductoRequest;
use App\Models\TipoProducto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class TipoProductoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $tipoProductos = TipoProducto::query()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('tipo-producto.index', compact('tipoProductos', 'search'));
    }

    public function create(): View
    {
        $tipoProducto = new TipoProducto();
        return view('tipo-producto.create', compact('tipoProducto'));
    }

    public function store(TipoProductoRequest $request): RedirectResponse
    {
        TipoProducto::create($request->validated());

        return Redirect::route('tipo-productos.index')
            ->with('success', 'Tipo de producto creado correctamente.');
    }

    public function show(TipoProducto $tipoProducto): View
    {
        return view('tipo-producto.show', compact('tipoProducto'));
    }

    public function edit(TipoProducto $tipoProducto): View
    {
        return view('tipo-producto.edit', compact('tipoProducto'));
    }

    public function update(TipoProductoRequest $request, TipoProducto $tipoProducto): RedirectResponse
    {
        $tipoProducto->update($request->validated());

        return Redirect::route('tipo-productos.index')
            ->with('success', 'Tipo de producto actualizado correctamente.');
    }

    public function destroy(TipoProducto $tipoProducto): RedirectResponse
    {
        $tipoProducto->update(['estado' => 0]);

        return Redirect::route('tipo-productos.index')
            ->with('success', 'Tipo de producto eliminado correctamente.');
    }
}
