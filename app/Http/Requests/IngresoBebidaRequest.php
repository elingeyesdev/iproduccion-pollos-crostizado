<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IngresoBebidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'                        => 'nullable|string|max:255',
            'descripcion'                   => 'nullable|string|max:255',
            'fecha_ingreso'                 => 'required|date',
            'productos'                     => 'required|array|min:1',
            'productos.*.producto_id'       => 'required|exists:productos,id',
            'productos.*.cantidad_ingresa'  => 'required|numeric|min:0.01',
            'productos.*.precio'            => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'nombre' => 'nombre / lote',
            'descripcion' => 'descripción',
            'fecha_ingreso' => 'fecha de ingreso',
            'productos' => 'bebidas',
            'productos.*.producto_id' => 'bebida',
            'productos.*.cantidad_ingresa' => 'cantidad ingresada',
            'productos.*.precio' => 'precio',
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.string' => 'El nombre del lote debe ser un texto.',
            'nombre.max' => 'El nombre del lote no puede superar los 255 caracteres.',

            'descripcion.string' => 'La descripción debe ser un texto.',
            'descripcion.max' => 'La descripción no puede superar los 255 caracteres.',

            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'fecha_ingreso.date' => 'La fecha de ingreso debe ser una fecha válida.',

            'productos.required' => 'Debes agregar al menos una bebida.',
            'productos.array' => 'Las bebidas deben ser una lista válida.',
            'productos.min' => 'Debes agregar al menos :min bebida(s).',

            'productos.*.producto_id.required' => 'La bebida de la línea :position es obligatoria.',
            'productos.*.producto_id.exists' => 'La bebida seleccionada en la línea :position no existe.',

            'productos.*.cantidad_ingresa.required' => 'La cantidad de la línea :position es obligatoria.',
            'productos.*.cantidad_ingresa.numeric' => 'La cantidad de la línea :position debe ser un número.',
            'productos.*.cantidad_ingresa.min' => 'La cantidad de la línea :position debe ser mayor a 0.',

            'productos.*.precio.required' => 'El precio de la línea :position es obligatorio.',
            'productos.*.precio.numeric' => 'El precio de la línea :position debe ser un número.',
            'productos.*.precio.min' => 'El precio de la línea :position no puede ser negativo.',
        ];
    }
}
