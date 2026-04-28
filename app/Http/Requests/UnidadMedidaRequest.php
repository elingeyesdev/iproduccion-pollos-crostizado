<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnidadMedidaRequest extends FormRequest
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
			'nombre' => 'required|string|max:100',
			'abreviatura' => 'required|string|max:20',
			'factor_conversion' => 'required|numeric|min:0.0001',
			'estado' => 'required|integer|in:0,1',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'abreviatura' => 'abreviatura',
            'factor_conversion' => 'factor de conversión',
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
            'min' => 'El campo :attribute debe ser mayor que 0.',
            'max' => 'El campo :attribute excede el límite permitido.',
        ];
    }
}
