<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', 'exists:roles,name'],
        ];
    }

    public function attributes(): array
    {
        return [
            'roles' => 'roles',
            'roles.*' => 'rol',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'exists' => 'El :attribute seleccionado no existe.',
            'min' => 'Debes seleccionar al menos un :attribute.',
            'array' => 'El campo :attribute debe ser una lista valida.',
        ];
    }
}
