<?php

namespace App\Http\Requests;

use App\Models\Merma;
use App\Models\Produccion;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

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

    // La merma acumulada del lote no puede superar este porcentaje de lo producido.
    public const PORCENTAJE_MAXIMO_MERMA = 5;

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                $produccion = Produccion::find($this->input('produccion_id'));

                if (!$produccion) {
                    return;
                }

                $mermaActual = $this->route('merma');
                $mermaActualId = $mermaActual instanceof Merma ? $mermaActual->id : $mermaActual;

                $yaRegistrada = (float) Merma::query()
                    ->whereIn('produccion_id', Produccion::delMismoLote($produccion)->select('id'))
                    ->where('estado', 1)
                    ->when($mermaActualId, fn ($q) => $q->whereKeyNot($mermaActualId))
                    ->sum('cantidad');

                $maximo = round((float) $produccion->cantidad_obtenida * self::PORCENTAJE_MAXIMO_MERMA / 100, 2);
                $disponible = max(round($maximo - $yaRegistrada, 2), 0);

                if ((float) $this->input('cantidad') > $disponible) {
                    $validator->errors()->add('cantidad', sprintf(
                        'La merma no puede superar el %d%% de lo producido (%s). Máximo permitido: %s, ya registrado: %s, disponible: %s.',
                        self::PORCENTAJE_MAXIMO_MERMA,
                        number_format((float) $produccion->cantidad_obtenida, 2),
                        number_format($maximo, 2),
                        number_format($yaRegistrada, 2),
                        number_format($disponible, 2)
                    ));
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'produccion_id' => 'producción',
            'cantidad' => 'cantidad',
        ];
    }

    public function messages(): array
    {
        return [
            'produccion_id.required' => 'Debes seleccionar una producción.',
            'produccion_id.exists' => 'La producción seleccionada no existe.',

            'cantidad.required' => 'La cantidad de merma es obligatoria.',
            'cantidad.numeric' => 'La cantidad debe ser un número.',
            'cantidad.min' => 'La cantidad no puede ser negativa.',
        ];
    }
}
