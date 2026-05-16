<?php

namespace App\Models;

use Illuminate\Pagination\LengthAwarePaginator;
use Spatie\Permission\Models\Role as SpatieRole;

/**
 * Class Role
 *
 * @property $id
 * @property $name
 * @property $guard_name
 * @property $created_at
 * @property $updated_at
 *
 * @property ModelHasRole[] $modelHasRoles
 * @property RoleHasPermission[] $roleHasPermissions
 * @property ModelHasRole[] $modelHasRoles
 * @property RoleHasPermission[] $roleHasPermissions
 * @property ModelHasRole[] $modelHasRoles
 * @property RoleHasPermission[] $roleHasPermissions
 * @property ModelHasRole[] $modelHasRoles
 * @property RoleHasPermission[] $roleHasPermissions
 * @package 
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Role extends SpatieRole
{
    protected $perPage = 20;

    public static function searchAndPaginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('guard_name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function createFromData(array $data): self
    {
        return self::create($data);
    }

    public function updateFromData(array $data): void
    {
        $this->update($data);
    }

    public function deleteRecord(): void
    {
        $this->delete();
    }
}
