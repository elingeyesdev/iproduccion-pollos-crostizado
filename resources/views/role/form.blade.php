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

    <div class="col-12">
        <label class="form-label fw-semibold">Permisos</label>
        <div class="row g-3">
            @foreach ($permissions->groupBy(fn ($permission) => \Illuminate\Support\Str::before($permission->name, '.')) as $module => $modulePermissions)
                <div class="col-md-6 col-xl-4">
                    <div class="border rounded-3 p-3 h-100">
                        <strong class="d-block mb-2 text-capitalize">{{ str_replace('-', ' ', $module) }}</strong>
                        @foreach ($modulePermissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="permission-{{ $permission->id }}"
                                    @checked(in_array($permission->name, old('permissions', $selectedPermissions), true))>
                                <label class="form-check-label" for="permission-{{ $permission->id }}">
                                    {{ \Illuminate\Support\Str::after($permission->name, '.') }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        @error('permissions')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @error('permissions.*')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>