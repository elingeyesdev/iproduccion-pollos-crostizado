<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetalleInsumoProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produccion_id' => 'required|exists:producciones,id',
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad' => 'required|numeric|gt:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'produccion_id' => 'producción',
            'insumo_id' => 'insumo',
            'cantidad' => 'cantidad',
        ];
    }

    public function messages(): array
    {
        return [
            'produccion_id.required' => 'Debes seleccionar una producción.',
            'produccion_id.exists' => 'La producción seleccionada no existe.',

            'insumo_id.required' => 'Debes seleccionar un insumo.',
            'insumo_id.exists' => 'El insumo seleccionado no existe.',

            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un número.',
            'cantidad.gt' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}
