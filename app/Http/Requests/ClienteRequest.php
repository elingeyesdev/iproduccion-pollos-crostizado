<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ClienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'telefono' => 'required|string|max:255',
            'estado' => 'nullable|integer|in:0,1',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del cliente',
            'telefono' => 'teléfono',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del cliente es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',

            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono debe ser un texto válido.',
            'telefono.max' => 'El teléfono no puede superar los 255 caracteres.',

            'estado.integer' => 'El estado debe ser un valor entero.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
