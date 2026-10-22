<?php

namespace App\Services;

use App\Models\Insumo;
use App\Models\Producto;

/**
 * Movimientos de stock con bloqueo pesimista (lockForUpdate).
 * Debe llamarse siempre dentro de una DB::transaction.
 */
class StockService
{
    /**
     * Mueve el stock de un producto: cantidad negativa descuenta, positiva repone.
     * Si el producto es un combo, el movimiento se aplica de forma recursiva a sus componentes.
     */
    public function moverProducto(Producto $producto, float $cantidad): void
    {
        $producto->loadMissing('ingredientes');

        if ($producto->ingredientes->isNotEmpty()) {
            foreach ($producto->ingredientes as $ingrediente) {
                $this->moverProducto($ingrediente, (float) $ingrediente->pivot->cantidad * $cantidad);
            }

            return;
        }

        $fila = Producto::whereKey($producto->id)->lockForUpdate()->firstOrFail();

        if ($cantidad < 0 && (float) $fila->cantidad_actual < abs($cantidad)) {
            throw new \RuntimeException("Stock insuficiente para: {$fila->nombre}. Disponible: {$fila->cantidad_actual}");
        }

        $fila->increment('cantidad_actual', $cantidad);
    }

    /**
     * Productos base (con stock propio) que consume vender $cantidad del producto, como [producto_id => cantidad].
     * Sigue la misma recursión que moverProducto para que los combos se validen igual que al descontar.
     *
     * @return array<int, float>
     */
    public function componentesBase(Producto $producto, float $cantidad = 1): array
    {
        $producto->loadMissing('ingredientes');

        if ($producto->ingredientes->isEmpty()) {
            return [$producto->id => $cantidad];
        }

        $base = [];

        foreach ($producto->ingredientes as $ingrediente) {
            foreach ($this->componentesBase($ingrediente, (float) $ingrediente->pivot->cantidad * $cantidad) as $id => $cant) {
                $base[$id] = ($base[$id] ?? 0) + $cant;
            }
        }

        return $base;
    }

    /**
     * Aplica al stock de insumos la diferencia neta entre los detalles anteriores de un ingreso
     * y los nuevos ([] al eliminar). Impide revertir cantidades que ya se consumieron en producción.
     *
     * @param iterable<\App\Models\IngresoInsumoDetalle> $anteriores
     * @param array<int, array{insumo_id:int|string, cantidad:float|string}> $nuevos
     */
    public function aplicarDiferenciaInsumos(iterable $anteriores, array $nuevos): void
    {
        $delta = [];

        foreach ($anteriores as $detalle) {
            $delta[$detalle->insumo_id] = ($delta[$detalle->insumo_id] ?? 0) - (float) $detalle->cantidad;
        }

        foreach ($nuevos as $detalle) {
            $delta[$detalle['insumo_id']] = ($delta[$detalle['insumo_id']] ?? 0) + (float) $detalle['cantidad'];
        }

        // Orden fijo de bloqueo para evitar deadlocks entre transacciones concurrentes
        ksort($delta);

        foreach ($delta as $insumoId => $diferencia) {
            $diferencia = round($diferencia, 2);

            if ($diferencia == 0) {
                continue;
            }

            $insumo = Insumo::whereKey($insumoId)->lockForUpdate()->firstOrFail();

            if ((float) $insumo->stock_actual + $diferencia < 0) {
                throw new \RuntimeException(
                    "No se puede modificar el ingreso: el insumo \"{$insumo->nombre}\" ya se usó en producción. "
                    . "Stock actual: {$insumo->stock_actual}, se intentan quitar " . abs($diferencia) . '.'
                );
            }

            $insumo->increment('stock_actual', $diferencia);
        }
    }
}
