<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnidadMedidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'abreviatura' => 'required|string|max:50',
            'factor_conversion' => 'required|numeric|min:0',
            'estado' => 'nullable|integer|in:0,1',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre de la unidad',
            'abreviatura' => 'abreviatura',
            'factor_conversion' => 'factor de conversión',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la unidad de medida es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',

            'abreviatura.required' => 'La abreviatura es obligatoria.',
            'abreviatura.string' => 'La abreviatura debe ser un texto válido.',
            'abreviatura.max' => 'La abreviatura no puede superar los 50 caracteres.',

            'factor_conversion.required' => 'El factor de conversión es obligatorio.',
            'factor_conversion.numeric' => 'El factor de conversión debe ser un número.',
            'factor_conversion.min' => 'El factor de conversión no puede ser negativo.',

            'estado.integer' => 'El estado debe ser un valor entero.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
