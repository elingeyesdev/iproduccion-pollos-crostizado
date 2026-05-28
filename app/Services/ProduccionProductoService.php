<?php

namespace App\Services;

use App\Models\ProduccionProducto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ProduccionProductoService
{
    public function getAll(): Collection
    {
        return ProduccionProducto::query()
            ->where('estado', 1)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getById(int $id): ProduccionProducto
    {
        return ProduccionProducto::query()->findOrFail($id);
    }

    public function create(array $data): ProduccionProducto
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['estado'] = $data['estado'] ?? 1;
                return ProduccionProducto::create($data);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear produccion-producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(int $id, array $data): ProduccionProducto
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $produccionProducto = $this->getById($id);
                $produccionProducto->update($data);
                return $produccionProducto->refresh();
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar produccion-producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $produccionProducto = $this->getById($id);
                return $produccionProducto->update(['estado' => 0]);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar produccion-producto: ' . $e->getMessage(), 0, $e);
        }
    }
}
