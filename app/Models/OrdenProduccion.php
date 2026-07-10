<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OrdenProduccion extends Model
{
    protected $table = 'orden_producciones';

    protected $fillable = ['numero_orden', 'fecha_programada', 'observaciones', 'estado'];

    public function producciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Produccion::class);
    }

    public function insumos(): HasMany
    {
        return $this->hasMany(OrdenProduccionInsumo::class);
    }
}
