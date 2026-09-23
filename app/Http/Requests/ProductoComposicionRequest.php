<?php

namespace App\Http\Requests;

use App\Models\Producto;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ProductoComposicionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->storeRules();
        }

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            return $this->updateRules();
        }

        return [];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $productoPadreId = $this->getProductoPadreId();
            if ($productoPadreId === null) {
                return;
            }

            $ingredientes = (array) $this->input('ingredientes', []);
            foreach ($ingredientes as $ingrediente) {
                if ((int) ($ingrediente['id'] ?? 0) === $productoPadreId) {
                    $validator->errors()->add(
                        'ingredientes',
                        'El producto no puede incluirse a si mismo en su composicion.'
                    );
                    return;
                }
            }
        });
    }

    private function storeRules(): array
    {
        return [
            'producto_id' => 'required|exists:productos,id',
            'ingredientes' => 'nullable|array',
            'ingredientes.*.id' => 'required|exists:productos,id',
            'ingredientes.*.cantidad' => 'required|numeric|min:0.01',
        ];
    }

    private function updateRules(): array
    {
        return [
            'ingredientes' => 'nullable|array',
            'ingredientes.*.id' => 'required|exists:productos,id',
            'ingredientes.*.cantidad' => 'required|numeric|min:0.01',
        ];
    }

    private function getProductoPadreId(): ?int
    {
        if ($this->isMethod('post')) {
            $id = $this->input('producto_id');
            return is_numeric($id) ? (int) $id : null;
        }

        $producto = $this->route('producto');
        if ($producto instanceof Producto) {
            return (int) $producto->id;
        }

        return is_numeric($producto) ? (int) $producto : null;
    }

    public function attributes(): array
    {
        return [
            'producto_id' => 'producto',
            'ingredientes' => 'ingredientes',
            'ingredientes.*.id' => 'ingrediente',
            'ingredientes.*.cantidad' => 'cantidad',
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required' => 'Debes seleccionar un producto.',
            'producto_id.exists' => 'El producto seleccionado no existe.',

            'ingredientes.array' => 'Los ingredientes deben ser una lista válida.',

            'ingredientes.*.id.required' => 'El ingrediente de la línea :position es obligatorio.',
            'ingredientes.*.id.exists' => 'El ingrediente seleccionado en la línea :position no existe.',

            'ingredientes.*.cantidad.required' => 'La cantidad del ingrediente en la línea :position es obligatoria.',
            'ingredientes.*.cantidad.numeric' => 'La cantidad del ingrediente en la línea :position debe ser un número.',
            'ingredientes.*.cantidad.min' => 'La cantidad del ingrediente en la línea :position debe ser mayor a 0.',
        ];
    }
}
