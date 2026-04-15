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
        $categoriaInsumos = CategoriaInsumo::paginate();

        return view('categoria-insumo.index', compact('categoriaInsumos'))
            ->with('i', ($request->input('page', 1) - 1) * $categoriaInsumos->perPage());
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
        CategoriaInsumo::create($request->validated());

        return Redirect::route('categoria-insumos.index')
            ->with('success', 'CategoriaInsumo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $categoriaInsumo = CategoriaInsumo::find($id);

        return view('categoria-insumo.show', compact('categoriaInsumo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $categoriaInsumo = CategoriaInsumo::find($id);

        return view('categoria-insumo.edit', compact('categoriaInsumo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoriaInsumoRequest $request, CategoriaInsumo $categoriaInsumo): RedirectResponse
    {
        $categoriaInsumo->update($request->validated());

        return Redirect::route('categoria-insumos.index')
            ->with('success', 'CategoriaInsumo updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        CategoriaInsumo::find($id)->delete();

        return Redirect::route('categoria-insumos.index')
            ->with('success', 'CategoriaInsumo deleted successfully');
    }
}
