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
}
