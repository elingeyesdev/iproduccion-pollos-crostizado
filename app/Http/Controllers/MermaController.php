<?php

namespace App\Http\Controllers;

use App\Http\Requests\MermaRequest;
use App\Models\Merma;
use App\Models\Produccion;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MermaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $mermas = Merma::query()
            ->with(['produccion.ordenProduccion:orden_producciones.id,orden_producciones.numero_orden', 'produccion.producto', 'produccion.reglaConversion'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('cantidad', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $mermas->each(function (Merma $merma) {
            $merma->produccion?->setAttribute('producto_afectado', $this->resolverProductoDeProduccion($merma->produccion));
        });

        return view('merma.index', compact('mermas', 'search'));
    }

    public function create(): View
    {
        $merma = new Merma();
        $producciones = $this->produccionesConProducto();

        return view('merma.create', compact('merma', 'producciones'));
    }

    public function store(MermaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $produccion = Produccion::with(['producto', 'reglaConversion'])->findOrFail($data['produccion_id']);
        $producto = $this->resolverProductoDeProduccion($produccion);

        if (!$producto) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['produccion_id' => 'No se pudo determinar el producto resultante de esta producción.']);
        }

        $cantidad = (float) $data['cantidad'];

        try {
            DB::transaction(function () use ($data, $producto, $cantidad) {
                // Bloqueo pesimista: la validación y el descuento ocurren sobre la misma fila bloqueada
                $fila = Producto::whereKey($producto->id)->lockForUpdate()->firstOrFail();

                if ($cantidad > (float) $fila->cantidad_actual) {
                    throw new \RuntimeException("Stock insuficiente de \"{$fila->nombre}\" para registrar esta merma. Disponible: {$fila->cantidad_actual}.");
                }

                Merma::create($data);

                $fila->decrement('cantidad_actual', (int) round($cantidad));
            });
        } catch (\RuntimeException $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['cantidad' => $e->getMessage()]);
        }

        return Redirect::route('mermas.index')
            ->with('success', 'Merma registrada correctamente.');
    }

    public function show(Merma $merma): View
    {
        $merma->load(['produccion.ordenProduccion', 'produccion.producto', 'produccion.reglaConversion']);
        $merma->produccion?->setAttribute('producto_afectado', $this->resolverProductoDeProduccion($merma->produccion));

        return view('merma.show', compact('merma'));
    }

    public function edit(Merma $merma): View
    {
        $producciones = $this->produccionesConProducto();
        return view('merma.edit', compact('merma', 'producciones'));
    }

    public function update(MermaRequest $request, Merma $merma): RedirectResponse
    {
        $data = $request->validated();

        $productoAnterior = null;

        if ((int) $merma->estado === 1) {
            $produccionAnterior = Produccion::with(['producto', 'reglaConversion'])->find($merma->produccion_id);
            $productoAnterior = $this->resolverProductoDeProduccion($produccionAnterior);
        }

        $produccionNueva = Produccion::with(['producto', 'reglaConversion'])->findOrFail($data['produccion_id']);
        $productoNuevo = $this->resolverProductoDeProduccion($produccionNueva);

        if (!$productoNuevo) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['produccion_id' => 'No se pudo determinar el producto resultante de esta producción.']);
        }

        $cantidadNueva = (float) $data['cantidad'];

        try {
            DB::transaction(function () use ($merma, $data, $productoAnterior, $productoNuevo, $cantidadNueva) {
                // Se repone primero la merma anterior; si falla la validación, el rollback la deshace
                if ($productoAnterior) {
                    Producto::whereKey($productoAnterior->id)->lockForUpdate()->firstOrFail()
                        ->increment('cantidad_actual', (int) round((float) $merma->cantidad));
                }

                $fila = Producto::whereKey($productoNuevo->id)->lockForUpdate()->firstOrFail();

                if ($cantidadNueva > (float) $fila->cantidad_actual) {
                    throw new \RuntimeException("Stock insuficiente de \"{$fila->nombre}\" para esta merma. Disponible: " . number_format((float) $fila->cantidad_actual, 2) . '.');
                }

                $merma->update($data);

                $fila->decrement('cantidad_actual', (int) round($cantidadNueva));
            });
        } catch (\RuntimeException $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['cantidad' => $e->getMessage()]);
        }

        return Redirect::route('mermas.index')
            ->with('success', 'Merma actualizada correctamente.');
    }

    public function destroy(Merma $merma): RedirectResponse
    {
        DB::transaction(function () use ($merma) {
            if ((int) $merma->estado === 1) {
                $produccion = Produccion::with(['producto', 'reglaConversion'])->find($merma->produccion_id);
                $producto = $this->resolverProductoDeProduccion($produccion);

                if ($producto) {
                    Producto::whereKey($producto->id)->increment('cantidad_actual', (int) round((float) $merma->cantidad));
                }
            }

            $merma->update(['estado' => 0]);
        });

        return Redirect::route('mermas.index')
            ->with('success', 'Merma eliminada correctamente.');
    }

    private function produccionesConProducto()
    {
        return Produccion::with(['producto', 'reglaConversion', 'ordenProduccion:orden_producciones.id,orden_producciones.numero_orden'])
            ->where('estado', 1)
            ->orderByDesc('created_at')
            ->get()
            ->each(function (Produccion $produccion) {
                $produccion->setAttribute('producto_afectado', $this->resolverProductoDeProduccion($produccion));
            });
    }

    private function resolverProductoDeProduccion(?Produccion $produccion): ?Producto
    {
        if (!$produccion) {
            return null;
        }

        return $produccion->producto ?? $produccion->reglaConversion?->resolverProducto();
    }
}
