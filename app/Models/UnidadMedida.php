<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class UnidadMedida
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $nombre
 * @property $abreviatura
 * @property $factor_conversion
 * @property $estado
 *
 * @property Insumo[] $insumos
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class UnidadMedida extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'abreviatura', 'factor_conversion', 'estado'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function insumos()
    {
        return $this->hasMany(\App\Models\Insumo::class, 'id', 'unidad_medida_id');
    }
    
}
