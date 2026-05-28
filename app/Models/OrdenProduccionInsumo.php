<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrdenProduccionInsumo extends Model
{
    protected $table = 'orden_produccion_insumo';

    protected $fillable = ['orden_produccion_id', 'insumo_id', 'cantidad', 'estado'];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    public function ordenProduccion(): BelongsTo
    {
        return $this->belongsTo(OrdenProduccion::class);
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }
}
