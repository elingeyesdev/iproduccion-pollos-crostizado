<?php

namespace App\Http\Controllers;

use App\Http\Requests\DetalleIngresoBebidaRequest;
use App\Models\DetalleIngresoBebida;
use App\Models\IngresoBebida;
use App\Models\Producto;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class DetalleIngresoBebidaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $detalleIngresoBebidas = DetalleIngresoBebida::query()
            ->with(['ingresoBebida:id,nombre,fecha_ingreso', 'producto:id,nombre'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('producto', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                })->orWhereHas('ingresoBebida', function ($q) use ($search) {
                    $q->where('nombre', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('detalle-ingreso-bebida.index', compact('detalleIngresoBebidas', 'search'));
    }

    public function create(): View
    {
        $detalleIngresoBebida = new DetalleIngresoBebida();
        $ingresoBebidas = IngresoBebida::where('estado', 1)->orderByDesc('fecha_ingreso')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        return view('detalle-ingreso-bebida.create', compact('detalleIngresoBebida', 'ingresoBebidas', 'productos'));
    }

    public function store(DetalleIngresoBebidaRequest $request): RedirectResponse
    {
        DetalleIngresoBebida::create($request->validated());

        return Redirect::route('detalle-ingreso-bebidas.index')
            ->with('success', 'Detalle de ingreso de bebida creado correctamente.');
    }

    public function show(DetalleIngresoBebida $detalleIngresoBebida): View
    {
        $detalleIngresoBebida->load(['ingresoBebida', 'producto']);

        return view('detalle-ingreso-bebida.show', compact('detalleIngresoBebida'));
    }

    public function edit(DetalleIngresoBebida $detalleIngresoBebida): View
    {
        $ingresoBebidas = IngresoBebida::where('estado', 1)->orderByDesc('fecha_ingreso')->get();
        $productos = Producto::where('estado', 1)->orderBy('nombre')->get();

        return view('detalle-ingreso-bebida.edit', compact('detalleIngresoBebida', 'ingresoBebidas', 'productos'));
    }

    public function update(DetalleIngresoBebidaRequest $request, DetalleIngresoBebida $detalleIngresoBebida): RedirectResponse
    {
        $detalleIngresoBebida->update($request->validated());

        return Redirect::route('detalle-ingreso-bebidas.index')
            ->with('success', 'Detalle de ingreso de bebida actualizado correctamente.');
    }

    public function destroy(DetalleIngresoBebida $detalleIngresoBebida): RedirectResponse
    {
        $detalleIngresoBebida->update(['estado' => 0]);

        return Redirect::route('detalle-ingreso-bebidas.index')
            ->with('success', 'Detalle de ingreso de bebida eliminado correctamente.');
    }
}
