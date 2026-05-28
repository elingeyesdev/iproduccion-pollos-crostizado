<?php

namespace App\Http\Controllers;

use App\Http\Requests\UnidadMedidaRequest;
use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class UnidadMedidaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $unidadMedidas = UnidadMedida::query()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('abreviatura', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('unidad-medida.index', compact('unidadMedidas', 'search'));
    }

    public function create(): View
    {
        $unidadMedida = new UnidadMedida();
        return view('unidad-medida.create', compact('unidadMedida'));
    }

    public function store(UnidadMedidaRequest $request): RedirectResponse
    {
        UnidadMedida::create($request->validated());

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'Unidad de medida creada correctamente.');
    }

    public function show(UnidadMedida $unidadMedida): View
    {
        return view('unidad-medida.show', compact('unidadMedida'));
    }

    public function edit(UnidadMedida $unidadMedida): View
    {
        return view('unidad-medida.edit', compact('unidadMedida'));
    }

    public function update(UnidadMedidaRequest $request, UnidadMedida $unidadMedida): RedirectResponse
    {
        $unidadMedida->update($request->validated());

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'Unidad de medida actualizada correctamente.');
    }

    public function destroy(UnidadMedida $unidadMedida): RedirectResponse
    {
        $unidadMedida->update(['estado' => 0]);

        return Redirect::route('unidad-medidas.index')
            ->with('success', 'Unidad de medida eliminada correctamente.');
    }
}
