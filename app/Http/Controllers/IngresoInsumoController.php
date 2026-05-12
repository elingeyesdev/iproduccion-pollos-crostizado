<?php

namespace App\Http\Controllers;

use App\Http\Requests\IngresoInsumoRequest;
use App\Models\IngresoInsumo;
use App\Models\Insumo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IngresoInsumoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $ingresos = IngresoInsumo::searchAndPaginate($search, 10);

        return view('ingreso-insumo.index', compact('ingresos', 'search'));
    }

    public function create(): View
    {
        $ingreso = new IngresoInsumo();
        $insumos = Insumo::orderBy('nombre')->get();

        return view('ingreso-insumo.create', compact('ingreso', 'insumos'));
    }

    public function store(IngresoInsumoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        IngresoInsumo::createWithDetalles($data);

        return redirect()->route('ingreso-insumos.index')
            ->with('success', 'Ingreso de insumos registrado correctamente.');
    }

    public function show(IngresoInsumo $ingresoInsumo): View
    {
        $ingresoInsumo->load('detalles.insumo');

        return view('ingreso-insumo.show', ['ingreso' => $ingresoInsumo]);
    }

    public function edit(IngresoInsumo $ingresoInsumo): View
    {
        $ingresoInsumo->load('detalles.insumo');
        $insumos = Insumo::orderBy('nombre')->get();

        return view('ingreso-insumo.edit', ['ingreso' => $ingresoInsumo, 'insumos' => $insumos]);
    }

    public function update(IngresoInsumoRequest $request, IngresoInsumo $ingresoInsumo): RedirectResponse
    {
        $data = $request->validated();

        $ingresoInsumo->updateWithDetalles($data);

        return redirect()->route('ingreso-insumos.index')
            ->with('success', 'Ingreso de insumos actualizado correctamente.');
    }

    public function destroy(IngresoInsumo $ingresoInsumo): RedirectResponse
    {
        $ingresoInsumo->deleteWithStockRollback();

        return redirect()->route('ingreso-insumos.index')
            ->with('success', 'Ingreso de insumos eliminado correctamente.');
    }
}
