<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MermaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'produccion_id' => 'required|exists:producciones,id',
            'cantidad' => 'required|numeric|min:0',
        ];
    }
}
