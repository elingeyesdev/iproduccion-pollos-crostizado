<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsumoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'unidad_medida_id' => 'required|exists:unidad_medidas,id',
            'stock_actual' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:0',
            'estado' => 'nullable|integer|in:0,1',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre',
            'unidad_medida_id' => 'unidad de medida',
            'stock_actual' => 'stock actual',
            'stock_minimo' => 'stock mínimo',
            'estado' => 'estado',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del insumo es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 150 caracteres.',

            'unidad_medida_id.required' => 'Debes seleccionar una unidad de medida.',
            'unidad_medida_id.exists' => 'La unidad de medida seleccionada no es válida.',

            'stock_actual.required' => 'El stock actual es obligatorio.',
            'stock_actual.numeric' => 'El stock actual debe ser un número.',
            'stock_actual.min' => 'El stock actual no puede ser negativo.',

            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.numeric' => 'El stock mínimo debe ser un número.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',

            'estado.integer' => 'El estado debe ser un valor entero.',
            'estado.in' => 'El estado seleccionado no es válido.',
        ];
    }
}
