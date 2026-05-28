<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleIngresoBebida extends Model
{
    protected $table = 'ingreso_bebida_producto';

    protected $fillable = ['ingreso_bebida_id', 'producto_id', 'cantidad_ingresa', 'precio', 'estado'];

    protected $casts = [
        'cantidad_ingresa' => 'decimal:2',
        'precio' => 'decimal:2',
    ];

    public function ingresoBebida(): BelongsTo
    {
        return $this->belongsTo(IngresoBebida::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
