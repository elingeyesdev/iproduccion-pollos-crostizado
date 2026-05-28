<?php

namespace App\Http\Controllers;

use App\Http\Requests\MermaRequest;
use App\Models\Merma;
use App\Models\Produccion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class MermaController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $mermas = Merma::query()
            ->with(['produccion.ordenProduccion:id,numero_orden'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('cantidad', 'like', "%{$search}%");
            })
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        return view('merma.index', compact('mermas', 'search'));
    }

    public function create(): View
    {
        $merma = new Merma();
        $producciones = Produccion::where('estado', 1)->orderByDesc('created_at')->get();

        return view('merma.create', compact('merma', 'producciones'));
    }

    public function store(MermaRequest $request): RedirectResponse
    {
        Merma::create($request->validated());

        return Redirect::route('mermas.index')
            ->with('success', 'Merma registrada correctamente.');
    }

    public function show(Merma $merma): View
    {
        $merma->load('produccion.ordenProduccion');
        return view('merma.show', compact('merma'));
    }

    public function edit(Merma $merma): View
    {
        $producciones = Produccion::where('estado', 1)->orderByDesc('created_at')->get();
        return view('merma.edit', compact('merma', 'producciones'));
    }

    public function update(MermaRequest $request, Merma $merma): RedirectResponse
    {
        $merma->update($request->validated());

        return Redirect::route('mermas.index')
            ->with('success', 'Merma actualizada correctamente.');
    }

    public function destroy(Merma $merma): RedirectResponse
    {
        $merma->update(['estado' => 0]);

        return Redirect::route('mermas.index')
            ->with('success', 'Merma eliminada correctamente.');
    }
}
