<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class CategoriaInsumo
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $nombre
 * @property $descripcion
 * @property $estado
 *
 * @property Insumo[] $insumos
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class CategoriaInsumo extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'descripcion', 'estado'];

    public static function searchAndPaginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('descripcion', 'like', "%{$search}%");
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
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function insumos()
    {
        return $this->hasMany(\App\Models\Insumo::class, 'categoria_insumo_id', 'id');
    }
    
}
