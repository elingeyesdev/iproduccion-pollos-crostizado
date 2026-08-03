<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Insumo extends Model
{
    protected $fillable = ['nombre', 'unidad_medida_id', 'stock_actual', 'stock_minimo', 'estado'];

    protected $casts = [
        'stock_actual' => 'decimal:2',
        'stock_minimo' => 'decimal:2',
    ];

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class);
    }

    public function ingresosInsumos(): BelongsToMany
    {
        return $this->belongsToMany(IngresoInsumo::class, 'ingreso_insumo_detalle')
            ->withPivot(['cantidad', 'precio_unitario', 'estado'])
            ->withTimestamps();
    }

}
