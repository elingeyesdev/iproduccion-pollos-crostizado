<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngresoInsumoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_ingreso' => ['required', 'date'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.insumo_id' => ['required', 'exists:insumos,id'],
            'detalles.*.cantidad' => ['required', 'numeric', 'min:0.01'],
            'detalles.*.precio_unitario' => ['required', 'numeric', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'fecha_ingreso' => 'fecha de ingreso',
            'descripcion' => 'descripción',
            'detalles' => 'detalles',
            'detalles.*.insumo_id' => 'insumo',
            'detalles.*.cantidad' => 'cantidad',
            'detalles.*.precio_unitario' => 'precio unitario',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'date' => 'El campo :attribute debe ser una fecha válida.',
            'exists' => 'El :attribute seleccionado no existe.',
            'min' => 'El campo :attribute debe ser mayor o igual a :min.',
            'array' => 'El campo :attribute debe ser una lista válida.',
            'numeric' => 'El campo :attribute debe ser numérico.',
            'max' => 'El campo :attribute excede el límite permitido.',
        ];
    }
}
