<?php

namespace App\Http\Controllers;

use App\Models\Insumo;
use App\Models\OrdenProduccion;
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

        $producto = $regla->resolverProducto();

        try {
            DB::transaction(function () use ($detalle, $regla, $producto, $cantidadObtenida) {
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

                if ($this->produccionTieneColumna('producto_id')) {
                    $raizPayload['producto_id'] = $producto?->id;
                }

                if ($this->produccionTieneColumna('nivel')) {
                    $raizPayload['nivel'] = 0;
                }

                Produccion::create($raizPayload);

                $this->descontarStockInsumo($detalle);
                $this->registrarSalidaProducto($producto, $cantidadObtenida);
            });
        } catch (\RuntimeException $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['orden_produccion_insumo_id' => $e->getMessage()]);
        }

        return Redirect::route('producciones.index')
            ->with('success', 'Producción ejecutada correctamente.');
    }

    public function loteCreate(OrdenProduccion $ordenProduccion): View
    {
        $detalles = OrdenProduccionInsumo::query()
            ->where('orden_produccion_id', $ordenProduccion->id)
            ->where('estado', 1)
            ->whereDoesntHave('produccion')
            ->with('insumo.unidadMedida')
            ->orderBy('id')
            ->get()
            ->map(function (OrdenProduccionInsumo $detalle) {
                $regla = $this->autoMatchRegla($detalle);
                $producto = $regla ? $regla->resolverProducto() : null;

                return [
                    'detalle' => $detalle,
                    'regla' => $regla,
                    'producto' => $producto,
                ];
            });

        $productoIds = $detalles->pluck('producto.id')->filter()->unique();

        $loteEjecutable = $detalles->isNotEmpty()
            && $detalles->every(fn (array $linea) => $linea['regla'] && $linea['producto'])
            && $productoIds->count() === 1;

        return view('produccion.lote-create', compact('ordenProduccion', 'detalles', 'loteEjecutable'));
    }

    public function loteStore(Request $request, OrdenProduccion $ordenProduccion): RedirectResponse
    {
        $data = $request->validate([
            'cantidad_obtenida' => 'required|numeric|min:0.01',
        ]);

        $cantidadObtenida = (float) $data['cantidad_obtenida'];

        $detalles = OrdenProduccionInsumo::query()
            ->where('orden_produccion_id', $ordenProduccion->id)
            ->where('estado', 1)
            ->whereDoesntHave('produccion')
            ->with('insumo')
            ->orderBy('id')
            ->get();

        if ($detalles->isEmpty()) {
            return Redirect::back()
                ->withErrors(['cantidad_obtenida' => 'Esta orden no tiene insumos pendientes de producción.']);
        }

        $lineas = [];
        $productoIds = [];

        foreach ($detalles as $detalle) {
            $regla = $this->autoMatchRegla($detalle);

            if (!$regla) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors(['cantidad_obtenida' => "No hay una regla de conversión activa para el insumo \"{$detalle->insumo?->nombre}\"."]);
            }

            $producto = $regla->resolverProducto();

            if (!$producto) {
                return Redirect::back()
                    ->withInput()
                    ->withErrors(['cantidad_obtenida' => "No se pudo determinar el producto resultante para el insumo \"{$detalle->insumo?->nombre}\"."]);
            }

            $lineas[] = ['detalle' => $detalle, 'regla' => $regla, 'producto' => $producto];
            $productoIds[$producto->id] = true;
        }

        if (count($productoIds) > 1) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['cantidad_obtenida' => 'Las líneas de esta orden generan productos distintos; no se puede ejecutar como un solo lote.']);
        }

        $productoFinal = $lineas[0]['producto'];

        try {
            DB::transaction(function () use ($lineas, $cantidadObtenida, $ordenProduccion, $productoFinal) {
                foreach ($lineas as $linea) {
                    $raizPayload = [
                        'orden_produccion_id' => $ordenProduccion->id,
                        'orden_produccion_insumo_id' => $linea['detalle']->id,
                        'cantidad_insumo' => (float) $linea['detalle']->cantidad,
                        'cantidad_obtenida' => $cantidadObtenida,
                        'estado' => 1,
                    ];

                    if ($this->produccionTieneColumna('regla_conversion_id')) {
                        $raizPayload['regla_conversion_id'] = $linea['regla']->id;
                    }

                    if ($this->produccionTieneColumna('producto_id')) {
                        $raizPayload['producto_id'] = $linea['producto']->id;
                    }

                    if ($this->produccionTieneColumna('nivel')) {
                        $raizPayload['nivel'] = 0;
                    }

                    Produccion::create($raizPayload);

                    $this->descontarStockInsumo($linea['detalle']);
                }

                Producto::whereKey($productoFinal->id)->increment('cantidad_actual', (int) round($cantidadObtenida));
            });
        } catch (\RuntimeException $e) {
            return Redirect::back()
                ->withInput()
                ->withErrors(['cantidad_obtenida' => $e->getMessage()]);
        }

        return Redirect::route('orden-producciones.show', $ordenProduccion)
            ->with('success', 'Producción del lote ejecutada correctamente. Se descontaron ' . count($lineas) . ' insumo(s) y se generaron ' . number_format($cantidadObtenida, 2) . ' unidades de ' . $productoFinal->nombre . '.');
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
            'producto',
            'ordenProduccion.insumos.insumo.unidadMedida',
            'ordenProduccionInsumo.insumo.unidadMedida',
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

    private function descontarStockInsumo(OrdenProduccionInsumo $detalle): void
    {
        $insumo = Insumo::whereKey($detalle->insumo_id)->lockForUpdate()->firstOrFail();

        $cantidadConsumida = round((float) $detalle->cantidad, 2);

        if ((float) $insumo->stock_actual < $cantidadConsumida) {
            throw new \RuntimeException(
                "Stock insuficiente para el insumo \"{$insumo->nombre}\". Disponible: {$insumo->stock_actual}, requerido: {$cantidadConsumida}."
            );
        }

        $insumo->decrement('stock_actual', $cantidadConsumida);
    }

    private function registrarSalidaProducto(?Producto $producto, float $cantidadObtenida): void
    {
        if (!$producto) {
            return;
        }

        Producto::query()
            ->whereKey($producto->id)
            ->increment('cantidad_actual', (int) round($cantidadObtenida));
    }

    private function autoMatchRegla(OrdenProduccionInsumo $detalle): ?ReglaConversion
    {
        return ReglaConversion::with(['insumo', 'producto'])
            ->where('estado', 1)
            ->get()
            ->first(fn (ReglaConversion $regla) => $this->reglaCoincideConDetalle($regla, $detalle));
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
