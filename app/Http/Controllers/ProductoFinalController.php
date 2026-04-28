<?php

namespace App\Http\Controllers;

use App\Models\ProductoFinal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\ProductoFinalRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProductoFinalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $productoFinals = ProductoFinal::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('producto-final.index', compact('productoFinals', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $productoFinal = new ProductoFinal();

        return view('producto-final.create', compact('productoFinal'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ProductoFinalRequest $request): RedirectResponse
    {
        ProductoFinal::create($request->validated());

        return Redirect::route('producto-finals.index')
            ->with('success', 'Producto final creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductoFinal $productoFinal): View
    {
        return view('producto-final.show', compact('productoFinal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProductoFinal $productoFinal): View
    {
        return view('producto-final.edit', compact('productoFinal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoFinalRequest $request, ProductoFinal $productoFinal): RedirectResponse
    {
        $productoFinal->update($request->validated());

        return Redirect::route('producto-finals.index')
            ->with('success', 'Producto final actualizado correctamente.');
    }

    public function destroy(ProductoFinal $productoFinal): RedirectResponse
    {
        $productoFinal->delete();

        return Redirect::route('producto-finals.index')
            ->with('success', 'Producto final eliminado correctamente.');
    }
}
