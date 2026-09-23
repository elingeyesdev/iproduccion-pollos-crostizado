<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenProduccion extends Model
{
    protected $table = 'orden_producciones';

    protected $fillable = ['numero_orden', 'fecha_programada', 'regla_conversion_id', 'observaciones', 'estado'];

    public function producciones(): HasMany
    {
        return $this->hasMany(Produccion::class);
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(OrdenProduccionInsumo::class);
    }

    public function reglaConversion(): BelongsTo
    {
        return $this->belongsTo(ReglaConversion::class);
    }
}
