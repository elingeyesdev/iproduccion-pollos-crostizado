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
            'numero_orden' => 'nullable|string|max:255|unique:orden_producciones,numero_orden,' . $id,
            'fecha_programada' => 'required|date',
            'regla_conversion_id' => 'required|integer|exists:reglas_conversion,id',
            'insumos' => 'required|array|min:1',
            'insumos.*.insumo_id' => 'required|exists:insumos,id',
            'insumos.*.cantidad' => 'required|numeric|min:0.01',
        ];
    }

    public function attributes(): array
    {
        return [
            'numero_orden' => 'número de orden',
            'fecha_programada' => 'fecha programada',
            'regla_conversion_id' => 'receta / regla de conversión',
            'insumos' => 'insumos',
            'insumos.*.insumo_id' => 'insumo',
            'insumos.*.cantidad' => 'cantidad',
        ];
    }

    public function messages(): array
    {
        return [
            'numero_orden.string' => 'El número de orden debe ser un texto.',
            'numero_orden.max' => 'El número de orden no puede superar los 255 caracteres.',
            'numero_orden.unique' => 'Este número de orden ya está registrado.',

            'fecha_programada.required' => 'La fecha programada es obligatoria.',
            'fecha_programada.date' => 'La fecha programada debe ser una fecha válida.',

            'regla_conversion_id.required' => 'Debes seleccionar la receta (regla de conversión) de esta orden.',
            'regla_conversion_id.exists' => 'La receta seleccionada no existe.',

            'insumos.required' => 'Debes agregar al menos un insumo.',
            'insumos.array' => 'Los insumos deben ser una lista válida.',
            'insumos.min' => 'Debes agregar al menos :min insumo(s).',

            'insumos.*.insumo_id.required' => 'El insumo de la línea :position es obligatorio.',
            'insumos.*.insumo_id.exists' => 'El insumo seleccionado en la línea :position no existe.',

            'insumos.*.cantidad.required' => 'La cantidad de la línea :position es obligatoria.',
            'insumos.*.cantidad.numeric' => 'La cantidad de la línea :position debe ser un número.',
            'insumos.*.cantidad.min' => 'La cantidad de la línea :position debe ser mayor a 0.',
        ];
    }
}
