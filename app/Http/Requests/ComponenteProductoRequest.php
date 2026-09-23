<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComponenteProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'equivalencia' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del componente',
            'equivalencia' => 'equivalencia',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del componente es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',

            'equivalencia.string' => 'La equivalencia debe ser un texto válido.',
            'equivalencia.max' => 'La equivalencia no puede superar los 255 caracteres.',
        ];
    }
}
