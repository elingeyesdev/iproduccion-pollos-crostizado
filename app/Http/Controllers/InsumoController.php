<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsumoRequest;
use App\Models\Insumo;
use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class InsumoController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $insumos = Insumo::query()
            ->with(['unidadMedida:id,nombre,abreviatura'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhereHas('unidadMedida', function ($q) use ($search) {
                        $q->where('nombre', 'like', "%{$search}%");
                    });
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('insumo.index', compact('insumos', 'search'));
    }

    public function create(): View
    {
        $insumo = new Insumo();
        $unidades = UnidadMedida::where('estado', 1)->orderBy('nombre')->get();

        return view('insumo.create', compact('insumo', 'unidades'));
    }

    public function store(InsumoRequest $request): RedirectResponse
    {
        Insumo::create($request->validated());

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo creado correctamente.');
    }

    public function show(Insumo $insumo): View
    {
        $insumo->load(['unidadMedida:id,nombre,abreviatura']);
        return view('insumo.show', compact('insumo'));
    }

    public function edit(Insumo $insumo): View
    {
        $unidades = UnidadMedida::where('estado', 1)->orderBy('nombre')->get();
        return view('insumo.edit', compact('insumo', 'unidades'));
    }

    public function update(InsumoRequest $request, Insumo $insumo): RedirectResponse
    {
        $insumo->update($request->validated());

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo actualizado correctamente.');
    }

    public function destroy(Insumo $insumo): RedirectResponse
    {
        $insumo->update(['estado' => 0]);

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo eliminado correctamente.');
    }
}
