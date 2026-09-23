<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;



class ClienteController extends Controller
{
    
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $clientes = Cliente::query()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('cliente.index', compact('clientes', 'search'));
    }

    public function create(): View
    {
        $cliente = new Cliente();
        return view('cliente.create', compact('cliente'));
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        Cliente::create($request->validated());

        return Redirect::route('clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function show(Cliente $cliente): View
    {
        return view('cliente.show', compact('cliente'));
    }

    public function edit(Cliente $cliente): View
    {
        return view('cliente.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return Redirect::route('clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->update(['estado' => 0]);

        return Redirect::route('clientes.index')
            ->with('success', 'Cliente eliminado correctamente.');
    }
}
