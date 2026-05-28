<?php

namespace App\Http\Controllers;

use App\Http\Requests\ComponenteProductoRequest;
use App\Models\ComponenteProducto;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ComponenteProductoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $componenteProductos = ComponenteProducto::query()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('componente-producto.index', compact('componenteProductos', 'search'));
    }

    public function create(): View
    {
        $componenteProducto = new ComponenteProducto();
        return view('componente-producto.create', compact('componenteProducto'));
    }

    public function store(ComponenteProductoRequest $request): RedirectResponse
    {
        ComponenteProducto::create($request->validated());

        return Redirect::route('componente-productos.index')
            ->with('success', 'Componente de producto creado correctamente.');
    }

    public function show(ComponenteProducto $componenteProducto): View
    {
        return view('componente-producto.show', compact('componenteProducto'));
    }

    public function edit(ComponenteProducto $componenteProducto): View
    {
        return view('componente-producto.edit', compact('componenteProducto'));
    }

    public function update(ComponenteProductoRequest $request, ComponenteProducto $componenteProducto): RedirectResponse
    {
        $componenteProducto->update($request->validated());

        return Redirect::route('componente-productos.index')
            ->with('success', 'Componente de producto actualizado correctamente.');
    }

    public function destroy(ComponenteProducto $componenteProducto): RedirectResponse
    {
        $componenteProducto->update(['estado' => 0]);

        return Redirect::route('componente-productos.index')
            ->with('success', 'Componente de producto eliminado correctamente.');
    }

    public function composiciones(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $productos = Producto::query()
            ->with(['tipoProducto:id,nombre', 'componentes' => fn ($query) => $query->where('estado', 1)])
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

        return view('componente-producto.composiciones-index', compact('productos', 'search'));
    }

    public function editComposicion(Producto $producto): View
    {
        $producto->load(['tipoProducto:id,nombre', 'componentes' => function ($query) {
            $query->where('estado', 1)->orderBy('nombre');
        }]);

        $componentesDisponibles = ComponenteProducto::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();

        $componentRows = old('componentes', $producto->componentes->map(function ($componente) {
            return [
                'componente_producto_id' => $componente->id,
                'cantidad' => $componente->pivot->cantidad,
            ];
        })->values()->all());

        if (empty($componentRows)) {
            $componentRows = [[
                'componente_producto_id' => '',
                'cantidad' => '',
            ]];
        }

        return view('componente-producto.composicion-edit', compact('producto', 'componentesDisponibles', 'componentRows'));
    }

    public function updateComposicion(Request $request, Producto $producto): RedirectResponse
    {
        $data = $request->validate([
            'componentes' => ['required', 'array', 'min:1'],
            'componentes.*.componente_producto_id' => [
                'required',
                'integer',
                Rule::exists('componente_productos', 'id')->where(fn ($query) => $query->where('estado', 1)),
            ],
            'componentes.*.cantidad' => ['required', 'numeric', 'min:0.01'],
        ]);

        $componentes = collect($data['componentes'])
            ->filter(fn ($row) => filled($row['componente_producto_id'] ?? null) && filled($row['cantidad'] ?? null))
            ->values();

        if ($componentes->isEmpty()) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['componentes' => 'Debes registrar al menos un componente para la composición.']);
        }

        if ($componentes->pluck('componente_producto_id')->duplicates()->isNotEmpty()) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['componentes' => 'No repitas el mismo componente en la composición.']);
        }

        $syncData = $componentes->mapWithKeys(function ($row) {
            return [
                (int) $row['componente_producto_id'] => [
                    'cantidad' => (float) $row['cantidad'],
                    'estado' => 1,
                ],
            ];
        })->all();

        DB::transaction(function () use ($producto, $syncData) {
            $producto->componentes()->sync($syncData);
        });

        return Redirect::route('componente-productos.composiciones')
            ->with('success', 'Composición del producto actualizada correctamente.');
    }
}
