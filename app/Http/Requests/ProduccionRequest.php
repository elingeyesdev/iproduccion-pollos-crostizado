<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProduccionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'orden_produccion_id' => 'required|exists:orden_producciones,id',
            'insumo_id' => 'required|exists:insumos,id',
            'regla_conversion_id' => 'required|exists:regla_conversiones,id',
            'cantidad_insumo' => 'required|numeric|min:0.01',
            'cantidad_obtenida' => 'required|numeric|min:0.01',
        ];
    }
}
