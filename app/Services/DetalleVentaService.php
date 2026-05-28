<?php

namespace App\Services;

use App\Models\DetalleVenta;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DetalleVentaService
{
    public function getAll(): Collection
    {
        return DetalleVenta::query()
            ->where('estado', 1)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getById(int $id): DetalleVenta
    {
        return DetalleVenta::query()->findOrFail($id);
    }

    public function create(array $data): DetalleVenta
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['estado'] = $data['estado'] ?? 1;
                return DetalleVenta::create($data);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear detalle de venta: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(int $id, array $data): DetalleVenta
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $detalleVenta = $this->getById($id);
                $detalleVenta->update($data);
                return $detalleVenta->refresh();
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar detalle de venta: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $detalleVenta = $this->getById($id);
                return $detalleVenta->update(['estado' => 0]);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar detalle de venta: ' . $e->getMessage(), 0, $e);
        }
    }
}
