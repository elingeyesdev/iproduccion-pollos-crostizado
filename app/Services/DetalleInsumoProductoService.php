<?php

namespace App\Services;

use App\Models\DetalleInsumoProducto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DetalleInsumoProductoService
{
    public function getAll(): Collection
    {
        return DetalleInsumoProducto::query()
            ->where('estado', 1)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getById(int $id): DetalleInsumoProducto
    {
        return DetalleInsumoProducto::query()->findOrFail($id);
    }

    public function create(array $data): DetalleInsumoProducto
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['estado'] = $data['estado'] ?? 1;
                return DetalleInsumoProducto::create($data);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear detalle insumo-producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(int $id, array $data): DetalleInsumoProducto
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $detalle = $this->getById($id);
                $detalle->update($data);
                return $detalle->refresh();
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar detalle insumo-producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $detalle = $this->getById($id);
                return $detalle->update(['estado' => 0]);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar detalle insumo-producto: ' . $e->getMessage(), 0, $e);
        }
    }
}
