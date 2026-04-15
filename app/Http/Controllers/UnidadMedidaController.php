<?php

namespace App\Http\Controllers;

use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UnidadMedidaRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UnidadMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $unidadMedidas = UnidadMedida::paginate();

        return view('unidad-medida.index', compact('unidadMedidas'))
            ->with('i', ($request->input('page', 1) - 1) * $unidadMedidas->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $unidadMedida = new UnidadMedida();

        return view('unidad-medida.create', compact('unidadMedida'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UnidadMedidaRequest $request): RedirectResponse
    {
        UnidadMedida::create($request->validated());

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'UnidadMedida created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $unidadMedida = UnidadMedida::find($id);

        return view('unidad-medida.show', compact('unidadMedida'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $unidadMedida = UnidadMedida::find($id);

        return view('unidad-medida.edit', compact('unidadMedida'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnidadMedidaRequest $request, UnidadMedida $unidadMedida): RedirectResponse
    {
        $unidadMedida->update($request->validated());

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'UnidadMedida updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        UnidadMedida::find($id)->delete();

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'UnidadMedida deleted successfully');
    }
}
