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
}
