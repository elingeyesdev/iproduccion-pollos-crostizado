<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DetalleInsumoProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produccion_id' => 'required|exists:producciones,id',
            'insumo_id' => 'required|exists:insumos,id',
            'cantidad' => 'required|numeric|gt:0',
        ];
    }
}
