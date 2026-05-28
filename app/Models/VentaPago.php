<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VentaPago extends Model
{
    protected $table = 'venta_pagos';

    protected $fillable = ['venta_id', 'metodo_pago', 'monto', 'referencia', 'estado'];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }
}
