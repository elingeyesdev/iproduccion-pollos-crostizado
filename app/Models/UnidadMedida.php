<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnidadMedida extends Model
{
    protected $fillable = ['nombre', 'abreviatura', 'factor_conversion', 'estado'];

    public function insumos(): HasMany
    {
        return $this->hasMany(Insumo::class);
    }
}
