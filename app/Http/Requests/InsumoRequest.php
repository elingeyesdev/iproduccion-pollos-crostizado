<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InsumoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
			'nombre' => 'required|string',
			'categoria_insumo_id' => 'required',
			'unidad_medida_id' => 'required',
			'precioUnitario' => 'required',
			'stockActual' => 'required',
			'stockMinimo' => 'required',
			'estado' => 'required',
        ];
    }
}
