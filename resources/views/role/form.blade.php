<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="name" label="Nombre" :value="$role->name" placeholder="Ejemplo: administrador" required />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="guard_name" class="form-label fw-semibold">Guard</label>
            <select id="guard_name" name="guard_name" class="form-select @error('guard_name') is-invalid @enderror" required>
                <option value="">Selecciona una opción</option>
                <option value="web" @selected(old('guard_name', $role->guard_name) === 'web')>web</option>
                <option value="api" @selected(old('guard_name', $role->guard_name) === 'api')>api</option>
            </select>
            @error('guard_name')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>