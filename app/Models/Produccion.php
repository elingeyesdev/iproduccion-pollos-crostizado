<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Produccion extends Model
{
    protected $table = 'producciones';

    protected $fillable = ['orden_produccion_id', 'cantidad_insumo', 'cantidad_obtenida', 'estado'];

    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class);
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
}
