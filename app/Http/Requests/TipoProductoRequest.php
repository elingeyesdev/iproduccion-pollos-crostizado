<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TipoProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:255',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del tipo de producto',
            'descripcion' => 'descripción',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del tipo de producto es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',

            'descripcion.string' => 'La descripción debe ser un texto válido.',
            'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',
        ];
    }
}
