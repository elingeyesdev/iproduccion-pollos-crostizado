<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReglaConversionRequest;
use App\Models\Insumo;
use App\Models\Producto;
use App\Models\ReglaConversion;
use App\Models\UnidadMedida;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ReglaConversionController extends Controller
{
    public function create(): View
    {
        $reglaConversion = new ReglaConversion();
        $insumos = Insumo::query()->where('estado', 1)->orderBy('nombre')->get();
        $productos = Producto::query()->where('estado', 1)->orderBy('nombre')->get();
        $unidades = UnidadMedida::query()->where('estado', 1)->orderBy('nombre')->get();

        return view('regla-conversion.create', compact('reglaConversion', 'insumos', 'productos', 'unidades'));
    }

    public function store(StoreReglaConversionRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $insumo = Insumo::findOrFail((int) $data['insumo_id']);
        $producto = Producto::findOrFail((int) $data['producto_id']);

        $payload = [
            'cantidad_entrada' => (float) $data['cantidad_entrada'],
            'rendimiento_min' => (float) $data['rendimiento_minimo'],
            'rendimiento_max' => (float) $data['rendimiento_maximo'],
            'tipo_conversion' => $data['tipo_conversion'],
            'estado' => 1,
        ];

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

        ReglaConversion::create($payload);

        return Redirect::route('regla-conversiones.index')
            ->with('success', 'Regla de conversión creada correctamente.');
    }

    public function index(Request $request): View
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

        return view('regla-conversion.index', compact('reglas', 'search'));
    }

    public function edit(ReglaConversion $reglaConversion): View
    {
        $insumos = Insumo::query()->where('estado', 1)->orderBy('nombre')->get();
        $productos = Producto::query()->where('estado', 1)->orderBy('nombre')->get();
        $unidades = UnidadMedida::query()->where('estado', 1)->orderBy('nombre')->get();

        return view('regla-conversion.edit', compact('reglaConversion', 'insumos', 'productos', 'unidades'));
    }

    public function update(StoreReglaConversionRequest $request, ReglaConversion $reglaConversion): RedirectResponse
    {
        $data = $request->validated();

        $insumo = Insumo::findOrFail((int) $data['insumo_id']);
        $producto = Producto::findOrFail((int) $data['producto_id']);

        $payload = [
            'cantidad_entrada' => (float) $data['cantidad_entrada'],
            'rendimiento_min' => (float) $data['rendimiento_minimo'],
            'rendimiento_max' => (float) $data['rendimiento_maximo'],
            'tipo_conversion' => $data['tipo_conversion'],
        ];

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

        $reglaConversion->update($payload);

        return Redirect::route('regla-conversiones.index')
            ->with('success', 'Regla de conversión actualizada correctamente.');
    }

    public function destroy(ReglaConversion $reglaConversion): RedirectResponse
    {
        // Eliminación lógica usando el campo estado
        $reglaConversion->update(['estado' => 0]);

        return Redirect::route('regla-conversiones.index')
            ->with('success', 'Regla de conversión eliminada correctamente.');
    }
}
