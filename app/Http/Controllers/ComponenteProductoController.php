<?php

namespace App\Http\Controllers;

use App\Models\ComponenteProducto;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;

class ComponenteProductoController extends Controller
{
    /**
     * CRUD original para ComponenteProducto (Materia Prima)
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        $componenteProductos = ComponenteProducto::query()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->paginate(10);

        return view('componente-producto.index', compact('componenteProductos', 'search'));
    }

    public function create(): View
    {
        $componenteProducto = new ComponenteProducto();
        return view('componente-producto.create', compact('componenteProducto'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate(['nombre' => 'required|string|max:255', 'equivalencia' => 'nullable|string']);
        ComponenteProducto::create($request->all());
        return Redirect::route('componente-productos.index')->with('success', 'Componente creado correctamente.');
    }

    public function show(ComponenteProducto $componenteProducto): View
    {
        return view('componente-producto.show', compact('componenteProducto'));
    }

    public function edit(ComponenteProducto $componenteProducto): View
    {
        return view('componente-producto.edit', compact('componenteProducto'));
    }

    public function update(Request $request, ComponenteProducto $componenteProducto): RedirectResponse
    {
        $request->validate(['nombre' => 'required|string|max:255', 'equivalencia' => 'nullable|string']);
        $componenteProducto->update($request->all());
        return Redirect::route('componente-productos.index')->with('success', 'Componente actualizado correctamente.');
    }

    public function destroy(ComponenteProducto $componenteProducto): RedirectResponse
    {
        $componenteProducto->update(['estado' => 0]);
        return Redirect::route('componente-productos.index')->with('success', 'Componente eliminado correctamente.');
    }

    /**
     * NUEVAS FUNCIONALIDADES: Gestión de Composición (Combos de Productos)
     */
    public function composiciones(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        
        $productos = Producto::query()
            ->with(['ingredientes', 'tipoProducto'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10);

        return view('componente-producto.composiciones', compact('productos', 'search'));
    }

    public function editComposicion(Producto $producto): View
    {
        $producto->load('ingredientes');
        $todosLosProductos = Producto::where('estado', 1)
            ->where('id', '!=', $producto->id)
            ->orderBy('nombre')
            ->get();

        return view('componente-producto.edit_composicion', compact('producto', 'todosLosProductos'));
    }

    public function updateComposicion(Request $request, Producto $producto): RedirectResponse
    {
        $request->validate([
            'ingredientes' => 'nullable|array',
            'ingredientes.*.id' => 'required|exists:productos,id',
            'ingredientes.*.cantidad' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            DB::table('composicion_productos')
                ->where('producto_padre_id', $producto->id)
                ->update(['estado' => 0]);

            if ($request->has('ingredientes')) {
                foreach ($request->ingredientes as $ingrediente) {
                    DB::table('composicion_productos')->updateOrInsert(
                        ['producto_padre_id' => $producto->id, 'producto_hijo_id' => $ingrediente['id']],
                        ['cantidad' => $ingrediente['cantidad'], 'estado' => 1, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }

            DB::commit();
            return redirect()->route('componente-productos.composiciones')
                ->with('success', "Composición de '{$producto->nombre}' actualizada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al actualizar composición: ' . $e->getMessage()]);
        }
    }
}
