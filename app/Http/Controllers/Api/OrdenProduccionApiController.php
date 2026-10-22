<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenProduccionRequest;
use App\Models\OrdenProduccion;
use App\Models\OrdenProduccionInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrdenProduccionApiController extends Controller
{
    #[OA\Get(
        path: '/api/orden-producciones',
        summary: 'Listar órdenes de producción',
        description: 'Listado paginado (10 por página) con conteo de insumos y de insumos pendientes de producción. Permite buscar por número de orden o fecha programada.',
        tags: ['Órdenes de producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Filtra por número de orden o fecha programada', schema: new OA\Schema(type: 'string', example: 'OR-PRO-2026-10')),
            new OA\Parameter(name: 'page', in: 'query', required: false, description: 'Número de página', schema: new OA\Schema(type: 'integer', example: 1)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Órdenes de producción obtenidas correctamente (paginadas)'),
            new OA\Response(response: 401, description: 'No autenticado'),
        ]
    )]
    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));

        $ordenProducciones = OrdenProduccion::query()
            ->withCount('insumos')
            ->withCount(['insumos as insumos_pendientes_count' => function ($query) {
                $query->where('estado', 1)->whereDoesntHave('produccion');
            }])
            ->when($search, function ($query) use ($search) {
                $query->where('numero_orden', 'like', "%{$search}%")
                    ->orWhere('fecha_programada', 'like', "%{$search}%");
            })
            ->orderByDesc('fecha_programada')
            ->paginate(10)
            ->withQueryString();

        return response()->json($ordenProducciones);
    }

    #[OA\Post(
        path: '/api/orden-producciones',
        summary: 'Crear orden de producción',
        description: 'Crea la orden con sus insumos planificados. Si no se envía numero_orden, se genera automáticamente (OR-PRO-YYYY-MM-NNNN). No modifica stock.',
        tags: ['Órdenes de producción'],
        security: [['sanctum' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_programada', 'regla_conversion_id', 'insumos'],
                properties: [
                    new OA\Property(property: 'numero_orden', type: 'string', maxLength: 255, nullable: true, example: 'OR-PRO-2026-10-0001'),
                    new OA\Property(property: 'fecha_programada', type: 'string', format: 'date', example: '2026-10-05'),
                    new OA\Property(property: 'regla_conversion_id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'insumos',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['insumo_id', 'cantidad'],
                            properties: [
                                new OA\Property(property: 'insumo_id', type: 'integer', example: 3),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.01, example: 25.5),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Orden de producción creada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function store(OrdenProduccionRequest $request)
    {
        $data = $request->validated();

        $ordenProduccion = DB::transaction(function () use ($data) {
            $numeroOrden = !empty($data['numero_orden'])
                ? $data['numero_orden']
                : $this->generarNumeroOrden();

            $ordenProduccion = OrdenProduccion::create([
                'numero_orden' => $numeroOrden,
                'fecha_programada' => $data['fecha_programada'],
                'regla_conversion_id' => $data['regla_conversion_id'],
                'estado' => 1,
            ]);

            $detalles = collect($data['insumos'])->map(function ($insumoData) {
                return new OrdenProduccionInsumo([
                    'insumo_id' => $insumoData['insumo_id'],
                    'cantidad' => $insumoData['cantidad'],
                    'estado' => 1,
                ]);
            });

            $ordenProduccion->insumos()->saveMany($detalles->all());

            return $ordenProduccion;
        });

        $ordenProduccion->loadMissing([
            'insumos.insumo.unidadMedida',
            'reglaConversion.insumo',
            'reglaConversion.producto',
            'reglaConversion.recetaConversiones.insumo',
        ]);

        return response()->json($ordenProduccion, 201);
    }

    // Nota: el parámetro se llama "orden_produccione" porque Laravel singulariza
    // el recurso "orden-producciones" a "orden-produccione" (regla usada también
    // en OrdenProduccionRequest::rules()). El binding implícito exige este nombre
    // exacto cuando se registra vía Route::apiResource('orden-producciones', ...).
    #[OA\Get(
        path: '/api/orden-producciones/{orden_produccione}',
        summary: 'Ver orden de producción',
        description: 'Devuelve la orden con sus insumos, la receta (regla de conversión) y el conteo de insumos pendientes.',
        tags: ['Órdenes de producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'orden_produccione', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Orden de producción obtenida correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function show(OrdenProduccion $orden_produccione)
    {
        $ordenProduccion = $orden_produccione;

        $ordenProduccion->loadMissing([
            'insumos.insumo.unidadMedida',
            'reglaConversion.insumo',
            'reglaConversion.producto',
            'reglaConversion.recetaConversiones.insumo',
        ]);
        $ordenProduccion->loadCount(['insumos as insumos_pendientes_count' => function ($query) {
            $query->where('estado', 1)->whereDoesntHave('produccion');
        }]);

        return response()->json($ordenProduccion);
    }

    #[OA\Put(
        path: '/api/orden-producciones/{orden_produccione}',
        summary: 'Actualizar orden de producción',
        description: 'Actualiza los datos de la orden y reemplaza por completo la lista de insumos.',
        tags: ['Órdenes de producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'orden_produccione', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fecha_programada', 'regla_conversion_id', 'insumos'],
                properties: [
                    new OA\Property(property: 'numero_orden', type: 'string', maxLength: 255, nullable: true, example: 'OR-PRO-2026-10-0001'),
                    new OA\Property(property: 'fecha_programada', type: 'string', format: 'date', example: '2026-10-06'),
                    new OA\Property(property: 'regla_conversion_id', type: 'integer', example: 1),
                    new OA\Property(
                        property: 'insumos',
                        type: 'array',
                        minItems: 1,
                        items: new OA\Items(
                            required: ['insumo_id', 'cantidad'],
                            properties: [
                                new OA\Property(property: 'insumo_id', type: 'integer', example: 3),
                                new OA\Property(property: 'cantidad', type: 'number', format: 'float', minimum: 0.01, example: 30),
                            ]
                        )
                    ),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Orden de producción actualizada correctamente'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
            new OA\Response(response: 422, description: 'Error de validación'),
        ]
    )]
    public function update(OrdenProduccionRequest $request, OrdenProduccion $orden_produccione)
    {
        $ordenProduccion = $orden_produccione;
        $data = $request->validated();

        DB::transaction(function () use ($data, $ordenProduccion) {
            $ordenProduccion->update([
                'numero_orden' => $data['numero_orden'],
                'fecha_programada' => $data['fecha_programada'],
                'regla_conversion_id' => $data['regla_conversion_id'],
                'estado' => 1,
            ]);

            $ordenProduccion->insumos()->delete();

            $detalles = collect($data['insumos'])->map(function ($insumoData) {
                return new OrdenProduccionInsumo([
                    'insumo_id' => $insumoData['insumo_id'],
                    'cantidad' => $insumoData['cantidad'],
                    'estado' => 1,
                ]);
            });

            $ordenProduccion->insumos()->saveMany($detalles->all());
        });

        $ordenProduccion->loadMissing([
            'insumos.insumo.unidadMedida',
            'reglaConversion.insumo',
            'reglaConversion.producto',
            'reglaConversion.recetaConversiones.insumo',
        ]);

        return response()->json($ordenProduccion);
    }

    #[OA\Delete(
        path: '/api/orden-producciones/{orden_produccione}',
        summary: 'Eliminar orden de producción',
        description: 'Baja lógica: marca la orden con estado = 0.',
        tags: ['Órdenes de producción'],
        security: [['sanctum' => []]],
        parameters: [
            new OA\Parameter(name: 'orden_produccione', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Orden de producción eliminada'),
            new OA\Response(response: 401, description: 'No autenticado'),
            new OA\Response(response: 404, description: 'No encontrado'),
        ]
    )]
    public function destroy(OrdenProduccion $orden_produccione)
    {
        $orden_produccione->update(['estado' => 0]);

        return response()->json([
            'message' => 'Orden de producción eliminada',
        ]);
    }

    private function generarNumeroOrden(): string
    {
        $anioMes = now()->format('Y-m');
        $prefijo = 'OR-PRO-' . $anioMes . '-';

        $ultima = OrdenProduccion::latest('id')->first();

        $numero = 1;
        if ($ultima && preg_match('/-(\d{4})$/', $ultima->numero_orden, $matches)) {
            $numero = (int) $matches[1] + 1;
        }

        return $prefijo . str_pad($numero, 4, '0', STR_PAD_LEFT);
    }
}
