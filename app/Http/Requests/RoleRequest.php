<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
			'name' => 'required|string|max:100',
			'guard_name' => 'required|string|in:web,api',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre del rol',
            'guard_name' => 'guard',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'max' => 'El campo :attribute excede el límite permitido.',
            'in' => 'El valor seleccionado para :attribute no es válido.',
        ];
    }
}
