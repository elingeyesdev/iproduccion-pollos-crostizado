<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Validator;

class StoreReglaConversionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'insumo_id' => 'required|integer|exists:insumos,id',
            'cantidad_entrada' => 'required|numeric|min:0.01',
            'producto_id' => 'required|integer|exists:productos,id',
            'rendimiento_minimo' => 'required|numeric|min:0',
            'rendimiento_maximo' => 'required|numeric|min:0',
            'tipo_conversion' => 'required|string|in:Estática,Variable,estatica,variable',
            'detalles' => 'nullable|array',
            'detalles.*.insumo_id' => 'required|integer|exists:insumos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.001',
            'detalles.*.unidad_medida_id' => 'required|integer|exists:unidad_medidas,id',
            'detalles.*.observacion' => 'nullable|string|max:255',
        ];

        if (Schema::hasColumn('reglas_conversion', 'unidad_entrada_id')) {
            $rules['unidad_medida_entrada_id'] = 'required|integer|exists:unidad_medidas,id';
        }

        if (Schema::hasColumn('reglas_conversion', 'unidad_salida_id')) {
            $rules['unidad_medida_resultante_id'] = 'required|integer|exists:unidad_medidas,id';
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cantidad_entrada' => 1,
            // Las filas de receta que se dejaron vacías no son insumos adicionales
            'detalles' => array_values(array_filter(
                (array) $this->input('detalles', []),
                fn ($d) => filled($d['insumo_id'] ?? null) || filled($d['cantidad'] ?? null)
            )),
        ]);

        if ($this->filled('tipo_conversion')) {
            $tipoConversion = strtolower((string) $this->input('tipo_conversion'));

            $this->merge([
                'tipo_conversion' => $tipoConversion === 'variable' ? 'variable' : 'estatica',
            ]);
        }
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $rendimientoMinimo = $this->input('rendimiento_minimo');
            $rendimientoMaximo = $this->input('rendimiento_maximo');

            if (
                is_numeric($rendimientoMinimo)
                && is_numeric($rendimientoMaximo)
                && (float) $rendimientoMaximo < (float) $rendimientoMinimo
            ) {
                $validator->errors()->add(
                    'rendimiento_maximo',
                    'El rendimiento maximo debe ser mayor o igual al rendimiento minimo.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'insumo_id.required' => 'El insumo es obligatorio.',
            'insumo_id.exists' => 'El insumo seleccionado no existe.',
            'cantidad_entrada.required' => 'La cantidad de entrada es obligatoria.',
            'cantidad_entrada.numeric' => 'La cantidad debe ser un número.',
            'cantidad_entrada.min' => 'La cantidad debe ser mayor a 0.',
            'producto_id.required' => 'El producto es obligatorio.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'rendimiento_minimo.required' => 'El rendimiento mínimo es obligatorio.',
            'rendimiento_maximo.required' => 'El rendimiento máximo es obligatorio.',
            'tipo_conversion.required' => 'El tipo de conversión es obligatorio.',
            'tipo_conversion.in' => 'El tipo de conversion debe ser estatica o variable.',
            'unidad_medida_entrada_id.required' => 'La unidad de medida de entrada es obligatoria.',
            'unidad_medida_resultante_id.required' => 'La unidad de medida resultante es obligatoria.',
            'detalles.*.insumo_id.required' => 'El insumo del detalle es obligatorio.',
            'detalles.*.cantidad.required' => 'La cantidad del detalle es obligatoria.',
            'detalles.*.unidad_medida_id.required' => 'La unidad de medida del detalle es obligatoria.',
        ];
    }
}
