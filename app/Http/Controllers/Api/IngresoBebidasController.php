<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IngresoBebida;
use Illuminate\Http\Request;

class IngresoBebidasController extends Controller
{
    public function index()
    {
        return response()->json([IngresoBebida::where('estado',1)
            ->get()]);
    }

    public function store(Request $request)
    {
        $ingresoBebida = IngresoBebida::create($request->all());

        return response()->json($ingresoBebida, 201);
    }

    public function show(string $id)
    {
        $ingresoBebida = IngresoBebida::where('estado',1)->findOrFail($id);

        return response()->json($ingresoBebida);
    }

    public function update(Request $request, string $id)
    {
       $ingresoBebida = IngresoBebida::where('estado',1)->findOrFail($id);
        $ingresoBebida->update($request->all());

        return response()->json($ingresoBebida);
    
    }

    public function destroy(string $id)
    {
        $ingresoBebida = IngresoBebida::where('estado',1)->findOrFail($id);
        $ingresoBebida->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
