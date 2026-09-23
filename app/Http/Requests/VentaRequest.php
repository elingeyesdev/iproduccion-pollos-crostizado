<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha_venta' => 'nullable|date',
            'total' => 'required|numeric|min:0',
            'estado' => 'nullable|integer|in:0,1',
            'descuento' => 'nullable|numeric|min:0',
            'pago_efectivo' => 'nullable|numeric|min:0',
            'pago_qr' => 'nullable|numeric|min:0',
            'productos' => 'required|array|min:1',
            'productos.*' => 'required|exists:productos,id',
            'cantidades' => 'required|array',
            'cantidades.*' => 'required|numeric|min:0.01',
            'precios' => 'required|array',
            'precios.*' => 'required|numeric|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'cliente_id' => 'cliente',
            'fecha_venta' => 'fecha de venta',
            'total' => 'total',
            'estado' => 'estado',
            'descuento' => 'descuento',
            'pago_efectivo' => 'pago en efectivo',
            'pago_qr' => 'pago por QR',
            'productos' => 'productos',
            'productos.*' => 'producto',
            'cantidades' => 'cantidades',
            'cantidades.*' => 'cantidad',
            'precios' => 'precios',
            'precios.*' => 'precio',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.required' => 'Debes seleccionar un cliente.',
            'cliente_id.exists' => 'El cliente seleccionado no existe.',

            'fecha_venta.date' => 'La fecha de venta debe ser una fecha válida.',

            'total.required' => 'El total es obligatorio.',
            'total.numeric' => 'El total debe ser un número.',
            'total.min' => 'El total no puede ser negativo.',

            'estado.integer' => 'El estado debe ser un valor entero.',
            'estado.in' => 'El estado seleccionado no es válido.',

            'descuento.numeric' => 'El descuento debe ser un número.',
            'descuento.min' => 'El descuento no puede ser negativo.',

            'pago_efectivo.numeric' => 'El pago en efectivo debe ser un número.',
            'pago_efectivo.min' => 'El pago en efectivo no puede ser negativo.',

            'pago_qr.numeric' => 'El pago por QR debe ser un número.',
            'pago_qr.min' => 'El pago por QR no puede ser negativo.',

            'productos.required' => 'Debes agregar al menos un producto.',
            'productos.array' => 'Los productos deben ser una lista válida.',
            'productos.min' => 'Debes agregar al menos :min producto(s).',
            'productos.*.required' => 'El producto de la línea :position es obligatorio.',
            'productos.*.exists' => 'El producto seleccionado en la línea :position no existe.',

            'cantidades.required' => 'Debes ingresar las cantidades.',
            'cantidades.array' => 'Las cantidades deben ser una lista válida.',
            'cantidades.*.required' => 'La cantidad de la línea :position es obligatoria.',
            'cantidades.*.numeric' => 'La cantidad de la línea :position debe ser un número.',
            'cantidades.*.min' => 'La cantidad de la línea :position debe ser mayor a 0.',

            'precios.required' => 'Debes ingresar los precios.',
            'precios.array' => 'Los precios deben ser una lista válida.',
            'precios.*.required' => 'El precio de la línea :position es obligatorio.',
            'precios.*.numeric' => 'El precio de la línea :position debe ser un número.',
            'precios.*.min' => 'El precio de la línea :position no puede ser negativo.',
        ];
    }
}
