<div class="row">
    <div class="col-md-6">
        <x-crud.form-input
            name="name"
            label="Nombre"
            :value="$user->name"
            placeholder="Nombre completo"
            required
        />
    </div>

    <div class="col-md-6">
        <x-crud.form-input
            name="email"
            label="Correo electrónico"
            type="email"
            :value="$user->email"
            placeholder="usuario@correo.com"
            required
        />
    </div>

    <div class="col-md-6">
        <x-crud.form-input
            name="password"
            label="Contraseña"
            type="password"
            placeholder="Mínimo 8 caracteres"
            :required="$isCreate"
        />
    </div>

    <div class="col-md-6">
        <x-crud.form-input
            name="password_confirmation"
            label="Confirmar contraseña"
            type="password"
            placeholder="Repite la contraseña"
            :required="$isCreate"
        />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="role" class="form-label fw-semibold">Rol</label>
            <select id="role" name="role" class="form-select @error('role') is-invalid @enderror" required>
                <option value="">Selecciona un rol</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}" @selected(old('role', $selectedRole) === $role->name)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    @if (!$isCreate)
        <div class="col-12">
            <small class="text-muted">Si no deseas cambiar la contraseña, deja ambos campos vacíos.</small>
        </div>
    @endif

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>
