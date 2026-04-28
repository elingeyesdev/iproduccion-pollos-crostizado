<x-guest-layout>
    <div class="text-center mb-4">
        <h1 class="h4 fw-bold mb-1" style="color: #b30000;">Iniciar sesión</h1>
        <p class="text-muted mb-0">Accede al sistema de gestión.</p>
    </div>

    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label for="email" class="form-label fw-semibold">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autofocus autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label fw-semibold">Contraseña</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <label for="remember_me" class="form-check-label small d-flex align-items-center gap-2">
                <input id="remember_me" type="checkbox" class="form-check-input mt-0" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Recordarme
            </label>

            @if (Route::has('password.request'))
                <a class="small text-decoration-none" style="color: #2f9e44;" href="{{ route('password.request') }}">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <button type="submit" class="btn text-white fw-semibold" style="background-color: #b30000; border-color: #b30000;">
            <i class="fa-solid fa-right-to-bracket me-1"></i> Ingresar
        </button>
    </form>
</x-guest-layout>
