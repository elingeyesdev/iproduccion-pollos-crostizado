<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class KpiScreensTest extends TestCase
{
    use RefreshDatabase;

    public function test_each_kpi_area_has_its_own_screen(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo(Permission::create([
            'name' => 'reportes.buscar',
            'guard_name' => 'web',
        ]));

        $this->actingAs($user)
            ->get(route('kpi.ventas'))
            ->assertOk()
            ->assertSee('KPI de Administración de Ventas');

        $this->get(route('kpi.finanzas'))
            ->assertOk()
            ->assertSee('KPI de Administración Financiera y Rentabilidad');

        $this->get(route('kpi.produccion'))
            ->assertOk()
            ->assertSee('KPI de Control de Producción');
    }
}
