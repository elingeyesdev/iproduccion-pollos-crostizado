<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class IngresoBebida extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'fecha_ingreso', 'estado'];

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'ingreso_bebida_producto')
            ->withPivot(['cantidad_ingresa', 'precio', 'estado'])
            ->withTimestamps();
    }
}
