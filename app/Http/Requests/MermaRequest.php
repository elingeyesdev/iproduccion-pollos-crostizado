<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MermaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produccion_id' => 'required|exists:producciones,id',
            'cantidad' => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'produccion_id' => 'producción',
            'cantidad' => 'cantidad',
        ];
    }

    public function messages(): array
    {
        return [
            'produccion_id.required' => 'Debes seleccionar una producción.',
            'produccion_id.exists' => 'La producción seleccionada no existe.',

            'cantidad.required' => 'La cantidad de merma es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un número.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
        ];
    }
}
