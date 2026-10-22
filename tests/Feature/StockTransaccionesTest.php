<?php

namespace Tests\Feature;

use App\Models\Cliente;
use App\Models\DetalleVenta;
use App\Models\IngresoInsumo;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\TipoProducto;
use App\Models\UnidadMedida;
use App\Models\Venta;
use App\Models\VentaPago;
use App\Services\StockService;
use App\Services\VentaService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class StockTransaccionesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // La tabla de combos no tiene migración en el repositorio
        if (!Schema::hasTable('composicion_productos')) {
            Schema::create('composicion_productos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('producto_padre_id');
                $table->foreignId('producto_hijo_id');
                $table->decimal('cantidad', 12, 2);
                $table->integer('estado')->default(1);
                $table->timestamps();
            });
        }
    }

    private function producto(string $nombre, int $stock, float $precio = 10): Producto
    {
        $tipo = TipoProducto::firstOrCreate(['nombre' => 'General']);

        return Producto::create([
            'nombre' => $nombre,
            'precio' => $precio,
            'tipo_producto_id' => $tipo->id,
            'cantidad_actual' => $stock,
            'estado' => 1,
        ]);
    }

    private function venta(array $lineas): Venta
    {
        $cliente = Cliente::create(['nombre' => 'Cliente', 'telefono' => '000']);
        $venta = Venta::create(['cliente_id' => $cliente->id, 'fecha_venta' => now()->toDateString(), 'total' => 0, 'estado' => 1]);

        foreach ($lineas as [$producto, $cantidad]) {
            DetalleVenta::create([
                'venta_id' => $venta->id, 'producto_id' => $producto->id,
                'precio_unitario' => $producto->precio, 'cantidad' => $cantidad,
                'sub_total' => $producto->precio * $cantidad, 'estado' => 1,
            ]);
            DB::transaction(fn () => app(StockService::class)->moverProducto($producto, -$cantidad));
        }

        VentaPago::create(['venta_id' => $venta->id, 'metodo_pago' => 'Efectivo', 'monto' => 10, 'estado' => 1]);

        return $venta;
    }

    private function insumo(string $nombre, float $stock): Insumo
    {
        $unidad = UnidadMedida::firstOrCreate(['nombre' => 'Kilogramo', 'abreviatura' => 'kg']);

        return Insumo::create(['nombre' => $nombre, 'unidad_medida_id' => $unidad->id, 'stock_actual' => $stock, 'estado' => 1]);
    }

    public function test_anular_venta_repone_stock_y_desactiva_detalles_y_pagos(): void
    {
        $gaseosa = $this->producto('Gaseosa', 10);
        $venta = $this->venta([[$gaseosa, 3]]);
        $this->assertSame(7, $gaseosa->fresh()->cantidad_actual);

        app(VentaService::class)->anular($venta);

        $this->assertSame(10, $gaseosa->fresh()->cantidad_actual);
        $this->assertSame(0, (int) $venta->fresh()->estado);
        $this->assertSame(0, $venta->detalles()->where('estado', 1)->count());
        $this->assertSame(0, $venta->pagos()->where('estado', 1)->count());
    }

    public function test_anular_venta_de_combo_repone_sus_componentes(): void
    {
        $pollo = $this->producto('Presa de pollo', 20);
        $papas = $this->producto('Papas', 20);
        $combo = $this->producto('Combo familiar', 0, 80);
        $combo->ingredientes()->attach([$pollo->id => ['cantidad' => 4, 'estado' => 1], $papas->id => ['cantidad' => 2, 'estado' => 1]]);

        $venta = $this->venta([[$combo, 2]]);
        $this->assertSame(12, $pollo->fresh()->cantidad_actual);
        $this->assertSame(16, $papas->fresh()->cantidad_actual);

        app(VentaService::class)->anular($venta);

        $this->assertSame(20, $pollo->fresh()->cantidad_actual);
        $this->assertSame(20, $papas->fresh()->cantidad_actual);
        $this->assertSame(0, $combo->fresh()->cantidad_actual);
    }

    public function test_no_se_puede_anular_una_venta_dos_veces(): void
    {
        $gaseosa = $this->producto('Gaseosa', 10);
        $venta = $this->venta([[$gaseosa, 3]]);
        app(VentaService::class)->anular($venta);

        try {
            app(VentaService::class)->anular($venta);
            $this->fail('Se esperaba una excepción al anular dos veces.');
        } catch (\RuntimeException $e) {
            $this->assertStringContainsString('ya está anulada', $e->getMessage());
        }

        $this->assertSame(10, $gaseosa->fresh()->cantidad_actual);
    }

    public function test_descontar_sin_stock_lanza_excepcion_y_no_modifica(): void
    {
        $gaseosa = $this->producto('Gaseosa', 2);

        $this->expectException(\RuntimeException::class);

        try {
            DB::transaction(fn () => app(StockService::class)->moverProducto($gaseosa, -5));
        } finally {
            $this->assertSame(2, $gaseosa->fresh()->cantidad_actual);
        }
    }

    public function test_no_se_puede_revertir_un_ingreso_de_insumo_ya_consumido(): void
    {
        $pollo = $this->insumo('Pollo crudo', 0);
        $ingreso = IngresoInsumo::create(['fecha_ingreso' => now()->toDateString()]);
        $ingreso->detalles()->create(['insumo_id' => $pollo->id, 'cantidad' => 50, 'precio_unitario' => 20]);
        $pollo->update(['stock_actual' => 50]);

        // Producción consumió 40 kg
        $pollo->decrement('stock_actual', 40);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('ya se usó en producción');

        try {
            DB::transaction(fn () => app(StockService::class)->aplicarDiferenciaInsumos($ingreso->detalles, []));
        } finally {
            $this->assertEquals(10, (float) $pollo->fresh()->stock_actual);
        }
    }

    public function test_editar_solo_precio_de_ingreso_consumido_no_bloquea(): void
    {
        $pollo = $this->insumo('Pollo crudo', 10); // quedan 10 de 50 ingresados
        $ingreso = IngresoInsumo::create(['fecha_ingreso' => now()->toDateString()]);
        $ingreso->detalles()->create(['insumo_id' => $pollo->id, 'cantidad' => 50, 'precio_unitario' => 20]);

        DB::transaction(fn () => app(StockService::class)->aplicarDiferenciaInsumos(
            $ingreso->detalles,
            [['insumo_id' => $pollo->id, 'cantidad' => 50, 'precio_unitario' => 25]]
        ));

        $this->assertEquals(10, (float) $pollo->fresh()->stock_actual);
    }

    public function test_editar_ingreso_aplica_solo_la_diferencia_neta(): void
    {
        $pollo = $this->insumo('Pollo crudo', 30);
        $aceite = $this->insumo('Aceite', 5);
        $ingreso = IngresoInsumo::create(['fecha_ingreso' => now()->toDateString()]);
        $ingreso->detalles()->create(['insumo_id' => $pollo->id, 'cantidad' => 30, 'precio_unitario' => 20]);

        // Se corrige: 25 de pollo (−5) y se agregan 3 de aceite (+3)
        DB::transaction(fn () => app(StockService::class)->aplicarDiferenciaInsumos(
            $ingreso->detalles,
            [
                ['insumo_id' => $pollo->id, 'cantidad' => 25, 'precio_unitario' => 20],
                ['insumo_id' => $aceite->id, 'cantidad' => 3, 'precio_unitario' => 15],
            ]
        ));

        $this->assertEquals(25, (float) $pollo->fresh()->stock_actual);
        $this->assertEquals(8, (float) $aceite->fresh()->stock_actual);
    }
}
