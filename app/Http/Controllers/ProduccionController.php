<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\OrdenProduccionInsumo;
use App\Models\Produccion;
use App\Models\Producto;
use App\Models\ReglaConversion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProduccionController extends Controller
{
    public function create(): View
    {
        $produccion = new Produccion();

        $detallesOrden = OrdenProduccionInsumo::query()
            ->with(['ordenProduccion:id,numero_orden,fecha_programada', 'insumo:id,nombre'])
            ->where('estado', 1)
            ->whereDoesntHave('produccion')
            ->orderByDesc('id')
            ->get();

        $insumoIds = $detallesOrden
            ->pluck('insumo_id')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $insumoNombres = $detallesOrden
            ->map(fn ($d) => trim((string) ($d->insumo?->nombre ?? '')))
            ->filter()
            ->unique()
            ->values()
            ->all();

        $reglasQuery = ReglaConversion::query()
            ->with(['insumo:id,nombre', 'unidadEntrada:id,nombre,abreviatura', 'unidadSalida:id,nombre,abreviatura', 'producto:id,nombre'])
            ->where('estado', 1)
            ->orderBy('id');

        if ($this->reglaTieneColumna('insumo_id')) {
            if (!empty($insumoIds)) {
                $reglasQuery->whereIn('insumo_id', $insumoIds);
            } else {
                $reglasQuery->whereRaw('1 = 0');
            }
        } elseif ($this->reglaTieneColumna('nombre_insumo')) {
            if (!empty($insumoNombres)) {
                $reglasQuery->whereIn('nombre_insumo', $insumoNombres);
            } else {
                $reglasQuery->whereRaw('1 = 0');
            }
        } else {
            $reglasQuery->whereRaw('1 = 0');
        }

        $reglas = $reglasQuery->get();

        return view('produccion.create', compact('produccion', 'detallesOrden', 'reglas'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'orden_produccion_insumo_id' => 'required|integer|exists:orden_produccion_insumo,id',
            'regla_conversion_id' => 'required|integer|exists:reglas_conversion,id',
            'cantidad_obtenida' => 'nullable|numeric|min:0.01',
        ]);

        $detalle = OrdenProduccionInsumo::with(['ordenProduccion', 'insumo.unidadMedida'])
            ->where('id', $data['orden_produccion_insumo_id'])
            ->where('estado', 1)
            ->firstOrFail();

        $regla = ReglaConversion::with(['insumo', 'producto'])
            ->where('id', $data['regla_conversion_id'])
            ->where('estado', 1)
            ->firstOrFail();

        if (!$this->reglaCoincideConDetalle($regla, $detalle)) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['regla_conversion_id' => 'La regla seleccionada no corresponde al insumo del detalle de orden.']);
        }

        if ($detalle->produccion()->exists()) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['orden_produccion_insumo_id' => 'Este detalle de orden ya fue ejecutado en producción.']);
        }

        [$minObtenida, $maxObtenida] = $this->calcularRangoSalida((float) $detalle->cantidad, $regla);

        if ($regla->tipo_conversion === 'variable') {
            $cantidadObtenida = (float) ($data['cantidad_obtenida'] ?? 0);

            if ($cantidadObtenida < $minObtenida || $cantidadObtenida > $maxObtenida) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors([
                        'cantidad_obtenida' => 'Para regla variable, la cantidad obtenida debe estar entre '
                            . number_format($minObtenida, 2)
                            . ' y '
                            . number_format($maxObtenida, 2)
                            . '.',
                    ]);
            }
        } else {
            $cantidadObtenida = $minObtenida;
        }

        try {
            DB::transaction(function () use ($detalle, $regla, $cantidadObtenida) {
                $raizPayload = [
                    'orden_produccion_id' => $detalle->orden_produccion_id,
                    'orden_produccion_insumo_id' => $detalle->id,
                    'cantidad_insumo' => (float) $detalle->cantidad,
                    'cantidad_obtenida' => (float) $cantidadObtenida,
                    'estado' => 1,
                ];

                if ($this->produccionTieneColumna('regla_conversion_id')) {
                    $raizPayload['regla_conversion_id'] = $regla->id;
                }

                if ($this->produccionTieneColumna('nivel')) {
                    $raizPayload['nivel'] = 0;
                }

                $raiz = Produccion::create($raizPayload);

                $this->descontarStockInsumo($raiz, $detalle);
                $this->registrarSalidaProducto($raiz, $regla, $cantidadObtenida);
            });
        } catch (\RuntimeException $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['orden_produccion_insumo_id' => $e->getMessage()]);
        }

        return Redirect::route('producciones.index')
            ->with('success', 'Producción ejecutada correctamente.');
    }

    public function index(Request $request): View
    {
        $search = trim((string) $request->input('search'));

        $producciones = Produccion::query()
            ->conDetalleOrden()
            ->where('estado', 1)
            ->when($search, function ($query) use ($search) {
                $query->whereHas('ordenProduccion', function ($q) use ($search) {
                    $q->where('numero_orden', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('produccion.index', compact('producciones', 'search'));
    }

    public function show(Produccion $produccion): View
    {
        $relations = [
            'reglaConversion.insumo.unidadMedida',
            'reglaConversion.unidadEntrada',
            'reglaConversion.unidadSalida',
            'reglaConversion.producto',
            'ordenProduccion.insumos.insumo.unidadMedida',
            'ordenProduccionInsumo.insumo.unidadMedida',
            'insumos.unidadMedida',
            'productos.tipoProducto',
            'mermas',
        ];

        $produccion->loadMissing($relations);

        return view('produccion.show', compact('produccion'));
    }

    private function calcularRangoSalida(float $cantidadInsumo, ReglaConversion $regla): array
    {
        $base = max((float) $regla->cantidad_entrada, 0.0001);
        $min = ($cantidadInsumo / $base) * (float) $regla->rendimiento_min;
        $max = ($cantidadInsumo / $base) * (float) $regla->rendimiento_max;

        if ($max < $min) {
            [$min, $max] = [$max, $min];
        }

        return [round($min, 2), round($max, 2)];
    }

    private function descontarStockInsumo(Produccion $produccion, OrdenProduccionInsumo $detalle): void
    {
        $insumo = Insumo::whereKey($detalle->insumo_id)->lockForUpdate()->firstOrFail();

        $cantidadConsumida = (int) round((float) $detalle->cantidad);

        if ((int) $insumo->stock_actual < $cantidadConsumida) {
            throw new \RuntimeException(
                "Stock insuficiente para el insumo \"{$insumo->nombre}\". Disponible: {$insumo->stock_actual}, requerido: {$cantidadConsumida}."
            );
        }

        $insumo->decrement('stock_actual', $cantidadConsumida);

        $produccion->insumos()->syncWithoutDetaching([
            $insumo->id => [
                'cantidad' => $cantidadConsumida,
                'estado' => 1,
            ],
        ]);
    }

    private function registrarSalidaProducto(Produccion $produccion, ReglaConversion $regla, float $cantidadObtenida): void
    {
        if (!$regla->producto_id) {
            return;
        }

        Producto::query()
            ->whereKey($regla->producto_id)
            ->increment('cantidad_actual', (int) round($cantidadObtenida));

        $produccion->productos()->syncWithoutDetaching([
            $regla->producto_id => [
                'cantidad_producida' => round($cantidadObtenida, 2),
                'estado' => 1,
            ],
        ]);
    }

    private function reglaTieneColumna(string $columna): bool
    {
        return Schema::hasTable('reglas_conversion') && Schema::hasColumn('reglas_conversion', $columna);
    }

    private function produccionTieneColumna(string $columna): bool
    {
        return Schema::hasTable('producciones') && Schema::hasColumn('producciones', $columna);
    }

    private function reglaCoincideConDetalle(ReglaConversion $regla, OrdenProduccionInsumo $detalle): bool
    {
        if ($this->reglaTieneColumna('insumo_id') && !empty($regla->insumo_id)) {
            return (int) $regla->insumo_id === (int) $detalle->insumo_id;
        }

        if ($this->reglaTieneColumna('nombre_insumo')) {
            $nombreRegla = trim((string) ($regla->nombre_insumo ?? ''));
            $nombreDetalle = trim((string) ($detalle->insumo?->nombre ?? ''));

            if ($nombreRegla === '' || $nombreDetalle === '') {
                return false;
            }

            return mb_strtolower($nombreRegla) === mb_strtolower($nombreDetalle);
        }

        return false;
    }
}
