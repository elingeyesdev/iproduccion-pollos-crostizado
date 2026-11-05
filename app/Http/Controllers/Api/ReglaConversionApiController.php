<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReglaConversionRequest;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\ReglaConversion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use OpenApi\Attributes as OA;

class ReglaConversionApiController extends Controller
{
    #[OA\Get(
        path: '/api/regla-conversion',
        summary: 'Listar reglas de conversión activas (paginado)',
        security: [['sanctum' => []]],
        tags: ['Reglas de conversión'],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Filtra por nombre de insumo o de producto resultante', schema: new OA\Schema(type: 'string'), example: 'pollo'),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página (10 registros por página)', schema: new OA\Schema(type: 'integer', minimum: 1), example: 1),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Reglas de conversión obtenidas correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->input('search'));

        $reglas = ReglaConversion::query()
            ->with(['insumo:id,nombre', 'producto:id,nombre', 'unidadEntrada:id,nombre,abreviatura', 'unidadSalida:id,nombre,abreviatura'])
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('nombre_insumo', 'like', "%{$search}%")
                        ->orWhere('producto_resultante', 'like', "%{$search}%")
                        ->orWhereHas('insumo', function ($qi) use ($search) {
                            $qi->where('nombre', 'like', "%{$search}%");
                        })
                        ->orWhereHas('producto', function ($qp) use ($search) {
                            $qp->where('nombre', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        return response()->json($reglas);
    }

    #[OA\Post(
        path: '/api/regla-conversion',
        summary: 'Registrar una regla de conversión',
        description: 'La cantidad de entrada se fija siempre en 1. Las unidades de entrada/resultante son obligatorias cuando la tabla reglas_conversion tiene las columnas unidad_entrada_id/unidad_salida_id.',
        security: [['sanctum' => []]],
        tags: ['Reglas de conversión'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['insumo_id', 'producto_id', 'rendimiento_minimo', 'rendimiento_maximo', 'tipo_conversion'],
                properties: [
                    new OA\Property(property: 'insumo_id', type: 'integer', example: 1),
                    new OA\Property(property: 'producto_id', type: 'integer', example: 3),
                    new OA\Property(property: 'rendimiento_minimo', type: 'number', format: 'float', minimum: 0, example: 8),
                    new OA\Property(property: 'rendimiento_maximo', type: 'number', format: 'float', minimum: 0, description: 'Debe ser mayor o igual al rendimiento mínimo', example: 10),
                    new OA\Property(property: 'tipo_conversion', type: 'string', enum: ['estatica', 'variable', 'Estática', 'Variable'], example: 'variable'),
                    new OA\Property(property: 'unidad_medida_entrada_id', type: 'integer', example: 1),
                    new OA\Property(property: 'unidad_medida_resultante_id', type: 'integer', example: 2),
                    new OA\Property(
                        property: 'detalles',
                        type: 'array',
                        nullable: true,
                        items: new OA\Items(
                            required: ['insumo_id', 'cantidad', 'unidad_medida_id'],
                            properties: [
                                new OA\Property(property: 'insumo_id', type: 'integer', example: 4),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.001, example: 0.25),
                                new OA\Property(property: 'unidad_medida_id', type: 'integer', example: 1),
                                new OA\Property(property: 'observacion', type: 'string', maxLength: 255, nullable: true, example: 'Harina para el apanado'),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Regla de conversión creada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(StoreReglaConversionRequest $request): JsonResponse
    {
        $data = $request->validated();

        $insumo = Insumo::findOrFail((int) $data['insumo_id']);
        $producto = Producto::findOrFail((int) $data['producto_id']);

        $payload = $this->buildPayload($data, $insumo, $producto, true);

        $reglaConversion = DB::transaction(function () use ($payload, $data) {
            $reglaConversion = ReglaConversion::create($payload);

            $this->guardarDetalles($reglaConversion, $data['detalles'] ?? []);

            return $reglaConversion;
        });

        $reglaConversion->load(['recetaConversiones.insumo', 'recetaConversiones.unidadMedida']);

        return response()->json($reglaConversion, 201);
    }

    #[OA\Get(
        path: '/api/regla-conversion/{regla_conversion}',
        summary: 'Obtener una regla de conversión con sus detalles',
        security: [['sanctum' => []]],
        tags: ['Reglas de conversión'],
        parameters: [
            new OA\Parameter(name: 'regla_conversion', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Regla de conversión obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(string $id): JsonResponse
    {
        $reglaConversion = ReglaConversion::query()
            ->with(['insumo', 'producto', 'unidadEntrada', 'unidadSalida', 'recetaConversiones.insumo', 'recetaConversiones.unidadMedida'])
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($reglaConversion);
    }

    #[OA\Put(
        path: '/api/regla-conversion/{regla_conversion}',
        summary: 'Actualizar una regla de conversión',
        description: 'Reemplaza los detalles (receta) de la regla. La cantidad de entrada se fija siempre en 1.',
        security: [['sanctum' => []]],
        tags: ['Reglas de conversión'],
        parameters: [
            new OA\Parameter(name: 'regla_conversion', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['insumo_id', 'producto_id', 'rendimiento_minimo', 'rendimiento_maximo', 'tipo_conversion'],
                properties: [
                    new OA\Property(property: 'insumo_id', type: 'integer', example: 1),
                    new OA\Property(property: 'producto_id', type: 'integer', example: 3),
                    new OA\Property(property: 'rendimiento_minimo', type: 'number', format: 'float', minimum: 0, example: 7.5),
                    new OA\Property(property: 'rendimiento_maximo', type: 'number', format: 'float', minimum: 0, description: 'Debe ser mayor o igual al rendimiento mínimo', example: 9),
                    new OA\Property(property: 'tipo_conversion', type: 'string', enum: ['estatica', 'variable', 'Estática', 'Variable'], example: 'estatica'),
                    new OA\Property(property: 'unidad_medida_entrada_id', type: 'integer', example: 1),
                    new OA\Property(property: 'unidad_medida_resultante_id', type: 'integer', example: 2),
                    new OA\Property(
                        property: 'detalles',
                        type: 'array',
                        nullable: true,
                        items: new OA\Items(
                            required: ['insumo_id', 'cantidad', 'unidad_medida_id'],
                            properties: [
                                new OA\Property(property: 'insumo_id', type: 'integer', example: 4),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.001, example: 0.3),
                                new OA\Property(property: 'unidad_medida_id', type: 'integer', example: 1),
                                new OA\Property(property: 'observacion', type: 'string', maxLength: 255, nullable: true, example: null),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Regla de conversión actualizada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(StoreReglaConversionRequest $request, string $id): JsonResponse
    {
        $reglaConversion = ReglaConversion::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $data = $request->validated();

        $insumo = Insumo::findOrFail((int) $data['insumo_id']);
        $producto = Producto::findOrFail((int) $data['producto_id']);

        $payload = $this->buildPayload($data, $insumo, $producto, false);

        DB::transaction(function () use ($reglaConversion, $payload, $data) {
            $reglaConversion->update($payload);

            $this->guardarDetalles($reglaConversion, $data['detalles'] ?? []);
        });

        $reglaConversion->load(['recetaConversiones.insumo', 'recetaConversiones.unidadMedida']);

        return response()->json($reglaConversion);
    }

    #[OA\Delete(
        path: '/api/regla-conversion/{regla_conversion}',
        summary: 'Eliminar (desactivar) una regla de conversión',
        security: [['sanctum' => []]],
        tags: ['Reglas de conversión'],
        parameters: [
            new OA\Parameter(name: 'regla_conversion', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Regla de conversión eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $reglaConversion = ReglaConversion::query()
            ->where('estado', 1)
            ->findOrFail($id);

        $reglaConversion->update(['estado' => 0]);

        return response()->json([
            'message' => 'Regla de conversión eliminada',
        ]);
    }

    private function buildPayload(array $data, Insumo $insumo, Producto $producto, bool $isCreate): array
    {
        $payload = [
            'cantidad_entrada' => (float) $data['cantidad_entrada'],
            'rendimiento_min' => (float) $data['rendimiento_minimo'],
            'rendimiento_max' => (float) $data['rendimiento_maximo'],
            'tipo_conversion' => $data['tipo_conversion'],
        ];

        if ($isCreate) {
            $payload['estado'] = 1;
        }

        if (Schema::hasColumn('reglas_conversion', 'insumo_id')) {
            $payload['insumo_id'] = $data['insumo_id'] ?? null;
        }

        if (Schema::hasColumn('reglas_conversion', 'nombre_insumo')) {
            $payload['nombre_insumo'] = (string) $insumo->nombre;
        }

        if (Schema::hasColumn('reglas_conversion', 'unidad_entrada_id')) {
            $payload['unidad_entrada_id'] = $data['unidad_medida_entrada_id'] ?? null;
        }

        if (Schema::hasColumn('reglas_conversion', 'producto_id')) {
            $payload['producto_id'] = $data['producto_id'] ?? null;
        }

        if (Schema::hasColumn('reglas_conversion', 'producto_resultante')) {
            $payload['producto_resultante'] = (string) $producto->nombre;
        }

        if (Schema::hasColumn('reglas_conversion', 'unidad_salida_id')) {
            $payload['unidad_salida_id'] = $data['unidad_medida_resultante_id'] ?? null;
        }

        return $payload;
    }

    private function guardarDetalles(ReglaConversion $reglaConversion, array $detalles): void
    {
        $reglaConversion->recetaConversiones()->delete();

        foreach ($detalles as $detalle) {
            if (empty($detalle['insumo_id']) || empty($detalle['cantidad']) || empty($detalle['unidad_medida_id'])) {
                continue;
            }

            $reglaConversion->recetaConversiones()->create([
                'insumo_id' => $detalle['insumo_id'],
                'cantidad' => $detalle['cantidad'],
                'unidad_medida_id' => $detalle['unidad_medida_id'],
                'observacion' => $detalle['observacion'] ?? null,
                'estado' => 1,
            ]);
        }
    }
}
