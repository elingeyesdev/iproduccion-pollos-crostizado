<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Acciones CRUD standard por cada módulo.
     */
    private const ACCIONES = [
        'buscar'   => 'Listar / buscar registros',
        'ver'      => 'Ver detalle de un registro',
        'crear'    => 'Crear nuevos registros',
        'editar'   => 'Editar registros existentes',
        'eliminar' => 'Eliminar registros',
    ];

    /**
     * Definición de módulos del sistema.
     * 'key'   => nombre base del permiso (coincide con el resource/route name)
     * 'label' => nombre humano del módulo
     */
    private function modulos(): array
    {
        return [
            // Módulo de Acceso y Seguridad
            ['key' => 'users',              'label' => 'Administrar Usuarios'],
            ['key' => 'roles',              'label' => 'Administrar Roles'],
            ['key' => 'role-assignments',   'label' => 'Asignar Rol a Usuario'],

            // Módulo de Parametrización
            ['key' => 'unidad-medidas',     'label' => 'Unidades de Medida'],
            ['key' => 'tipo-productos',     'label' => 'Tipos de Producto'],
            ['key' => 'productos',          'label' => 'Productos'],
            ['key' => 'productos-composicion', 'label' => 'Composición de Productos'],
            ['key' => 'regla-conversiones', 'label' => 'Reglas de Conversión'],
            ['key' => 'insumos',            'label' => 'Insumos'],
            ['key' => 'clientes',           'label' => 'Clientes'],

            // Módulo Transaccionales
            ['key' => 'ingreso-insumos',    'label' => 'Ingreso de Insumos'],
            ['key' => 'ingreso-bebidas',    'label' => 'Ingreso de Bebidas'],
            ['key' => 'orden-producciones', 'label' => 'Órdenes de Producción'],
            ['key' => 'producciones',       'label' => 'Producciones'],
            ['key' => 'mermas',             'label' => 'Mermas'],
            ['key' => 'ventas',             'label' => 'Ventas'],

            // Reportes (solo ver/buscar, pero se crean las 5 acciones por consistencia)
            ['key' => 'reportes',           'label' => 'Reportes'],
        ];
    }

    /**
     * Permisos que tendrá el ROL VENTA (solo módulos que le corresponden).
     */
    private function permisosRolVenta(): array
    {
        $modulosVenta = [
            'clientes',
            'ventas',
            'productos',
            'reportes',
        ];

        return $this->armarPermisos($modulosVenta, ['buscar', 'ver', 'crear', 'editar']);
    }

    /**
     * Permisos que tendrá el ROL COCINA.
     */
    private function permisosRolCocina(): array
    {
        $modulosCocina = [
            'insumos',
            'productos',
            'productos-composicion',
            'ingreso-insumos',
            'ingreso-bebidas',
            'orden-producciones',
            'producciones',
            'mermas',
            'unidad-medidas',
            'tipo-productos',
            'regla-conversiones',
            'clientes',
            'ventas',
            'reportes',
        ];

        return $this->armarPermisos($modulosCocina, ['buscar', 'ver', 'crear', 'editar', 'eliminar']);
    }

    /**
     * Helper: arma los nombres de permisos "modulo.accion".
     */
    private function armarPermisos(array $modulos, array $acciones): array
    {
        $permisos = [];
        foreach ($modulos as $m) {
            foreach ($acciones as $a) {
                $permisos[] = "{$m}.{$a}";
            }
        }
        return $permisos;
    }

    public function run(): void
    {
        // 1) Crear TODOS los permisos en BD
        foreach ($this->modulos() as $modulo) {
            foreach (self::ACCIONES as $accion => $descripcionAccion) {
                $nombre = "{$modulo['key']}.{$accion}";

                Permission::firstOrCreate(
                    ['name' => $nombre, 'guard_name' => 'web'],
                    ['name' => $nombre, 'guard_name' => 'web']
                );
            }
        }

        // 2) Obtener roles (creados por RoleSeeder)
        $rolAdmin    = Role::where('name', 'administrador')->first();
        $rolVenta    = Role::where('name', 'venta')->first();
        $rolCocina   = Role::where('name', 'cocina')->first();

        // 3) ADMINISTRADOR => TODOS los permisos
        if ($rolAdmin) {
            $todosLosPermisos = Permission::where('guard_name', 'web')
                ->whereIn('name', $this->armarPermisos(
                    collect($this->modulos())->pluck('key')->toArray(),
                    array_keys(self::ACCIONES)
                ))
                ->pluck('name')
                ->toArray();

            $rolAdmin->syncPermissions($todosLosPermisos);
        }

        // 4) ROL VENTA => solo sus módulos
        if ($rolVenta) {
            $rolVenta->syncPermissions($this->permisosRolVenta());
        }

        // 5) ROL COCINA => módulos de producción/operación
        if ($rolCocina) {
            $rolCocina->syncPermissions($this->permisosRolCocina());
        }
    }
}
