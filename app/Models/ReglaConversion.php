<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReglaConversion extends Model
{
    protected $table = 'reglas_conversion';

    protected $fillable = [
        'insumo_id',
        'nombre_insumo',
        'unidad_entrada_id',
        'cantidad_entrada',
        'producto_id',
        'producto_resultante',
        'unidad_salida_id',
        'rendimiento_min',
        'rendimiento_max',
        'tipo_conversion',
        'insumo_resultante_id',
        'estado',
    ];

    protected $casts = [
        'cantidad_entrada' => 'decimal:2',
        'rendimiento_min' => 'decimal:2',
        'rendimiento_max' => 'decimal:2',
    ];

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    public function unidadEntrada(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_entrada_id');
    }

    public function unidadSalida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class, 'unidad_salida_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function insumoResultante(): BelongsTo
    {
        return $this->belongsTo(Insumo::class, 'insumo_resultante_id');
    }

    public function producciones(): HasMany
    {
        return $this->hasMany(Produccion::class, 'regla_conversion_id');
    }

    public function recetaConversiones(): HasMany
    {
        return $this->hasMany(DetalleReglaConversion::class)->where('estado', 1);
    }

    public function rangoRendimiento(float $cantidadPlanificada): array
    {
        return [
            'minimo' => $cantidadPlanificada * (float) $this->rendimiento_min,
            'maximo' => $cantidadPlanificada * (float) $this->rendimiento_max,
        ];
    }

    public function aceptaCantidadObtenida(float $cantidad, float $cantidadPlanificada): bool
    {
        $rango = $this->rangoRendimiento($cantidadPlanificada);

        return $cantidad >= $rango['minimo'] && $cantidad <= $rango['maximo'];
    }

    public function mensajeRendimiento(float $cantidad, float $cantidadPlanificada): string
    {
        $rango = $this->rangoRendimiento($cantidadPlanificada);

        return sprintf(
            'La cantidad obtenida de %.2f está fuera del rendimiento permitido para %.2f planificados. Debe estar entre %.2f y %.2f.',
            $cantidad,
            $cantidadPlanificada,
            $rango['minimo'],
            $rango['maximo']
        );
    }

    public function resolverProducto(): ?Producto
    {
        if ($this->producto) {
            return $this->producto;
        }

        $nombreProducto = trim((string) ($this->producto_resultante ?? ''));

        if ($nombreProducto === '') {
            return null;
        }

        return Producto::whereRaw('LOWER(TRIM(nombre)) = ?', [mb_strtolower($nombreProducto)])->first();
    }
}
