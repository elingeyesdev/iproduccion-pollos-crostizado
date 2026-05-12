<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id;

        $rules = [
            'name' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:180',
                Rule::unique('users', 'email')->ignore($userId),
            ],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', 'exists:roles,name'],
        ];

        if ($this->isMethod('POST')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        } else {
            $rules['password'] = ['nullable', 'string', 'min:8', 'confirmed'];
        }

        return $rules;
    }

    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'roles' => 'roles',
            'roles.*' => 'rol',
        ];
    }

    public function messages(): array
    {
        return [
            'required' => 'El campo :attribute es obligatorio.',
            'email' => 'El :attribute debe ser un correo válido.',
            'unique' => 'El :attribute ya está registrado.',
            'exists' => 'El :attribute seleccionado no existe.',
            'confirmed' => 'La confirmación de :attribute no coincide.',
            'min' => 'El campo :attribute debe tener al menos :min caracteres.',
            'max' => 'El campo :attribute supera el límite permitido.',
            'array' => 'El campo :attribute debe ser una lista válida.',
        ];
    }
}
