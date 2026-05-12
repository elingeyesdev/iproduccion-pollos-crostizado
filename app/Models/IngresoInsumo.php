<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class IngresoInsumo extends Model
{
    protected $fillable = ['fecha_ingreso', 'descripcion'];

    protected $casts = [
        'fecha_ingreso' => 'date',
    ];

    public function detalles()
    {
        return $this->hasMany(IngresoInsumoDetalle::class);
    }

    public function insumos(): BelongsToMany
    {
        return $this->belongsToMany(Insumo::class, 'ingreso_insumo_detalle')
            ->withPivot(['cantidad', 'precio_unitario'])
            ->withTimestamps();
    }

    public static function searchAndPaginate(?string $search, int $perPage = 10): LengthAwarePaginator
    {
        return self::query()
            ->with(['detalles.insumo'])
            ->when($search, function ($query) use ($search) {
                $query->where('descripcion', 'like', "%{$search}%")
                    ->orWhere('fecha_ingreso', 'like', "%{$search}%");
            })
            ->orderByDesc('fecha_ingreso')
            ->paginate($perPage)
            ->withQueryString();
    }

    public static function createWithDetalles(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $ingreso = self::create([
                'fecha_ingreso' => $data['fecha_ingreso'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            $ingreso->applyDetalles($data['detalles']);

            return $ingreso;
        });
    }

    public function updateWithDetalles(array $data): void
    {
        DB::transaction(function () use ($data) {
            $this->rollbackStockFromCurrentDetalles();

            $this->update([
                'fecha_ingreso' => $data['fecha_ingreso'],
                'descripcion' => $data['descripcion'] ?? null,
            ]);

            $this->detalles()->delete();
            $this->applyDetalles($data['detalles']);
        });
    }

    public function deleteWithStockRollback(): void
    {
        DB::transaction(function () {
            $this->rollbackStockFromCurrentDetalles();
            $this->detalles()->delete();
            $this->delete();
        });
    }

    private function applyDetalles(array $detalles): void
    {
        foreach ($detalles as $detalle) {
            $this->detalles()->create([
                'insumo_id' => $detalle['insumo_id'],
                'cantidad' => $detalle['cantidad'],
                'precio_unitario' => $detalle['precio_unitario'],
            ]);

            $insumo = Insumo::find($detalle['insumo_id']);
            if ($insumo) {
                $insumo->stockActual = (int) $insumo->stockActual + (int) $detalle['cantidad'];
                $insumo->precioUnitario = $detalle['precio_unitario'];
                $insumo->save();
            }
        }
    }

    private function rollbackStockFromCurrentDetalles(): void
    {
        foreach ($this->detalles as $detalle) {
            $insumo = Insumo::find($detalle->insumo_id);
            if ($insumo) {
                $insumo->stockActual = max(0, (int) $insumo->stockActual - (int) $detalle->cantidad);
                $insumo->save();
            }
        }
    }
}
