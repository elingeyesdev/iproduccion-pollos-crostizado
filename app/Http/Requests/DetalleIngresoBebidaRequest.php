<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetalleIngresoBebidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ingreso_bebida_id' => 'required|exists:ingreso_bebidas,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_ingresa' => 'required|numeric|gt:0',
            'precio' => 'required|numeric|min:0',
        ];
    }
}
