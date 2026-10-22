<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngresoBebidaRequest;
use App\Models\IngresoBebida;
use App\Models\Producto;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class IngresoBebidasController extends Controller
{
    #[OA\Get(
        path: '/api/ingreso-bebidas',
        summary: 'Listar ingresos de bebidas',
        description: 'Devuelve los ingresos de bebidas activos con sus productos, ordenados por fecha de ingreso descendente.',
        tags: ['Ingresos de bebidas'],
        security: [['sanctum' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Ingresos de bebidas obtenidos correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index()
    {
        return response()->json(
            IngresoBebida::with('productos')
                ->where('estado', 1)
                ->orderByDesc('fecha_ingreso')
                ->get()
        );
    }

    #[OA\Post(
        path: '/api/ingreso-bebidas',
        summary: 'Registrar ingreso de bebidas',
        description: 'Crea un lote de ingreso de bebidas y suma la cantidad ingresada al stock (cantidad_actual) de cada producto. Si no se envía nombre, se genera automáticamente (LOTE-BEB-YYYY-MM-NNN).',
        tags: ['Ingresos de bebidas'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_ingreso', 'productos'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, nullable: true, example: 'LOTE-BEB-2026-10-001'),
                    new OA\Property(property: 'descripcion', type: 'string', maxLength: 255, nullable: true, example: 'Ingreso semanal de gaseosas'),
                    new OA\Property(property: 'fecha_ingreso', type: 'string', format: 'date', example: '2026-10-03'),
                    new OA\Property(
                        property: 'productos',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['producto_id', 'cantidad_ingresa', 'precio'],
                            properties: [
                                new OA\Property(property: 'producto_id', type: 'integer', example: 5),
                                new OA\Property(property: 'cantidad_ingresa', type: 'number', format: 'float', minimum: 0.01, example: 24),
                                new OA\Property(property: 'precio', type: 'number', format: 'float', minimum: 0, example: 8.5),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Ingreso de bebidas registrado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(IngresoBebidaRequest $request)
    {
        $data = $request->validated();

        $ingresoBebida = DB::transaction(function () use ($data) {
            $nombreLote = !empty($data['nombre'])
                ? $data['nombre']
                : $this->generarNombreLote();

            $ingresoBebida = IngresoBebida::create([
                'nombre' => $nombreLote,
                'descripcion' => $data['descripcion'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
            ]);

            foreach ($data['productos'] as $item) {
                $ingresoBebida->productos()->attach($item['producto_id'], [
                    'cantidad_ingresa' => $item['cantidad_ingresa'],
                    'precio' => $item['precio'],
                ]);

                Producto::where('id', $item['producto_id'])
                    ->increment('cantidad_actual', (int) $item['cantidad_ingresa']);
            }

            return $ingresoBebida;
        });

        $ingresoBebida->load('productos');

        return response()->json($ingresoBebida, 201);
    }

    #[OA\Get(
        path: '/api/ingreso-bebidas/{ingreso_bebida}',
        summary: 'Ver ingreso de bebidas',
        tags: ['Ingresos de bebidas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'ingreso_bebida', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ingreso de bebidas obtenido correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id)
    {
        $ingresoBebida = IngresoBebida::with('productos.tipoProducto')
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($ingresoBebida);
    }

    #[OA\Put(
        path: '/api/ingreso-bebidas/{ingreso_bebida}',
        summary: 'Actualizar ingreso de bebidas',
        description: 'Revierte el stock del ingreso anterior, reemplaza el detalle de productos y vuelve a sumar las nuevas cantidades al stock.',
        tags: ['Ingresos de bebidas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'ingreso_bebida', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_ingreso', 'productos'],
                properties: [
                    new OA\Property(property: 'nombre', type: 'string', maxLength: 255, nullable: true, example: 'LOTE-BEB-2026-10-001'),
                    new OA\Property(property: 'descripcion', type: 'string', maxLength: 255, nullable: true, example: 'Ingreso semanal de gaseosas'),
                    new OA\Property(property: 'fecha_ingreso', type: 'string', format: 'date', example: '2026-10-03'),
                    new OA\Property(
                        property: 'productos',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['producto_id', 'cantidad_ingresa', 'precio'],
                            properties: [
                                new OA\Property(property: 'producto_id', type: 'integer', example: 5),
                                new OA\Property(property: 'cantidad_ingresa', type: 'number', format: 'float', minimum: 0.01, example: 30),
                                new OA\Property(property: 'precio', type: 'number', format: 'float', minimum: 0, example: 8.5),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Ingreso de bebidas actualizado correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(IngresoBebidaRequest $request, string $id)
    {
        $data = $request->validated();

        $ingresoBebida = DB::transaction(function () use ($data, $id) {
            $ingresoBebida = IngresoBebida::where('estado', 1)->findOrFail($id);
            $ingresoBebida->load('productos');

            // Revertir stock anterior
            foreach ($ingresoBebida->productos as $producto) {
                Producto::where('id', $producto->id)
                    ->decrement('cantidad_actual', (int) $producto->pivot->cantidad_ingresa);
            }

            $ingresoBebida->update([
                'nombre' => $data['nombre'] ?? $ingresoBebida->nombre,
                'descripcion' => $data['descripcion'] ?? null,
                'fecha_ingreso' => $data['fecha_ingreso'],
            ]);

            $ingresoBebida->productos()->detach();

            foreach ($data['productos'] as $item) {
                $ingresoBebida->productos()->attach($item['producto_id'], [
                    'cantidad_ingresa' => $item['cantidad_ingresa'],
                    'precio' => $item['precio'],
                ]);

                Producto::where('id', $item['producto_id'])
                    ->increment('cantidad_actual', (int) $item['cantidad_ingresa']);
            }

            return $ingresoBebida;
        });

        $ingresoBebida->load('productos');

        return response()->json($ingresoBebida);
    }

    #[OA\Delete(
        path: '/api/ingreso-bebidas/{ingreso_bebida}',
        summary: 'Eliminar ingreso de bebidas',
        description: 'Baja lógica (estado = 0) del ingreso y descuenta del stock de cada producto la cantidad que había ingresado.',
        tags: ['Ingresos de bebidas'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'ingreso_bebida', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Ingreso de bebida eliminado'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id)
    {
        DB::transaction(function () use ($id) {
            $ingresoBebida = IngresoBebida::where('estado', 1)->findOrFail($id);
            $ingresoBebida->load('productos');

            foreach ($ingresoBebida->productos as $producto) {
                Producto::where('id', $producto->id)
                    ->decrement('cantidad_actual', (int) $producto->pivot->cantidad_ingresa);
            }

            $ingresoBebida->update(['estado' => 0]);
        });

        return response()->json([
            'message' => 'Ingreso de bebida eliminado',
        ]);
    }

    private function generarNombreLote(): string
    {
        $anioMes = now()->format('Y-m');
        $prefijo = 'LOTE-BEB-' . $anioMes . '-';

        $ultimoMes = IngresoBebida::where('nombre', 'like', $prefijo . '%')
            ->latest('id')
            ->first();

        $numero = 1;
        if ($ultimoMes && preg_match('/-(\d{3})$/', $ultimoMes->nombre, $matches)) {
            $numero = (int) $matches[1] + 1;
        }

        return $prefijo . str_pad($numero, 3, '0', STR_PAD_LEFT);
    }
}
