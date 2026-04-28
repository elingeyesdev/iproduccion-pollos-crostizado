<x-guest-layout>
    <div class="text-center mb-4">
        <h1 class="h4 fw-bold mb-1" style="color: #b30000;">Crear cuenta</h1>
        <p class="text-muted mb-0">Registra un nuevo usuario en el sistema.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="d-grid gap-3">
        @csrf

        <div>
            <label for="name" class="form-label fw-semibold">Nombre</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" required autofocus autocomplete="name">
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="email" class="form-label fw-semibold">Correo electrónico</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control @error('email') is-invalid @enderror" required autocomplete="username">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password" class="form-label fw-semibold">Contraseña</label>
            <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label fw-semibold">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password">
            @error('password_confirmation')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center justify-content-between">
            <a class="small text-decoration-none" style="color: #2f9e44;" href="{{ route('login') }}">
                ¿Ya tienes cuenta?
            </a>

            <button type="submit" class="btn text-white fw-semibold" style="background-color: #b30000; border-color: #b30000;">
                <i class="fa-solid fa-user-plus me-1"></i> Registrarse
            </button>
        </div>
    </form>
</x-guest-layout>
