<?php

namespace App\Models;

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
    
}
