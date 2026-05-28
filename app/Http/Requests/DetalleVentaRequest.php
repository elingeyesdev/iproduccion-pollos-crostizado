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
}
