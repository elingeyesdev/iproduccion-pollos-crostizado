<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="user_id" class="form-label fw-semibold">Usuario</label>
            <select id="user_id" name="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                <option value="">Selecciona un usuario</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected((string) old('user_id', $selectedUserId ?? '') === (string) $user->id)>
                        {{ $user->name }} - {{ $user->email }}
                    </option>
                @endforeach
            </select>
            @error('user_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="role_id" class="form-label fw-semibold">Rol</label>
            <select id="role_id" name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                <option value="">Selecciona un rol</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected((string) old('role_id', $selectedRoleId ?? '') === (string) $role->id)>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('role-assignments.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>