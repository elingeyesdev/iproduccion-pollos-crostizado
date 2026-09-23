<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'correo electrónico',
            'password' => 'contraseña',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.string' => 'El correo electrónico debe ser un texto válido.',
            'email.email' => 'Debes ingresar un correo electrónico válido.',

            'password.required' => 'La contraseña es obligatoria.',
            'password.string' => 'La contraseña debe ser un texto válido.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $email = Str::lower(trim((string) $this->input('email', '')));
        $user = $email !== '' ? User::whereRaw('LOWER(email) = ?', [$email])->first() : null;

        if ($user && ! $user->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'email' => 'Debes verificar tu correo electrónico antes de iniciar sesión.',
            ]);
        }

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $attempts = RateLimiter::attempts($this->throttleKey());
            $remaining = max(0, 5 - $attempts);

            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => $remaining > 0
                    ? 'Credenciales incorrectas. Te quedan ' . $remaining . ' intento(s) antes de quedar bloqueado.'
                    : trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
