<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class Cliente
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $nombre
 * @property $telefono
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Cliente extends Model
{
    
    protected $perPage = 20;
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['nombre', 'telefono'];

    public static function searchAndPaginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return self::query()
            ->when($search, function ($query) use ($search) {
                $query->where('nombre', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
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


}
