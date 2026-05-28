<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetalleVentaRequest;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DetalleVentaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $detalleVentas = DetalleVenta::query()
            ->with(['venta:id,fecha_venta', 'producto:id,nombre'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('producto', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('detalle-venta.index', compact('detalleVentas', 'search'));
    }

    public function create(): View
    {
        $detalleVenta = new DetalleVenta();
        $ventas = Venta::where('estado', 1)->orderByDesc('fecha_venta')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        return view('detalle-venta.create', compact('detalleVenta', 'ventas', 'productos'));
    }

    public function store(DetalleVentaRequest $request): RedirectResponse
    {
        DetalleVenta::create($request->validated());

        return Redirect::route('detalle-ventas.index')
            ->with('success', 'Detalle de venta creado correctamente.');
    }

    public function show(DetalleVenta $detalleVenta): View
    {
        $detalleVenta->load(['venta', 'producto']);

        return view('detalle-venta.show', compact('detalleVenta'));
    }

    public function edit(DetalleVenta $detalleVenta): View
    {
        $ventas = Venta::where('estado', 1)->orderByDesc('fecha_venta')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        return view('detalle-venta.edit', compact('detalleVenta', 'ventas', 'productos'));
    }

    public function update(DetalleVentaRequest $request, DetalleVenta $detalleVenta): RedirectResponse
    {
        $detalleVenta->update($request->validated());

        return Redirect::route('detalle-ventas.index')
            ->with('success', 'Detalle de venta actualizado correctamente.');
    }

    public function destroy(DetalleVenta $detalleVenta): RedirectResponse
    {
        $detalleVenta->update(['estado' => 0]);

        return Redirect::route('detalle-ventas.index')
            ->with('success', 'Detalle de venta eliminado correctamente.');
    }
}
