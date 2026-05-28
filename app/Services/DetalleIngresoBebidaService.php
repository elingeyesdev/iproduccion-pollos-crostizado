<?php

namespace App\Services;

use App\Models\DetalleIngresoBebida;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class DetalleIngresoBebidaService
{
    public function getAll(): Collection
    {
        return DetalleIngresoBebida::query()
            ->where('estado', 1)
            ->orderByDesc('created_at')
            ->get();
    }

    public function getById(int $id): DetalleIngresoBebida
    {
        return DetalleIngresoBebida::query()->findOrFail($id);
    }

    public function create(array $data): DetalleIngresoBebida
    {
        try {
            return DB::transaction(function () use ($data) {
                $data['estado'] = $data['estado'] ?? 1;
                return DetalleIngresoBebida::create($data);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear detalle ingreso de bebidas: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(int $id, array $data): DetalleIngresoBebida
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $detalleIngresoBebida = $this->getById($id);
                $detalleIngresoBebida->update($data);
                return $detalleIngresoBebida->refresh();
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar detalle ingreso de bebidas: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $detalleIngresoBebida = $this->getById($id);
                return $detalleIngresoBebida->update(['estado' => 0]);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar detalle ingreso de bebidas: ' . $e->getMessage(), 0, $e);
        }
    }
}
