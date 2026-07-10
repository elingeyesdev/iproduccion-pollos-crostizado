<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Produccion extends Model
{
    protected $table = 'producciones';

    protected $fillable = [
        'orden_produccion_id',
        'orden_produccion_insumo_id',
        'regla_conversion_id',
        'receta_id',
        'cantidad_insumo',
        'cantidad_obtenida',
        'nivel',
        'estado',
    ];

    protected $casts = [
        'cantidad_insumo' => 'decimal:2',
        'cantidad_obtenida' => 'decimal:2',
    ];

    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class);
    }

    public function ordenProduccionInsumo(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccionInsumo::class);
    }

    public function reglaConversion(): BelongsTo
    {
        return $this->belongsTo(ReglaConversion::class, 'regla_conversion_id');
    }

    public function insumos(): BelongsToMany
    {
        return $this->belongsToMany(Insumo::class, 'produccion_insumo')
            ->withPivot(['cantidad', 'estado'])
            ->withTimestamps();
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'produccion_producto')
            ->withPivot(['cantidad_producida', 'estado'])
            ->withTimestamps();
    }

    public function mermas(): HasMany
    {
        return $this->hasMany(Merma::class);
    }

    // Eager load del detalle planificado en la orden asociada.
    public function scopeConDetalleOrden(Builder $query): Builder
    {
        return $query->with([
            'reglaConversion.insumo.unidadMedida',
            'reglaConversion.unidadEntrada',
            'reglaConversion.unidadSalida',
            'reglaConversion.producto',
            'ordenProduccion.insumos.insumo.unidadMedida',
            'ordenProduccionInsumo.insumo.unidadMedida',
        ]);
    }

    public function getInsumosPlanificadosAttribute(): Collection
    {
        $this->loadMissing('ordenProduccion.insumos.insumo.unidadMedida');

        return $this->ordenProduccion?->insumos ?? collect();
    }

    public function getDetalleOrdenAttribute(): ?OrdenProduccionInsumo
    {
        $this->loadMissing('ordenProduccionInsumo.insumo.unidadMedida');

        return $this->ordenProduccionInsumo;
    }
}
