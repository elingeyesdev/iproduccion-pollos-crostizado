<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetalleVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'venta_id' => 'required|exists:ventas,id',
            'producto_id' => 'required|exists:productos,id',
            'precio_unitario' => 'required|numeric|gte:0',
            'cantidad' => 'required|numeric|gt:0',
            'sub_total' => 'required|numeric|gte:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'venta_id' => 'venta',
            'producto_id' => 'producto',
            'precio_unitario' => 'precio unitario',
            'cantidad' => 'cantidad',
            'sub_total' => 'subtotal',
        ];
    }

    public function messages(): array
    {
        return [
            'venta_id.required' => 'Debes seleccionar una venta.',
            'venta_id.exists' => 'La venta seleccionada no existe.',

            'producto_id.required' => 'Debes seleccionar un producto.',
            'producto_id.exists' => 'El producto seleccionado no existe.',

            'precio_unitario.required' => 'El precio unitario es obligatorio.',
            'precio_unitario.numeric' => 'El precio unitario debe ser un número.',
            'precio_unitario.gte' => 'El precio unitario no puede ser negativo.',

            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un número.',
            'cantidad.gt' => 'La cantidad debe ser mayor a 0.',

            'sub_total.required' => 'El subtotal es obligatorio.',
            'sub_total.numeric' => 'El subtotal debe ser un número.',
            'sub_total.gte' => 'El subtotal no puede ser negativo.',
        ];
    }
}
