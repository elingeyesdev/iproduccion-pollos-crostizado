<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetalleIngresoBebidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ingreso_bebida_id' => 'required|exists:ingreso_bebidas,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_ingresa' => 'required|numeric|gt:0',
            'precio' => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'ingreso_bebida_id' => 'ingreso de bebida',
            'producto_id' => 'bebida / producto',
            'cantidad_ingresa' => 'cantidad ingresada',
            'precio' => 'precio',
        ];
    }

    public function messages(): array
    {
        return [
            'ingreso_bebida_id.required' => 'Debes seleccionar un ingreso de bebida.',
            'ingreso_bebida_id.exists' => 'El ingreso de bebida seleccionado no existe.',

            'producto_id.required' => 'Debes seleccionar una bebida.',
            'producto_id.exists' => 'La bebida seleccionada no existe.',

            'cantidad_ingresa.required' => 'La cantidad ingresada es obligatoria.',
            'cantidad_ingresa.numeric' => 'La cantidad ingresada debe ser un número.',
            'cantidad_ingresa.gt' => 'La cantidad ingresada debe ser mayor a 0.',

            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',
        ];
    }
}
