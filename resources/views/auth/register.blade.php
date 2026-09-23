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
            <div id="password-strength" class="small mt-2" aria-live="polite">
                <span class="fw-semibold text-danger">Insegura</span>
            </div>
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="form-label fw-semibold">Confirmar contraseña</label>
            <input id="password_confirmation" type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror" required autocomplete="new-password">
            <div id="password-match" class="small mt-2" aria-live="polite"></div>
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

    <script>
        (() => {
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const confirmationInput = document.getElementById('password_confirmation');
            const strength = document.getElementById('password-strength');
            const strengthLabel = strength.querySelector('span');
            const matchLabel = document.getElementById('password-match');

            const updatePasswordStatus = () => {
                const password = passwordInput.value;
                const normalizedPassword = password.trim().toLowerCase();
                const normalizedName = nameInput.value.trim().toLowerCase();
                const normalizedEmail = emailInput.value.trim().toLowerCase();
                const rules = {
                    length: password.length >= 8,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    identity: normalizedPassword !== normalizedName && normalizedPassword !== normalizedEmail,
                };
                const isSecure = Object.values(rules).every(Boolean);

                strengthLabel.textContent = isSecure ? 'Segura' : 'Insegura';
                strengthLabel.classList.toggle('text-success', isSecure);
                strengthLabel.classList.toggle('text-danger', !isSecure);

                confirmationInput.classList.toggle(
                    'is-invalid',
                    confirmationInput.value.length > 0 && password !== confirmationInput.value,
                );

                if (confirmationInput.value.length === 0) {
                    matchLabel.textContent = '';
                } else {
                    const matches = password === confirmationInput.value;
                    matchLabel.textContent = matches ? 'Coincide' : 'No coincide';
                    matchLabel.classList.toggle('text-success', matches);
                    matchLabel.classList.toggle('text-danger', !matches);
                }
            };

            [nameInput, emailInput, passwordInput, confirmationInput].forEach((input) => {
                input.addEventListener('input', updatePasswordStatus);
            });

            updatePasswordStatus();
        })();
    </script>
</x-guest-layout>
