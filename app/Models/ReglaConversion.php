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
}
