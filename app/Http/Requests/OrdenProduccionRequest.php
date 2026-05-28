<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrdenProduccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $insumos = collect($this->input('insumos', []))
            ->filter(function ($row) {
                return filled($row['insumo_id'] ?? null) || filled($row['cantidad'] ?? null);
            })
            ->values()
            ->all();

        $this->merge([
            'insumos' => $insumos,
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('orden_produccione')?->id ?? $this->route('orden_produccion')?->id;

        return [
            'numero_orden' => 'required|string|max:255|unique:orden_producciones,numero_orden,' . $id,
            'fecha_programada' => 'required|date',
            'insumos' => 'required|array|min:1',
            'insumos.*.insumo_id' => 'required|exists:insumos,id',
            'insumos.*.cantidad' => 'required|numeric|min:0.01',
        ];
    }
}
