<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
                function (string $attribute, mixed $value, \Closure $fail) use ($request): void {
                    $password = mb_strtolower((string) $value);
                    $name = mb_strtolower(trim((string) $request->input('name')));
                    $email = mb_strtolower(trim((string) $request->input('email')));

                    if ($password !== '' && ($password === $name || $password === $email)) {
                        $fail('La contraseña no puede ser igual al nombre de usuario o al correo electrónico.');
                    }
                },
            ],
        ]);

        $user = User::registerFromData($validated);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
