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
        $search = trim((string) $request->input('search'));

        $unidadMedidas = UnidadMedida::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('abreviatura', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('unidad-medida.index', compact('unidadMedidas', 'search'));
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
            ->with('success', 'Unidad de medida creada correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(UnidadMedida $unidadMedida): View
    {
        return view('unidad-medida.show', compact('unidadMedida'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(UnidadMedida $unidadMedida): View
    {
        return view('unidad-medida.edit', compact('unidadMedida'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UnidadMedidaRequest $request, UnidadMedida $unidadMedida): RedirectResponse
    {
        $unidadMedida->update($request->validated());

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnidadMedida $unidadMedida): RedirectResponse
    {
        $unidadMedida->delete();

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'Unidad de medida eliminada correctamente.');
    }
}
