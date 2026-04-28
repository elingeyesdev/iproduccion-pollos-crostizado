<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsumoRequest extends FormRequest
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
			'nombre' => 'required|string|max:150',
            'categoria_insumo_id' => 'required|exists:categoria_insumos,id',
			'unidad_medida_id' => 'required|exists:unidad_medidas,id',
			'precioUnitario' => 'required|numeric|min:0',
			'stockActual' => 'required|integer|min:0',
			'stockMinimo' => 'required|integer|min:0',
			'estado' => 'required|integer|in:0,1',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'categoria_insumo_id' => 'categoría',
            'unidad_medida_id' => 'unidad de medida',
            'precioUnitario' => 'precio unitario',
            'stockActual' => 'stock actual',
            'stockMinimo' => 'stock mínimo',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'exists' => 'La :attribute seleccionada no existe.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'integer' => 'El campo :attribute debe ser un número entero.',
            'in' => 'El valor de :attribute no es válido.',
            'min' => 'El campo :attribute debe ser mayor o igual a 0.',
            'max' => 'El campo :attribute excede el límite permitido.',
        ];
    }
}
