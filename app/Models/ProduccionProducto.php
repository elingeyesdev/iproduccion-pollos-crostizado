<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProduccionProducto extends Model
{
    protected $table = 'produccion_producto';

    protected $fillable = ['produccion_id', 'producto_id', 'cantidad_producida', 'estado'];

    protected $casts = [
        'cantidad_producida' => 'decimal:2',
    ];

    public function produccion(): BelongsTo
    {
        return $this->belongsTo(Produccion::class);
    }

    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}
