<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\RoleRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $roles = Role::searchAndPaginate($search, 10);

        return view('role.index', compact('roles', 'search'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $role = new Role();
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        $selectedPermissions = [];

        return view('role.create', compact('role', 'permissions', 'selectedPermissions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request): RedirectResponse
    {
        $role = Role::createFromData($request->validated());
        $role->syncPermissions($role->guard_name === 'web'
            ? $request->validated('permissions', [])
            : []);

        return Redirect::route('roles.index')
            ->with('success', 'Rol creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Role $role): View
    {
        $role->load('permissions');
        return view('role.show', compact('role'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role): View
    {
        $permissions = Permission::where('guard_name', 'web')->orderBy('name')->get();
        $selectedPermissions = $role->permissions->pluck('name')->all();

        return view('role.edit', compact('role', 'permissions', 'selectedPermissions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, Role $role): RedirectResponse
    {
        $role->updateFromData($request->validated());
        $role->syncPermissions($role->guard_name === 'web'
            ? $request->validated('permissions', [])
            : []);

        return Redirect::route('roles.index')
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        $role->deleteRecord();

        return Redirect::route('roles.index')
            ->with('success', 'Rol eliminado correctamente.');
    }
}
