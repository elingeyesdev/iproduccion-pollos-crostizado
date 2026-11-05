<?php

namespace App\Services;

use App\Models\Venta;
use Illuminate\Support\Facades\DB;

class VentaService
{
    public function __construct(private StockService $stockService)
    {
    }

    /**
     * Anula una venta y devuelve al stock los productos vendidos (incluidos los componentes de combos).
     */
    public function anular(Venta $venta): void
    {
        DB::transaction(function () use ($venta) {
            // Bloqueo para que dos solicitudes simultáneas no anulen (y repongan) dos veces
            $venta = Venta::whereKey($venta->id)->lockForUpdate()->firstOrFail();

            if ((int) $venta->estado === 0) {
                throw new \RuntimeException('La venta ya está anulada.');
            }

            $detalles = $venta->detalles()->where('estado', 1)->with('producto')->get();

            foreach ($detalles as $detalle) {
                $this->stockService->moverProducto($detalle->producto, (float) $detalle->cantidad);
            }

            $venta->detalles()->update(['estado' => 0]);
            $venta->pagos()->update(['estado' => 0]);
            $venta->update(['estado' => 0]);
        });
    }
}
