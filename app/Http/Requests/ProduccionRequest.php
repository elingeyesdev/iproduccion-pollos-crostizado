<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orden_produccion_insumo_id' => 'required|exists:orden_produccion_insumo,id',
            'insumo_id' => 'required|exists:insumos,id',
            'regla_conversion_id' => 'required|exists:regla_conversiones,id',
            'cantidad_insumo' => 'required|numeric|min:0.01',
            'cantidad_obtenida' => 'required|numeric|min:0.01',
        ];
    }

    public function attributes(): array
    {
        return [
            'orden_produccion_insumo_id' => 'detalle de insumo de la orden de producción',
            'insumo_id' => 'insumo',
            'regla_conversion_id' => 'regla de conversión',
            'cantidad_insumo' => 'cantidad de insumo',
            'cantidad_obtenida' => 'cantidad obtenida',
        ];
    }

    public function messages(): array
    {
        return [
            'orden_produccion_insumo_id.required' => 'Debes seleccionar un detalle de insumo de la orden de producción.',
            'orden_produccion_insumo_id.exists' => 'El detalle de insumo de la orden de producción seleccionado no existe.',

            'insumo_id.required' => 'Debes seleccionar un insumo.',
            'insumo_id.exists' => 'El insumo seleccionado no existe.',

            'regla_conversion_id.required' => 'Debes seleccionar una regla de conversión.',
            'regla_conversion_id.exists' => 'La regla de conversión seleccionada no existe.',

            'cantidad_insumo.required' => 'La cantidad de insumo es obligatoria.',
            'cantidad_insumo.numeric' => 'La cantidad de insumo debe ser un número.',
            'cantidad_insumo.min' => 'La cantidad de insumo debe ser mayor a 0.',

            'cantidad_obtenida.required' => 'La cantidad obtenida es obligatoria.',
            'cantidad_obtenida.numeric' => 'La cantidad obtenida debe ser un número.',
            'cantidad_obtenida.min' => 'La cantidad obtenida debe ser mayor a 0.',
        ];
    }
}
