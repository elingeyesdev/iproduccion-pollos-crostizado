<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComponenteProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:255',
            'equivalencia' => 'nullable|string|max:255',
        ];
    }
}
