<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Producto extends Model
{
    protected $fillable = ['nombre', 'precio', 'tipo_producto_id', 'cantidad_actual', 'estado'];

    public function tipoProducto(): BelongsTo
    {
        return $this->belongsTo(TipoProducto::class);
    }

    public function componentes(): BelongsToMany
    {
        return $this->belongsToMany(ComponenteProducto::class, 'componente_producto_producto')
            ->withPivot(['cantidad', 'estado'])
            ->withTimestamps();
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

    public function producciones(): BelongsToMany
    {
        return $this->belongsToMany(Produccion::class, 'produccion_producto')
            ->withPivot(['cantidad_producida', 'estado'])
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
