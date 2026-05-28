<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$producto->nombre" placeholder="Ejemplo: Pollo entero" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="precio" label="Precio" type="number" step="0.01" :value="$producto->precio" placeholder="0.00" required />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="tipo_producto_id" class="form-label fw-semibold">Tipo de producto</label>
            <select id="tipo_producto_id" name="tipo_producto_id" class="form-select @error('tipo_producto_id') is-invalid @enderror" required>
                <option value="">Selecciona un tipo</option>
                @foreach ($tipoProductos as $tipo)
                    <option value="{{ $tipo->id }}" @selected((string) old('tipo_producto_id', (string) $producto->tipo_producto_id) === (string) $tipo->id)>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
            @error('tipo_producto_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="cantidad_actual" label="Cantidad actual" type="number" :value="$producto->cantidad_actual" placeholder="0" required />
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>
