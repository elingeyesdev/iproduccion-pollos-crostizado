<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRoutePermission
{
    public function handle(Request $request, Closure $next): Response
    {
        $routeName = (string) $request->route()?->getName();
        $permission = self::permissionForRoute($routeName);

        if ($permission === null || $request->user()?->can($permission)) {
            return $next($request);
        }

        abort(403, 'No tienes permiso para realizar esta acción.');
    }

    public static function permissionForRoute(string $routeName): ?string
    {
        if (str_starts_with($routeName, 'reportes.')) {
            return 'reportes.buscar';
        }

        if (str_starts_with($routeName, 'producciones.lote.')) {
            return 'producciones.crear';
        }

        $parts = explode('.', $routeName);
        $action = array_pop($parts);
        $module = implode('.', $parts);

        if ($module === 'users.roles') {
            return 'role-assignments.editar';
        }

        if ($module === '' || in_array($module, ['profile'], true)) {
            return null;
        }

        $actions = [
            'index' => 'buscar',
            'show' => 'ver',
            'pdf' => 'ver',
            'create' => 'crear',
            'store' => 'crear',
            'edit' => 'editar',
            'update' => 'editar',
            'destroy' => 'eliminar',
        ];

        return isset($actions[$action]) ? "{$module}.{$actions[$action]}" : null;
    }
}