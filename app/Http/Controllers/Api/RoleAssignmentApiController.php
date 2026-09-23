<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRoleAssignmentRequest;
use App\Models\UserRoleAssignment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleAssignmentApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search'));

        $assignments = UserRoleAssignment::searchAndPaginate($search, 15);

        return response()->json($assignments);
    }

    public function store(UserRoleAssignmentRequest $request): JsonResponse
    {
        $data = $request->validated();

        $assignment = UserRoleAssignment::assignRoleWithAudit(
            (int) $data['user_id'],
            (int) $data['role_id'],
            Auth::id()
        );

        return response()->json(
            $assignment->load(['user', 'role', 'assignedBy']),
            201
        );
    }

    public function show(UserRoleAssignment $assignment): JsonResponse
    {
        $assignment->load(['user', 'role', 'assignedBy']);

        return response()->json($assignment);
    }
}
