<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
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
        $insumos = Insumo::paginate();

        return view('insumo.index', compact('insumos'))
            ->with('i', ($request->input('page', 1) - 1) * $insumos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $insumo = new Insumo();

        return view('insumo.create', compact('insumo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(InsumoRequest $request): RedirectResponse
    {
        Insumo::create($request->validated());

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $insumo = Insumo::find($id);

        return view('insumo.show', compact('insumo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $insumo = Insumo::find($id);

        return view('insumo.edit', compact('insumo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(InsumoRequest $request, Insumo $insumo): RedirectResponse
    {
        $insumo->update($request->validated());

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        Insumo::find($id)->delete();

        return Redirect::route('insumos.index')
            ->with('success', 'Insumo deleted successfully');
    }
}
