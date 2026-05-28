<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleInsumoProducto extends Model
{
    protected $table = 'produccion_insumo';

    protected $fillable = ['produccion_id', 'insumo_id', 'cantidad', 'estado'];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    public function produccion(): BelongsTo
    {
        return $this->belongsTo(Produccion::class);
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }
}
