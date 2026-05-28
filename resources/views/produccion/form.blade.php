<div class="row g-3">
    <div class="col-md-6">
        <label for="orden_produccion_id" class="form-label fw-semibold">Orden de producción</label>
        <select id="orden_produccion_id" name="orden_produccion_id" class="form-select @error('orden_produccion_id') is-invalid @enderror" required>
            <option value="">Selecciona una orden</option>
            @foreach ($ordenProducciones as $ordenProduccion)
                <option value="{{ $ordenProduccion->id }}" @selected((string) old('orden_produccion_id', $produccionRecord->orden_produccion_id) === (string) $ordenProduccion->id)>
                    {{ $ordenProduccion->numero_orden }} - {{ $ordenProduccion->fecha_programada ? \Illuminate\Support\Carbon::parse($ordenProduccion->fecha_programada)->format('d/m/Y') : '-' }}
                </option>
            @endforeach
        </select>
        @error('orden_produccion_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="cantidad_insumo" class="form-label fw-semibold">Cantidad de Insumo Base</label>
        <input type="number" step="0.01" name="cantidad_insumo" id="cantidad_insumo" class="form-control @error('cantidad_insumo') is-invalid @enderror" value="{{ old('cantidad_insumo', $produccionRecord->cantidad_insumo) }}" placeholder="Ej: 2 pollos" required>
        @error('cantidad_insumo')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="cantidad_obtenida" class="form-label fw-semibold">Cantidad Obtenida (Final)</label>
        <input type="number" step="0.01" name="cantidad_obtenida" id="cantidad_obtenida" class="form-control @error('cantidad_obtenida') is-invalid @enderror" value="{{ old('cantidad_obtenida', $produccionRecord->cantidad_obtenida) }}" placeholder="Ej: 16 piezas" required>
        @error('cantidad_obtenida')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    @if($errors->has('error'))
        <div class="col-12">
            <div class="alert alert-danger">{{ $errors->first('error') }}</div>
        </div>
    @endif

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Confirmar Producción</button>
    </div>
</div>
