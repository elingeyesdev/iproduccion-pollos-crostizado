<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoComposicionRequest;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductoComposicionController extends Controller
{
    /**
     * Formulario para crear la composición inicial de un producto
     */
    public function create(): View
    {
        $productosActivos = Producto::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        return view('producto-composicion.create', compact('productosActivos'));
    }

    /**
     * Registra una nueva composición de producto
     */
    public function store(ProductoComposicionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $ingredientes = (array) ($validated['ingredientes'] ?? []);

        try {
            DB::beginTransaction();

            DB::table('composicion_productos')
                ->where('producto_padre_id', $validated['producto_id'])
                ->update(['estado' => 0]);

            foreach ($ingredientes as $ingrediente) {
                DB::table('composicion_productos')->updateOrInsert(
                    [
                        'producto_padre_id' => $validated['producto_id'],
                        'producto_hijo_id' => $ingrediente['id'],
                    ],
                    [
                        'cantidad' => $ingrediente['cantidad'],
                        'estado' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            DB::commit();

            return redirect()->route('productos-composicion.index')
                ->with('success', 'Composición creada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()->withErrors([
                'error' => 'Error al crear composición: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Muestra la lista de todos los productos y su composición actual (combos)
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));
        
        $productos = Producto::query()
            ->with(['ingredientes', 'tipoProducto'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('producto-composicion.index', compact('productos', 'search'));
    }

    /**
     * Formulario para definir qué otros productos componen a este producto
     */
    public function edit(Producto $producto): View
    {
        $producto->load('ingredientes');
        
        // Obtenemos todos los productos activos para que sirvan como "ingredientes"
        // Excluimos al mismo producto para evitar que se contenga a sí mismo
        $todosLosProductos = Producto::where('estado', 1)
            ->where('id', '!=', $producto->id)
            ->orderBy('nombre')
            ->get();

        return view('producto-composicion.edit', compact('producto', 'todosLosProductos'));
    }

    /**
     * Guarda la relación de composición (Producto -> Productos)
     */
    public function update(ProductoComposicionRequest $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validated();
        $ingredientes = (array) ($validated['ingredientes'] ?? []);

        try {
            DB::beginTransaction();

            // Desactivamos composiciones anteriores (Eliminación lógica en el pivot)
            DB::table('composicion_productos')
                ->where('producto_padre_id', $producto->id)
                ->update(['estado' => 0]);

            foreach ($ingredientes as $ingrediente) {
                DB::table('composicion_productos')->updateOrInsert(
                    [
                        'producto_padre_id' => $producto->id,
                        'producto_hijo_id' => $ingrediente['id'],
                    ],
                    [
                        'cantidad' => $ingrediente['cantidad'],
                        'estado' => 1,
                        'updated_at' => now(),
                        'created_at' => now(),
                    ]
                );
            }

            DB::commit();
            return redirect()->route('productos-composicion.index')
                ->with('success', "Composición de '{$producto->nombre}' guardada correctamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al guardar composición: ' . $e->getMessage()]);
        }
    }
}
