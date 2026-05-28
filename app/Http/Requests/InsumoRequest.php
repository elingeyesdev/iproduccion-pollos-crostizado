<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsumoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:150',
            'unidad_medida_id' => 'required|exists:unidad_medidas,id',
            'stock_actual' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'estado' => 'nullable|integer|in:0,1',
        ];
    }
}
