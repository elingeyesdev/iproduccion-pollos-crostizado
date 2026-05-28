<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ComponenteProducto extends Model
{
    protected $fillable = ['nombre', 'equivalencia', 'estado'];

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'componente_producto_producto')
            ->withPivot(['cantidad', 'estado'])
            ->withTimestamps();
    }
}
