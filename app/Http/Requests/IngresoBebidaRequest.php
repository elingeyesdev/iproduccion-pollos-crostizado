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
            'nombre'                        => 'required|string|max:255',
            'descripcion'                   => 'nullable|string|max:255',
            'fecha_ingreso'                 => 'required|date',
            'productos'                     => 'required|array|min:1',
            'productos.*.producto_id'       => 'required|exists:productos,id',
            'productos.*.cantidad_ingresa'  => 'required|numeric|min:0.01',
            'productos.*.precio'            => 'required|numeric|min:0',
        ];
    }
}
