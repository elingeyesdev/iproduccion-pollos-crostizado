<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaRequest;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class VentaApiController extends Controller
{
    public function index()
    {
        return response()->json(
            Venta::with(['cliente', 'pagos'])->where('estado', 1)->orderByDesc('id')->get()
        );
    }

    public function store(VentaRequest $request)
    {
        $data = $request->validated();

        try {
            $venta = DB::transaction(function () use ($data, $request) {
                $data['fecha_venta'] = $data['fecha_venta'] ?? Carbon::now()->toDateString();
                $data['descuento'] = $data['descuento'] ?? 0;

                $pagoEfectivo = (float) ($data['pago_efectivo'] ?? 0);
                $pagoQr = (float) ($data['pago_qr'] ?? 0);

                if ($pagoEfectivo <= 0 && $pagoQr <= 0) {
                    throw new \RuntimeException('Debes registrar al menos un pago (efectivo o QR).');
                }

                $totalPagado = round($pagoEfectivo + $pagoQr, 2);
                $totalVenta = round((float) $data['total'], 2);

                if (abs($totalPagado - $totalVenta) > 0.01) {
                    throw new \RuntimeException('La suma de pagos debe ser igual al total de la venta.');
                }

                $venta = Venta::create([
                    'cliente_id' => $data['cliente_id'],
                    'fecha_venta' => $data['fecha_venta'],
                    'total' => $data['total'],
                    'estado' => $data['estado'] ?? 1,
                    'descuento' => $data['descuento'],
                ]);

                foreach ($request->productos as $index => $producto_id) {
                    $producto = Producto::with('ingredientes')->findOrFail((int) $producto_id);

                    $precioUnitario = (float) $producto->precio;
                    $cantidadVenta = (float) $request->cantidades[$index];

                    DetalleVenta::create([
                        'venta_id' => $venta->id,
                        'producto_id' => $producto_id,
                        'precio_unitario' => $precioUnitario,
                        'cantidad' => $cantidadVenta,
                        'sub_total' => $precioUnitario * $cantidadVenta,
                        'estado' => 1,
                    ]);

                    // Lógica de descuento de stock recursiva (combos)
                    $this->descontarStockProducto($producto, $cantidadVenta);
                }

                if ($pagoEfectivo > 0) {
                    VentaPago::create([
                        'venta_id' => $venta->id,
                        'metodo_pago' => 'Efectivo',
                        'monto' => $pagoEfectivo,
                        'estado' => 1,
                    ]);
                }

                if ($pagoQr > 0) {
                    VentaPago::create([
                        'venta_id' => $venta->id,
                        'metodo_pago' => 'QR',
                        'monto' => $pagoQr,
                        'estado' => 1,
                    ]);
                }

                return $venta;
            });
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => 'Error al crear la venta: ' . $e->getMessage(),
            ], 422);
        }

        $venta->load(['cliente', 'detalles.producto', 'pagos']);

        return response()->json($venta, 201);
    }

    public function show(string $id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'pagos'])
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($venta);
    }

    public function update(VentaRequest $request, string $id)
    {
        $venta = Venta::where('estado', 1)->findOrFail($id);
        $venta->update($request->validated());

        return response()->json($venta);
    }

    public function destroy(string $id)
    {
        $venta = Venta::where('estado', 1)->findOrFail($id);
        $venta->update(['estado' => 0]);

        return response()->json([
            'message' => 'Venta eliminada',
        ]);
    }

    /**
     * Descuenta el stock de un producto de forma recursiva si es un combo.
     */
    private function descontarStockProducto(Producto $producto, float $cantidadVenta)
    {
        if ($producto->ingredientes->count() > 0) {
            // Es un combo: descontar sus componentes (productos)
            foreach ($producto->ingredientes as $ingrediente) {
                $cantidadARestar = $ingrediente->pivot->cantidad * $cantidadVenta;
                $this->descontarStockProducto($ingrediente, $cantidadARestar);
            }
        } else {
            // Es un producto simple: descontar su propio stock
            if ($producto->cantidad_actual < $cantidadVenta) {
                throw new \RuntimeException("Stock insuficiente para: {$producto->nombre}. Disponible: {$producto->cantidad_actual}");
            }
            $producto->cantidad_actual -= $cantidadVenta;
            $producto->save();
        }
    }
}
