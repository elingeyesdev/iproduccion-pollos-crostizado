<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$productoFinal->nombre" placeholder="Ejemplo: Pollo Broaster" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="descripcion" label="Descripción" :value="$productoFinal->descripcion" placeholder="Descripción del producto" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="precio" label="Precio" type="number" step="0.01" :value="$productoFinal->precio" placeholder="0.00" required />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="estado" class="form-label fw-semibold">Estado</label>
            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="">Selecciona una opción</option>
                <option value="1" @selected((string) old('estado', (string) $productoFinal->estado) === '1')>Activo</option>
                <option value="0" @selected((string) old('estado', (string) $productoFinal->estado) === '0')>Inactivo</option>
            </select>
            @error('estado')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('producto-finals.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>