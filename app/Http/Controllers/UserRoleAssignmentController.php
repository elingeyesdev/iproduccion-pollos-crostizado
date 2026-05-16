<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRoleAssignmentRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRoleAssignment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserRoleAssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $assignments = UserRoleAssignment::searchAndPaginate($search, 10);

        return view('user-role-assignments.index', compact('assignments', 'search'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('user-role-assignments.create', compact('users', 'roles'));
    }

    public function store(UserRoleAssignmentRequest $request): RedirectResponse
    {
        $data = $request->validated();

        UserRoleAssignment::assignRoleWithAudit(
            (int) $data['user_id'],
            (int) $data['role_id'],
            Auth::id()
        );

        return redirect()->route('role-assignments.index')
            ->with('success', 'Rol asignado y registrado en el historial correctamente.');
    }

    public function show(UserRoleAssignment $assignment): View
    {
        $assignment->load(['user', 'role', 'assignedBy']);

        return view('user-role-assignments.show', compact('assignment'));
    }
}