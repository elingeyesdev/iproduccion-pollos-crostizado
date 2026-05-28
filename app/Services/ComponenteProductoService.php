<?php

namespace App\Services;

use App\Models\ComponenteProducto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ComponenteProductoService
{
    public function getAll(): Collection
    {
        return ComponenteProducto::query()
            ->where('estado', 1)
            ->orderBy('nombre')
            ->get();
    }

    public function getById(int $id): ComponenteProducto
    {
        return ComponenteProducto::query()->findOrFail($id);
    }

    public function create(array $data): ComponenteProducto
    {
        try {
            return DB::transaction(function () use ($data) {
                $productos = $data['productos'] ?? null;
                unset($data['productos']);

                $data['estado'] = $data['estado'] ?? 1;
                $componenteProducto = ComponenteProducto::create($data);

                if (is_array($productos)) {
                    $componenteProducto->productos()->sync($this->buildProductosSyncData($productos));
                }

                return $componenteProducto->refresh();
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al crear componente de producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function update(int $id, array $data): ComponenteProducto
    {
        try {
            return DB::transaction(function () use ($id, $data) {
                $componenteProducto = $this->getById($id);

                $hasProductos = array_key_exists('productos', $data);
                $productos = $data['productos'] ?? [];
                unset($data['productos']);

                $componenteProducto->update($data);

                if ($hasProductos) {
                    $componenteProducto->productos()->sync($this->buildProductosSyncData($productos));
                }

                return $componenteProducto->refresh();
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al actualizar componente de producto: ' . $e->getMessage(), 0, $e);
        }
    }

    public function delete(int $id): bool
    {
        try {
            return DB::transaction(function () use ($id) {
                $componenteProducto = $this->getById($id);
                return $componenteProducto->update(['estado' => 0]);
            });
        } catch (\Throwable $e) {
            throw new \Exception('Error al eliminar componente de producto: ' . $e->getMessage(), 0, $e);
        }
    }

    private function buildProductosSyncData(array $productos): array
    {
        $syncData = [];

        foreach ($productos as $key => $item) {
            if (is_array($item)) {
                $productoId = (int) ($item['producto_id'] ?? $key);
                if ($productoId <= 0) {
                    continue;
                }

                $syncData[$productoId] = [
                    'cantidad' => $item['cantidad'] ?? 0,
                    'estado' => $item['estado'] ?? 1,
                ];

                continue;
            }

            $productoId = is_numeric($item) ? (int) $item : (is_numeric($key) ? (int) $key : 0);
            if ($productoId <= 0) {
                continue;
            }

            $syncData[$productoId] = ['cantidad' => 0, 'estado' => 1];
        }

        return $syncData;
    }
}
