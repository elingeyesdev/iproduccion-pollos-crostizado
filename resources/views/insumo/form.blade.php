<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$insumo->nombre" placeholder="Ejemplo: Harina" required />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="unidad_medida_id" class="form-label fw-semibold">Unidad de medida</label>
            <select id="unidad_medida_id" name="unidad_medida_id" class="form-select @error('unidad_medida_id') is-invalid @enderror" required>
                <option value="">Selecciona una unidad</option>
                @foreach ($unidades as $unidad)
                    <option value="{{ $unidad->id }}" @selected((string) old('unidad_medida_id', (string) $insumo->unidad_medida_id) === (string) $unidad->id)>
                        {{ $unidad->nombre }} ({{ $unidad->abreviatura }})
                    </option>
                @endforeach
            </select>
            @error('unidad_medida_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="stock_actual" label="Stock actual" type="number" :value="$insumo->stock_actual" placeholder="0" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="stock_minimo" label="Stock mínimo" type="number" :value="$insumo->stock_minimo" placeholder="0" required />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="estado" class="form-label fw-semibold">Estado</label>
            <select id="estado" name="estado" class="form-select @error('estado') is-invalid @enderror" required>
                <option value="">Selecciona una opción</option>
                <option value="1" @selected((string) old('estado', (string) $insumo->estado) === '1')>Activo</option>
                <option value="0" @selected((string) old('estado', (string) $insumo->estado) === '0')>Inactivo</option>
            </select>
            @error('estado')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('insumos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>
