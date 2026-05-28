<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UnidadMedidaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'abreviatura' => 'required|string|max:50',
            'factor_conversion' => 'required|numeric|min:0',
            'estado' => 'nullable|integer|in:0,1',
        ];
    }
}
