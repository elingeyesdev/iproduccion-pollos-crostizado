<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngresoInsumoDetalle extends Model
{
    protected $table = 'ingreso_insumo_detalle';

    protected $fillable = ['ingreso_insumo_id', 'insumo_id', 'cantidad', 'precio_unitario'];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'precio_unitario' => 'decimal:2',
    ];

    public function ingresoInsumo(): BelongsTo
    {
        return $this->belongsTo(IngresoInsumo::class);
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }
}
