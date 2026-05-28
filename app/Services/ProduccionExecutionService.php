<?php

namespace App\Services;

use App\Models\OrdenProduccion;
use App\Models\Produccion;
use App\Models\Producto;
use App\Models\Receta;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ProduccionExecutionService
{
    public function buildPreview(Receta $receta, float $cantidadInsumo): array
    {
        $receta->loadMissing(['insumos.unidadMedida', 'producto']);

        $consumos = $receta->insumos->map(function ($insumo) use ($receta, $cantidadInsumo) {
            $cantidad = ((float) $insumo->pivot->cantidad_requerida / (float) $receta->cantidad_base) * $cantidadInsumo;

            return [
                'insumo' => $insumo,
                'cantidad' => $cantidad,
                'unidad' => $insumo->unidadMedida?->abreviatura ?? $insumo->unidadMedida?->nombre ?? '',
                'stock_actual' => (float) $insumo->stock_actual,
                'stock_restante' => (float) $insumo->stock_actual - $cantidad,
            ];
        });

        $cantidadMin = ($cantidadInsumo / (float) $receta->cantidad_base) * (float) $receta->rendimiento_esperado;

        return [
            'consumos' => $consumos,
            'producto_final' => $receta->producto,
            'cantidad_esperada' => $cantidadMin,
        ];
    }

    public function confirmarDesdeOrden(OrdenProduccion $orden): Produccion
    {
        $orden->loadMissing('receta.insumos');

        return $this->confirmar(
            ordenProduccionId: (int) $orden->id,
            recetaId: (int) $orden->receta_id,
            cantidadInsumo: (float) $orden->cantidad_insumo,
            cantidadObtenida: (float) $orden->cantidad_obtenida,
        );
    }

    public function confirmar(int $ordenProduccionId, int $recetaId, float $cantidadInsumo, float $cantidadObtenida): Produccion
    {
        return DB::transaction(function () use ($ordenProduccionId, $recetaId, $cantidadInsumo, $cantidadObtenida) {
            $receta = Receta::with('insumos')->findOrFail($recetaId);

            $produccion = Produccion::create([
                'orden_produccion_id' => $ordenProduccionId,
                'receta_id' => $recetaId,
                'cantidad_insumo' => $cantidadInsumo,
                'cantidad_obtenida' => $cantidadObtenida,
                'estado' => 1,
            ]);

            foreach ($receta->insumos as $insumo) {
                $cantidadADescontar = ((float) $insumo->pivot->cantidad_requerida / (float) $receta->cantidad_base) * $cantidadInsumo;

                $nuevoStock = (float) $insumo->stock_actual - $cantidadADescontar;
                if ($nuevoStock < 0) {
                    throw new \RuntimeException("Stock insuficiente para el insumo: {$insumo->nombre}");
                }

                $insumo->stock_actual = $nuevoStock;
                $insumo->save();

                $produccion->insumos()->attach($insumo->id, [
                    'cantidad' => $cantidadADescontar,
                    'estado' => 1,
                ]);
            }

            $productoFinal = Producto::findOrFail($receta->producto_id);
            $productoFinal->cantidad_actual += $cantidadObtenida;
            $productoFinal->save();

            $produccion->productos()->attach($productoFinal->id, [
                'cantidad_producida' => $cantidadObtenida,
                'estado' => 1,
            ]);

            return $produccion;
        });
    }
}
