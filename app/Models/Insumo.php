<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Insumo
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $nombre
 * @property $categoria_insumo_id
 * @property $unidad_medida_id
 * @property $precioUnitario
 * @property $stockActual
 * @property $stockMinimo
 * @property $estado
 *
 * @property CategoriaInsumo $categoriaInsumo
 * @property UnidadMedida $unidadMedida
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Insumo extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'categoria_insumo_id', 'unidad_medida_id', 'precioUnitario', 'stockActual', 'stockMinimo', 'estado'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function categoriaInsumo()
    {
        return $this->belongsTo(\App\Models\CategoriaInsumo::class, 'categoria_insumo_id', 'id');
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function unidadMedida()
    {
        return $this->belongsTo(\App\Models\UnidadMedida::class, 'unidad_medida_id', 'id');
    }
    
}
