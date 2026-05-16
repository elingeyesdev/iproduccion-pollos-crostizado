<?php

namespace App\Http\Controllers;

use App\Models\CategoriaInsumo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\CategoriaInsumoRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class CategoriaInsumoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $categoriaInsumos = CategoriaInsumo::searchAndPaginate($search, 10);

        return view('categoria-insumo.index', compact('categoriaInsumos', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categoriaInsumo = new CategoriaInsumo();

        return view('categoria-insumo.create', compact('categoriaInsumo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoriaInsumoRequest $request): RedirectResponse
    {
        CategoriaInsumo::createFromData($request->validated());

        return Redirect::route('categoria-insumos.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(CategoriaInsumo $categoriaInsumo): View
    {
        return view('categoria-insumo.show', compact('categoriaInsumo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CategoriaInsumo $categoriaInsumo): View
    {
        return view('categoria-insumo.edit', compact('categoriaInsumo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoriaInsumoRequest $request, CategoriaInsumo $categoriaInsumo): RedirectResponse
    {
        $categoriaInsumo->updateFromData($request->validated());

        return Redirect::route('categoria-insumos.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(CategoriaInsumo $categoriaInsumo): RedirectResponse
    {
        $categoriaInsumo->deleteRecord();

        return Redirect::route('categoria-insumos.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
