<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Venta
 *
 * @property $id
 * @property $created_at
 * @property $updated_at
 * @property $cliente_id
 * @property $observaciones
 * @property $total
 * @property $estado
 * @property $opcion_pago
 *
 * @property Cliente $cliente
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Venta extends Model
{
    
    protected $perPage = 20;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['cliente_id', 'observaciones', 'total', 'estado', 'opcion_pago'];


    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function cliente()
    {
        return $this->belongsTo(\App\Models\Cliente::class, 'cliente_id', 'id');
    }

    public static function createFromData(array $data): self
    {
        return self::create($data);
    }

    public static function findByIdOrFail(int|string $id): self
    {
        return self::query()->findOrFail($id);
    }

    public function updateFromData(array $data): void
    {
        $this->update($data);
    }

    public static function deleteById(int|string $id): void
    {
        $venta = self::findByIdOrFail($id);
        $venta->delete();
    }
    
}
