<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngresoBebidaRequest;
use App\Models\IngresoBebida;
use App\Models\Producto;
use App\Models\TipoProducto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class IngresoBebidaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $ingresoBebidas = IngresoBebida::query()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderByDesc('fecha_ingreso')
            ->paginate(10)
            ->withQueryString();

        return view('ingreso-bebida.index', compact('ingresoBebidas', 'search'));
    }

    public function create(): View
    {
        $ingresoBebida = new IngresoBebida();
        $ingresoBebida->nombre = $this->generarNombreLote();
        $productos      = $this->getProductosBebida();
        $productoRows   = $this->buildProductoRows();

        return view('ingreso-bebida.create', compact('ingresoBebida', 'productos', 'productoRows'));
    }

    public function store(IngresoBebidaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $nombreLote = !empty($data['nombre'])
                ? $data['nombre']
                : $this->generarNombreLote();

            $ingresoBebida = IngresoBebida::create([
                'nombre'       => $nombreLote,
                'descripcion'  => $data['descripcion'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
            ]);

            foreach ($data['productos'] as $item) {
                $ingresoBebida->productos()->attach($item['producto_id'], [
                    'cantidad_ingresa' => $item['cantidad_ingresa'],
                    'precio' => $item['precio'],
                ]);

                Producto::where('id', $item['producto_id'])
                    ->increment('cantidad_actual', (int) $item['cantidad_ingresa']);
            }
        });

        return Redirect::route('ingreso-bebidas.index')
            ->with('success', 'Ingreso de bebida registrado correctamente.');
    }

    public function show(IngresoBebida $ingresoBebida): View
    {
        $ingresoBebida->load('productos.tipoProducto');
        return view('ingreso-bebida.show', compact('ingresoBebida'));
    }

    public function edit(IngresoBebida $ingresoBebida): View
    {
        $ingresoBebida->load('productos');
        $productos    = $this->getProductosBebida();
        $productoRows = $this->buildProductoRows($ingresoBebida);

        return view('ingreso-bebida.edit', compact('ingresoBebida', 'productos', 'productoRows'));
    }

    public function update(IngresoBebidaRequest $request, IngresoBebida $ingresoBebida): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $ingresoBebida) {
            // Revert previous stock
            foreach ($ingresoBebida->productos as $producto) {
                Producto::where('id', $producto->id)
                    ->decrement('cantidad_actual', (int) $producto->pivot->cantidad_ingresa);
            }

            $ingresoBebida->update([
                'nombre'        => $data['nombre'],
                'descripcion'   => $data['descripcion'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
            ]);

            $ingresoBebida->productos()->detach();

            foreach ($data['productos'] as $item) {
                $ingresoBebida->productos()->attach($item['producto_id'], [
                    'cantidad_ingresa' => $item['cantidad_ingresa'],
                    'precio' => $item['precio'],
                ]);

                Producto::where('id', $item['producto_id'])
                    ->increment('cantidad_actual', (int) $item['cantidad_ingresa']);
            }
        });

        return Redirect::route('ingreso-bebidas.index')
            ->with('success', 'Ingreso de bebida actualizado correctamente.');
    }

    public function destroy(IngresoBebida $ingresoBebida): RedirectResponse
    {
        DB::transaction(function () use ($ingresoBebida) {
            $ingresoBebida->load('productos');

            foreach ($ingresoBebida->productos as $producto) {
                Producto::where('id', $producto->id)
                    ->decrement('cantidad_actual', (int) $producto->pivot->cantidad_ingresa);
            }

            $ingresoBebida->update(['estado' => 0]);
        });

        return Redirect::route('ingreso-bebidas.index')
            ->with('success', 'Ingreso de bebida eliminado correctamente.');
    }

    private function getProductosBebida()
    {
        $tipoBebida = TipoProducto::where('nombre', 'like', '%bebida%')
            ->where('estado', 1)
            ->first();

        return Producto::query()
            ->where('estado', 1)
            ->when($tipoBebida, fn ($q) => $q->where('tipo_producto_id', $tipoBebida->id))
            ->orderBy('nombre')
            ->get();
    }

    private function buildProductoRows(?IngresoBebida $ingresoBebida = null): array
    {
        if (!$ingresoBebida || !$ingresoBebida->exists) {
            return [['producto_id' => '', 'cantidad_ingresa' => '', 'precio' => '']];
        }

        return $ingresoBebida->productos->map(fn ($p) => [
            'producto_id'      => $p->id,
            'cantidad_ingresa' => $p->pivot->cantidad_ingresa,
            'precio' => $p->pivot->precio,
        ])->values()->all();
    }

    private function generarNombreLote(): string
    {
        $anioMes = now()->format('Y-m');
        $prefijo = 'LOTE-BEB-' . $anioMes . '-';

        $ultimoMes = IngresoBebida::where('nombre', 'like', $prefijo . '%')
            ->latest('id')
            ->first();

        $numero = 1;
        if ($ultimoMes && preg_match('/-(\d{3})$/', $ultimoMes->nombre, $matches)) {
            $numero = (int) $matches[1] + 1;
        }

        return $prefijo . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}
