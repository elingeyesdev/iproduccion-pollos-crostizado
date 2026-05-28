<div class="row g-3">
    <div class="col-md-6">
        <label for="produccion_id" class="form-label fw-semibold">Producción</label>
        <select id="produccion_id" name="produccion_id" class="form-select @error('produccion_id') is-invalid @enderror" required>
            <option value="">Selecciona una producción</option>
            @foreach ($producciones as $produccion)
                <option value="{{ $produccion->id }}" @selected((string) old('produccion_id', $merma->produccion_id) === (string) $produccion->id)>
                    {{ $produccion->ordenProduccion?->numero_orden ?? ('Producción #' . $produccion->id) }}
                </option>
            @endforeach
        </select>
        @error('produccion_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="cantidad" label="Cantidad" type="number" step="0.01" min="0" :value="$merma->cantidad" placeholder="0.00" required />
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('mermas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>
