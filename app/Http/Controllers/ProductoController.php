<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Producto;
use App\Models\TipoProducto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProductoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $productos = Producto::query()
            ->with(['tipoProducto:id,nombre'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhereHas('tipoProducto', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('producto.index', compact('productos', 'search'));
    }

    public function create(): View
    {
        $producto = new Producto(['cantidad_actual' => 0]);
        $tipoProductos = TipoProducto::where('estado', 1)->orderBy('nombre')->get();

        return view('producto.create', compact('producto', 'tipoProductos'));
    }

    public function store(ProductoRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['cantidad_actual'] = 0;

        Producto::create($data);

        return Redirect::route('productos.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function show(Producto $producto): View
    {
        $producto->load([
            'tipoProducto:id,nombre',
            'componentes' => fn ($query) => $query->where('estado', 1)->orderBy('nombre'),
        ]);

        return view('producto.show', compact('producto'));
    }

    public function edit(Producto $producto): View
    {
        $tipoProductos = TipoProducto::where('estado', 1)->orderBy('nombre')->get();
        return view('producto.edit', compact('producto', 'tipoProductos'));
    }

    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $producto->update($request->validated());

        return Redirect::route('productos.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto): RedirectResponse
    {
        $producto->update(['estado' => 0]);

        return Redirect::route('productos.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
