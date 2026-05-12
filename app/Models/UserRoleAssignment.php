<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserRoleAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'role_id',
        'assigned_by',
        'previous_roles',
        'resulting_roles',
    ];

    protected $casts = [
        'previous_roles' => 'array',
        'resulting_roles' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public static function searchAndPaginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return self::with(['user', 'role', 'assignedBy'])
            ->when($search, function ($query) use ($search) {
                $query->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('role', function ($roleQuery) use ($search) {
                    $roleQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function assignRoleWithAudit(int $userId, int $roleId, ?int $assignedBy): self
    {
        return DB::transaction(function () use ($userId, $roleId, $assignedBy) {
            $user = User::findOrFail($userId);
            $role = Role::findOrFail($roleId);

            $rolesBefore = $user->getRoleNames()->values()->all();
            $user->assignRole($role->name);
            $rolesAfter = $user->fresh()->getRoleNames()->values()->all();

            return self::create([
                'user_id' => $user->id,
                'role_id' => $role->id,
                'assigned_by' => $assignedBy,
                'previous_roles' => $rolesBefore,
                'resulting_roles' => $rolesAfter,
            ]);
        });
    }
}