<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;

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

    public static function searchAndPaginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return self::query()
            ->with([
                'categoriaInsumo:id,nombre',
                'unidadMedida:id,nombre,abreviatura',
            ])
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhereHas('categoriaInsumo', function ($relatedQuery) use ($search) {
                        $relatedQuery->where('nombre', 'like', "%{$search}%");
                    })
                    ->orWhereHas('unidadMedida', function ($relatedQuery) use ($search) {
                        $relatedQuery->where('nombre', 'like', "%{$search}%")
                            ->orWhere('abreviatura', 'like', "%{$search}%");
                    });
            })
            ->orderBy('nombre')
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function createFromData(array $data): self
    {
        return self::create($data);
    }

    public function updateFromData(array $data): void
    {
        $this->update($data);
    }

    public function deleteRecord(): void
    {
        $this->delete();
    }


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

    public function ingresosInsumos(): BelongsToMany
    {
        return $this->belongsToMany(IngresoInsumo::class, 'ingreso_insumo_detalle')
            ->withPivot(['cantidad', 'precio_unitario'])
            ->withTimestamps();
    }
    
}
