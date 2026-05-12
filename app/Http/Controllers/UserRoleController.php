<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRoleRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserRoleController extends Controller
{
    public function edit(User $user): View
    {
        $roles = User::roleOptions();
        $selectedRoles = old('roles', $user->selectedRoleNames());

        return view('users.roles', compact('user', 'roles', 'selectedRoles'));
    }

    public function update(UserRoleRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();
        $user->syncAssignedRoles($data['roles']);

        return redirect()->route('users.index')
            ->with('success', 'Roles actualizados correctamente.');
    }
}
