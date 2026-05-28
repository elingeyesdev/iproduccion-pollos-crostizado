<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IngresoInsumo extends Model
{
    protected $fillable = ['fecha_ingreso', 'descripcion', 'estado'];

    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    public function detalles(): HasMany
    {
        return $this->hasMany(IngresoInsumoDetalle::class);
    }

    public function insumos(): BelongsToMany
    {
        return $this->belongsToMany(Insumo::class, 'ingreso_insumo_detalle')
            ->withPivot(['cantidad', 'precio_unitario', 'estado'])
            ->withTimestamps();
    }
}
