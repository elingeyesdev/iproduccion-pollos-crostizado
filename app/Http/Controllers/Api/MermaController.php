<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Merma;
use Illuminate\Http\Request;

class MermaController extends Controller
{
    public function index()
    {
        return response()->json([
            Merma::where('estado', 1)->get()
        ]);
    }

    public function store(Request $request)
    {
    $merma = Merma::create($request->all());

        return response()->json($merma, 201);
    }

    public function show(string $id)
    {
        $merma = Merma::where('estado', 1)->findOrFail($id);

        return response()->json($merma);
    }

    public function update(Request $request, string $id)
    {
        $merma = Merma::where('estado', 1)->findOrFail($id);
        $merma->update($request->all());

        return response()->json($merma);
    }

    public function destroy(string $id)
    {
        $merma = Merma::where('estado', 1)->findOrFail($id);
        $merma->update(['estado' => 0]);

        return response()->json(null, 204);
    }
}
