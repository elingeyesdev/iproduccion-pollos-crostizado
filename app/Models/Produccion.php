<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Collection;

class Produccion extends Model
{
    protected $table = 'producciones';

    protected $fillable = [
        'orden_produccion_insumo_id',
        'regla_conversion_id',
        'receta_id',
        'producto_id',
        'cantidad_insumo',
        'cantidad_obtenida',
        'nivel',
        'estado',
    ];

    protected $casts = [
        'cantidad_insumo' => 'decimal:2',
        'cantidad_obtenida' => 'decimal:2',
    ];

    public function ordenProduccion(): HasOneThrough
    {
        return $this->hasOneThrough(
            OrdenProduccion::class,
            OrdenProduccionInsumo::class,
            'id',                          // orden_produccion_insumo.id
            'id',                          // orden_producciones.id
            'orden_produccion_insumo_id',  // producciones.orden_produccion_insumo_id
            'orden_produccion_id'          // orden_produccion_insumo.orden_produccion_id
        );
    }

    public function ordenProduccionInsumo(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccionInsumo::class);
    }

    public function reglaConversion(): BelongsTo
    {
        return $this->belongsTo(ReglaConversion::class, 'regla_conversion_id');
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }

    public function mermas(): HasMany
    {
        return $this->hasMany(Merma::class);
    }

    // Un lote ejecutado crea una fila por insumo de la orden en la misma transacción.
    public function scopeDelMismoLote(Builder $query, Produccion $produccion): Builder
    {
        $produccion->loadMissing('ordenProduccionInsumo');

        return $query
            ->whereHas('ordenProduccionInsumo', fn ($q) => $q->where(
                'orden_produccion_id',
                $produccion->ordenProduccionInsumo?->orden_produccion_id
            ))
            ->where('created_at', $produccion->created_at);
    }

    // Eager load del detalle planificado en la orden asociada.
    public function scopeConDetalleOrden(Builder $query): Builder
    {
        return $query->with([
            'reglaConversion.insumo.unidadMedida',
            'reglaConversion.unidadEntrada',
            'reglaConversion.unidadSalida',
            'reglaConversion.producto',
            'producto',
            'ordenProduccion.insumos.insumo.unidadMedida',
            'ordenProduccionInsumo.insumo.unidadMedida',
        ]);
    }

    public function getInsumosPlanificadosAttribute(): Collection
    {
        $this->loadMissing('ordenProduccionInsumo.ordenProduccion.insumos.insumo.unidadMedida');

        return $this->ordenProduccionInsumo?->ordenProduccion?->insumos ?? collect();
    }

    public function getDetalleOrdenAttribute(): ?OrdenProduccionInsumo
    {
        $this->loadMissing('ordenProduccionInsumo.insumo.unidadMedida');

        return $this->ordenProduccionInsumo;
    }
}
