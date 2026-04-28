<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoFinalRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'nombre' => 'required|string|max:120',
			'descripcion' => 'required|string|max:255',
			'precio' => 'required|numeric|min:0',
			'estado' => 'required|integer|in:0,1',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'descripcion' => 'descripción',
            'precio' => 'precio',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'in' => 'El valor de :attribute no es válido.',
            'min' => 'El campo :attribute no puede ser negativo.',
            'max' => 'El campo :attribute excede el límite permitido.',
        ];
    }
}
