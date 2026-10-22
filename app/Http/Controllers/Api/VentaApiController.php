<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\VentaRequest;
use App\Models\DetalleVenta;
use App\Models\Producto;
use App\Models\Venta;
use App\Models\VentaPago;
use App\Services\StockService;
use App\Services\VentaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class VentaApiController extends Controller
{
    public function __construct(
        private StockService $stockService,
        private VentaService $ventaService,
    ) {
    }

    #[OA\Get(
        path: '/api/ventas',
        summary: 'Listar ventas',
        description: 'Devuelve las ventas activas con cliente y pagos, ordenadas por id descendente.',
        tags: ['Ventas'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Ventas obtenidas correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index()
    {
        return response()->json(
            Venta::with(['cliente', 'pagos'])->where('estado', 1)->orderByDesc('id')->get()
        );
    }

    #[OA\Post(
        path: '/api/ventas',
        summary: 'Registrar venta',
        description: 'Crea la venta con su detalle (productos[i] con cantidades[i]; el precio unitario se toma del producto) y descuenta el stock de cada producto vendido, incluidos los componentes de combos. Registra los pagos en efectivo y/o QR: debe haber al menos un pago y la suma de pagos debe ser igual al total.',
        tags: ['Ventas'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['cliente_id', 'total', 'productos', 'cantidades', 'precios'],
                properties: [
                    new OA\Property(property: 'cliente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'fecha_venta', type: 'string', format: 'date', nullable: true, example: '2026-10-03'),
                    new OA\Property(property: 'total', type: 'number', format: 'float', minimum: 0, example: 75),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                    new OA\Property(property: 'descuento', type: 'number', format: 'float', minimum: 0, nullable: true, example: 0),
                    new OA\Property(property: 'pago_efectivo', type: 'number', format: 'float', minimum: 0, nullable: true, example: 50),
                    new OA\Property(property: 'pago_qr', type: 'number', format: 'float', minimum: 0, nullable: true, example: 25),
                    new OA\Property(property: 'productos', type: 'array', minItems: 1, description: 'IDs de productos', items: new OA\Items(type: 'integer'), example: [2, 5]),
                    new OA\Property(property: 'cantidades', type: 'array', description: 'Cantidad por producto (mismo orden que productos)', items: new OA\Items(type: 'number', format: 'float', minimum: 0.01), example: [2, 3]),
                    new OA\Property(property: 'precios', type: 'array', description: 'Precio por producto (mismo orden que productos)', items: new OA\Items(type: 'number', format: 'float', minimum: 0), example: [30, 5]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Venta registrada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación o error al crear la venta (sin pagos, pagos distintos al total, stock insuficiente)'),
        ]
    )]
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
                    $this->stockService->moverProducto($producto, -$cantidadVenta);
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

    #[OA\Get(
        path: '/api/ventas/{venta}',
        summary: 'Ver venta',
        description: 'Devuelve la venta activa con cliente, detalle de productos y pagos.',
        tags: ['Ventas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'venta', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Venta obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $venta = Venta::with(['cliente', 'detalles.producto', 'pagos'])
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($venta);
    }

    #[OA\Put(
        path: '/api/ventas/{venta}',
        summary: 'Actualizar venta',
        description: 'Actualiza los datos de cabecera de la venta. Se valida con las mismas reglas que el registro, pero no modifica el detalle, los pagos ni el stock.',
        tags: ['Ventas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'venta', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['cliente_id', 'total', 'productos', 'cantidades', 'precios'],
                properties: [
                    new OA\Property(property: 'cliente_id', type: 'integer', example: 1),
                    new OA\Property(property: 'fecha_venta', type: 'string', format: 'date', nullable: true, example: '2026-10-03'),
                    new OA\Property(property: 'total', type: 'number', format: 'float', minimum: 0, example: 75),
                    new OA\Property(property: 'estado', type: 'integer', enum: [0, 1], nullable: true, example: 1),
                    new OA\Property(property: 'descuento', type: 'number', format: 'float', minimum: 0, nullable: true, example: 0),
                    new OA\Property(property: 'pago_efectivo', type: 'number', format: 'float', minimum: 0, nullable: true, example: 50),
                    new OA\Property(property: 'pago_qr', type: 'number', format: 'float', minimum: 0, nullable: true, example: 25),
                    new OA\Property(property: 'productos', type: 'array', minItems: 1, description: 'IDs de productos', items: new OA\Items(type: 'integer'), example: [2, 5]),
                    new OA\Property(property: 'cantidades', type: 'array', description: 'Cantidad por producto (mismo orden que productos)', items: new OA\Items(type: 'number', format: 'float', minimum: 0.01), example: [2, 3]),
                    new OA\Property(property: 'precios', type: 'array', description: 'Precio por producto (mismo orden que productos)', items: new OA\Items(type: 'number', format: 'float', minimum: 0), example: [30, 5]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Venta actualizada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(VentaRequest $request, string $id)
    {
        $venta = Venta::where('estado', 1)->findOrFail($id);
        $venta->update($request->validated());

        return response()->json($venta);
    }

    #[OA\Delete(
        path: '/api/ventas/{venta}',
        summary: 'Anular venta',
        description: 'Anula la venta (estado = 0 en venta, detalles y pagos) y repone al stock los productos vendidos, incluidos los componentes de combos.',
        tags: ['Ventas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'venta', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Venta anulada y stock repuesto'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'No se pudo anular la venta (por ejemplo, ya estaba anulada)'),
        ]
    )]
    public function destroy(string $id)
    {
        $venta = Venta::where('estado', 1)->findOrFail($id);

        try {
            $this->ventaService->anular($venta);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => 'No se pudo anular la venta: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Venta anulada y stock repuesto',
        ]);
    }
}
