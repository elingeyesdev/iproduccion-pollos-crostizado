<?php

namespace App\Http\Controllers;

use App\Models\CategoriaInsumo;
use App\Models\Insumo;
use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\InsumoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class InsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $insumos = Insumo::searchAndPaginate($search, 10);

        return view('insumo.index', compact('insumos', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $insumo = new Insumo();
        $categorias = CategoriaInsumo::orderBy('nombre')->pluck('nombre', 'id');
        $unidades = UnidadMedida::orderBy('nombre')->get();

        return view('insumo.create', compact('insumo', 'categorias', 'unidades'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InsumoRequest $request): RedirectResponse
    {
        Insumo::createFromData($request->validated());

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Insumo $insumo): View
    {
        $insumo->load(['categoriaInsumo:id,nombre', 'unidadMedida:id,nombre,abreviatura']);

        return view('insumo.show', compact('insumo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Insumo $insumo): View
    {
        $categorias = CategoriaInsumo::orderBy('nombre')->pluck('nombre', 'id');
        $unidades = UnidadMedida::orderBy('nombre')->get();

        return view('insumo.edit', compact('insumo', 'categorias', 'unidades'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InsumoRequest $request, Insumo $insumo): RedirectResponse
    {
        $insumo->updateFromData($request->validated());

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo actualizado correctamente.');
    }

    public function destroy(Insumo $insumo): RedirectResponse
    {
        $insumo->deleteRecord();

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo eliminado correctamente.');
    }
}
