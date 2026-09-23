<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleReglaConversion extends Model
{
    protected $table = 'detalle_regla_conversion';

    protected $fillable = [
        'regla_conversion_id',
        'insumo_id',
        'unidad_medida_id',
        'cantidad',
        'observacion',
        'estado',
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
    ];

    public function reglaConversion(): BelongsTo
    {
        return $this->belongsTo(ReglaConversion::class);
    }

    public function insumo(): BelongsTo
    {
        return $this->belongsTo(Insumo::class);
    }

    public function unidadMedida(): BelongsTo
    {
        return $this->belongsTo(UnidadMedida::class);
    }
}
