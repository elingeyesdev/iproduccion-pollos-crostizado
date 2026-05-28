<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venta extends Model
{
    protected $fillable = ['cliente_id', 'fecha_venta', 'total', 'estado', 'descuento'];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function productos(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'detalle_ventas')
            ->withPivot(['precio_unitario', 'cantidad', 'sub_total', 'estado'])
            ->withTimestamps();
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(VentaPago::class);
    }
}
