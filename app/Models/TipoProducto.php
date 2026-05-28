<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoProducto extends Model
{
    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }
}
