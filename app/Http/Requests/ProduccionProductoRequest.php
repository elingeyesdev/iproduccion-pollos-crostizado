<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduccionProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produccion_id' => 'required|exists:producciones,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_producida' => 'required|numeric|gt:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'produccion_id' => 'producción',
            'producto_id' => 'producto',
            'cantidad_producida' => 'cantidad producida',
        ];
    }

    public function messages(): array
    {
        return [
            'produccion_id.required' => 'Debes seleccionar una producción.',
            'produccion_id.exists' => 'La producción seleccionada no existe.',

            'producto_id.required' => 'Debes seleccionar un producto.',
            'producto_id.exists' => 'El producto seleccionado no existe.',

            'cantidad_producida.required' => 'La cantidad producida es obligatoria.',
            'cantidad_producida.numeric' => 'La cantidad producida debe ser un número.',
            'cantidad_producida.gt' => 'La cantidad producida debe ser mayor a 0.',
        ];
    }
}
