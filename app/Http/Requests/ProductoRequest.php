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
        ];
    }
}
