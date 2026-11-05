<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $fillable = ['nombre', 'imagen', 'precio', 'tipo_producto_id', 'cantidad_actual', 'estado'];

    // URL pública de la foto (disco "public"), o null si el producto no tiene foto.
    public function getImagenUrlAttribute(): ?string
    {
        return $this->imagen ? asset('storage/' . $this->imagen) : null;
    }

    public function tipoProducto(): BelongsTo
    {
        return $this->belongsTo(TipoProducto::class);
    }

    // Un producto (combo) está compuesto por otros productos
    public function ingredientes(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'composicion_productos', 'producto_padre_id', 'producto_hijo_id')
            ->withPivot(['cantidad', 'estado'])
            ->wherePivot('estado', 1)
            ->withTimestamps();
    }

    // Un producto puede ser parte de varios combos
    public function esParteDe(): BelongsToMany
    {
        return $this->belongsToMany(Producto::class, 'composicion_productos', 'producto_hijo_id', 'producto_padre_id')
            ->withPivot(['cantidad', 'estado'])
            ->wherePivot('estado', 1)
            ->withTimestamps();
    }

    public function ventas(): BelongsToMany
    {
        return $this->belongsToMany(Venta::class, 'detalle_ventas')
            ->withPivot(['precio_unitario', 'cantidad', 'sub_total', 'estado'])
            ->withTimestamps();
    }

    public function ingresoBebidas(): BelongsToMany
    {
        return $this->belongsToMany(IngresoBebida::class, 'ingreso_bebida_producto')
            ->withPivot(['cantidad_ingresa', 'precio', 'estado'])
            ->withTimestamps();
    }
}
