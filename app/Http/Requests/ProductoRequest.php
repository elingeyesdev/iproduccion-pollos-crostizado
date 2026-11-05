<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'precio' => 'required|numeric|min:0',
            'tipo_producto_id' => 'required|exists:tipo_productos,id',
            'cantidad_actual' => 'required|integer|min:0',
            'imagen' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'quitar_imagen' => 'nullable|boolean',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre del producto',
            'precio' => 'precio',
            'tipo_producto_id' => 'tipo de producto',
            'cantidad_actual' => 'cantidad actual',
            'imagen' => 'foto del producto',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'nombre.string' => 'El nombre debe ser un texto válido.',
            'nombre.max' => 'El nombre no puede superar los 255 caracteres.',

            'precio.required' => 'El precio es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número.',
            'precio.min' => 'El precio no puede ser negativo.',

            'tipo_producto_id.required' => 'Debes seleccionar un tipo de producto.',
            'tipo_producto_id.exists' => 'El tipo de producto seleccionado no es válido.',

            'cantidad_actual.required' => 'La cantidad actual es obligatoria.',
            'cantidad_actual.integer' => 'La cantidad actual debe ser un número entero.',
            'cantidad_actual.min' => 'La cantidad actual no puede ser negativa.',

            'imagen.image' => 'El archivo debe ser una imagen.',
            'imagen.mimes' => 'La foto debe ser JPG, PNG o WEBP.',
            'imagen.max' => 'La foto no puede pesar más de 2 MB.',
        ];
    }
}
