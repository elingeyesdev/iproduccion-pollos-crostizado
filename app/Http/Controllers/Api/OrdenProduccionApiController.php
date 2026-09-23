<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrdenProduccionRequest;
use App\Models\OrdenProduccion;
use App\Models\OrdenProduccionInsumo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdenProduccionApiController extends Controller
{
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
