<div class="row">
    <div class="col-md-6">
        <x-crud.form-input
            name="nombre"
            label="Nombre"
            :value="$categoriaInsumo->nombre"
            placeholder="Ejemplo: Carnes"
            required
        />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="estado" class="form-label fw-semibold">Estado</label>
            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="">Selecciona una opcion</option>
                <option value="1" @selected((string) old('estado', (string) $categoriaInsumo->estado) === '1')>Activo</option>
                <option value="0" @selected((string) old('estado', (string) $categoriaInsumo->estado) === '0')>Inactivo</option>
            </select>
            @error('estado')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="descripcion" class="form-label fw-semibold">Descripcion</label>
            <textarea
                id="descripcion"
                name="descripcion"
                rows="4"
                class="form-control @error('descripcion') is-invalid @enderror"
                placeholder="Describe brevemente la categoria"
                required
            >{{ old('descripcion', $categoriaInsumo->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('categoria-insumos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary">
            <i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}
        </button>
    </div>
</div>