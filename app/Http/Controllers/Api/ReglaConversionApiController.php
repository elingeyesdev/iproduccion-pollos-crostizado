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

class ReglaConversionApiController extends Controller
{
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

    public function show(string $id): JsonResponse
    {
        $reglaConversion = ReglaConversion::query()
            ->with(['insumo', 'producto', 'unidadEntrada', 'unidadSalida', 'recetaConversiones.insumo', 'recetaConversiones.unidadMedida'])
            ->where('estado', 1)
            ->findOrFail($id);

        return response()->json($reglaConversion);
    }

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
