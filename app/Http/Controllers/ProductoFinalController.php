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
        $productoFinals = ProductoFinal::paginate();

        return view('producto-final.index', compact('productoFinals'))
            ->with('i', ($request->input('page', 1) - 1) * $productoFinals->perPage());
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
            ->with('success', 'ProductoFinal created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $productoFinal = ProductoFinal::find($id);

        return view('producto-final.show', compact('productoFinal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $productoFinal = ProductoFinal::find($id);

        return view('producto-final.edit', compact('productoFinal'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ProductoFinalRequest $request, ProductoFinal $productoFinal): RedirectResponse
    {
        $productoFinal->update($request->validated());

        return Redirect::route('producto-finals.index')
            ->with('success', 'ProductoFinal updated successfully');
    }

    public function destroy($id): RedirectResponse
    {
        ProductoFinal::find($id)->delete();

        return Redirect::route('producto-finals.index')
            ->with('success', 'ProductoFinal deleted successfully');
    }
}
