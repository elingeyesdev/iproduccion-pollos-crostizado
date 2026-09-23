<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MermaRequest;
use App\Models\Merma;

class MermaController extends Controller
{
    public function index()
    {
        return response()->json(
            Merma::with('produccion')->where('estado', 1)->get()
        );
    }

    public function store(MermaRequest $request)
    {
        $merma = Merma::create($request->validated() + ['estado' => 1]);

        return response()->json($merma, 201);
    }

    public function show(string $id)
    {
        $merma = Merma::with('produccion')->where('estado', 1)->findOrFail($id);

        return response()->json($merma);
    }

    public function update(MermaRequest $request, string $id)
    {
        $merma = Merma::where('estado', 1)->findOrFail($id);
        $merma->update($request->validated());

        return response()->json($merma);
    }

    public function destroy(string $id)
    {
        $merma = Merma::where('estado', 1)->findOrFail($id);
        $merma->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
