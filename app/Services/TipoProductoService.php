<?php

namespace App\Services;

use App\Models\TipoProducto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class TipoProductoService
{
    public function getAll(): Collection
    {
        return TipoProducto::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();
    }

    public function getById(int $id): TipoProducto
    {
        return TipoProducto::query()->findOrFail($id);
    }

    public function create(array $data): TipoProducto
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['estado'] = $data['estado'] ?? 1;
                return TipoProducto::create($data);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear tipo de producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(int $id, array $data): TipoProducto
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $tipoProducto = $this->getById($id);
                $tipoProducto->update($data);
                return $tipoProducto->refresh();
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar tipo de producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $tipoProducto = $this->getById($id);
                return $tipoProducto->update(['estado' => 0]);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar tipo de producto: ' . $e->getMessage(), 0, $e);
        }
    }
}
