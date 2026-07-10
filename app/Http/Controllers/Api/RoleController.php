<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = \Spatie\Permission\Models\Role::where('estado', 1)->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
        ]);

        $role = \Spatie\Permission\Models\Role::create(['name' => $request->name]);

        return response()->json($role, 201);
    }
    public function show(string $id){
        $role = \Spatie\Permission\Models\Role::where('estado', 1)->findOrFail($id);
        return response()->json($role);
    }
    public function update(Request $request, string $id){
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
        ]);

        $role = \Spatie\Permission\Models\Role::where('estado', 1)->findOrFail($id);
        $role->update(['name' => $request->name]);

        return response()->json($role);
    }
    public function destroy(string $id){    
        $role = \Spatie\Permission\Models\Role::where('estado', 1)->findOrFail($id);
        $role->update(['estado' => 0]);

        return response()->json([
            'message' => 'Rol eliminado',
        ]);
    }
}
