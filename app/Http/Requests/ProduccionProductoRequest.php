<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduccionProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produccion_id' => 'required|exists:producciones,id',
            'producto_id' => 'required|exists:productos,id',
            'cantidad_producida' => 'required|numeric|gt:0',
        ];
    }
}
