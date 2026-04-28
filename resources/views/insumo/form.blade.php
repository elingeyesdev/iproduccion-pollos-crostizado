<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$insumo->nombre" placeholder="Ejemplo: Harina" required />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="categoria_insumo_id" class="form-label fw-semibold">Categoría</label>
            <select id="categoria_insumo_id" name="categoria_insumo_id" class="form-select @error('categoria_insumo_id') is-invalid @enderror" required>
                <option value="">Selecciona una categoría</option>
                @foreach ($categorias as $id => $nombre)
                    <option value="{{ $id }}" @selected((string) old('categoria_insumo_id', (string) $insumo->categoria_insumo_id) === (string) $id)>{{ $nombre }}</option>
                @endforeach
            </select>
            @error('categoria_insumo_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
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
        <x-crud.form-input name="precioUnitario" label="Precio unitario" type="number" step="0.01" :value="$insumo->precioUnitario" placeholder="0.00" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="stockActual" label="Stock actual" type="number" :value="$insumo->stockActual" placeholder="0" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="stockMinimo" label="Stock mínimo" type="number" :value="$insumo->stockMinimo" placeholder="0" required />
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