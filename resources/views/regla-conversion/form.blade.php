<div class="row g-3">
    <!-- Entrada -->
    <div class="col-md-12">
        <h5 class="fw-bold text-danger mb-3"><i class="fa-solid fa-arrow-right-to-bracket me-2"></i>Insumo de Entrada</h5>
    </div>
    
    <div class="col-md-6">
        <label for="insumo_id" class="form-label fw-semibold">Insumo</label>
        <select id="insumo_id" name="insumo_id" class="form-select @error('insumo_id') is-invalid @enderror" required>
            <option value="">Selecciona el insumo</option>
            @foreach ($insumos as $insumo)
                <option value="{{ $insumo->id }}" @selected(old('insumo_id', $reglaConversion->insumo_id) == $insumo->id)>
                    {{ $insumo->nombre }}
                </option>
            @endforeach
        </select>
        @error('insumo_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="unidad_medida_entrada_id" class="form-label fw-semibold">Unidad Medida Entrada</label>
        <select id="unidad_medida_entrada_id" name="unidad_medida_entrada_id" class="form-select @error('unidad_medida_entrada_id') is-invalid @enderror" required>
            <option value="">Selecciona unidad</option>
            @foreach ($unidades as $unidad)
                <option value="{{ $unidad->id }}" @selected(old('unidad_medida_entrada_id', $reglaConversion->unidad_medida_entrada_id) == $unidad->id)>
                    {{ $unidad->nombre }}
                </option>
            @endforeach
        </select>
        @error('unidad_medida_entrada_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <label for="cantidad_entrada" class="form-label fw-semibold">Cantidad Entrada</label>
        <input type="number" id="cantidad_entrada" name="cantidad_entrada" class="form-control" value="1" readonly>
        <div class="form-text">Siempre es 1 (unidad base de la regla).</div>
    </div>

    <!-- Salida -->
    <div class="col-md-12 mt-4">
        <h5 class="fw-bold text-success mb-3"><i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Producto Resultante</h5>
    </div>

    <div class="col-md-6">
        <label for="producto_id" class="form-label fw-semibold">Producto</label>
        <select id="producto_id" name="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
            <option value="">Selecciona el producto</option>
            @foreach ($productos as $producto)
                <option value="{{ $producto->id }}" @selected(old('producto_id', $reglaConversion->producto_id) == $producto->id)>
                    {{ $producto->nombre }}
                </option>
            @endforeach
        </select>
        @error('producto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label for="unidad_medida_resultante_id" class="form-label fw-semibold">Unidad Medida Resultante</label>
        <select id="unidad_medida_resultante_id" name="unidad_medida_resultante_id" class="form-select @error('unidad_medida_resultante_id') is-invalid @enderror" required>
            <option value="">Selecciona unidad</option>
            @foreach ($unidades as $unidad)
                <option value="{{ $unidad->id }}" @selected(old('unidad_medida_resultante_id', $reglaConversion->unidad_medida_resultante_id) == $unidad->id)>
                    {{ $unidad->nombre }}
                </option>
            @endforeach
        </select>
        @error('unidad_medida_resultante_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <!-- Rendimiento -->
    <div class="col-md-12 mt-4">
        <h5 class="fw-bold text-info mb-3"><i class="fa-solid fa-calculator me-2"></i>Rendimiento y Tipo</h5>
    </div>

    <div class="col-md-4">
        <x-crud.form-input name="rendimiento_minimo" label="Rendimiento Mínimo" type="number" step="0.01" :value="$reglaConversion->rendimiento_minimo" placeholder="0.00" required />
    </div>

    <div class="col-md-4">
        <x-crud.form-input name="rendimiento_maximo" label="Rendimiento Máximo" type="number" step="0.01" :value="$reglaConversion->rendimiento_maximo" placeholder="0.00" required />
    </div>

    <div class="col-md-4">
        <label for="tipo_conversion" class="form-label fw-semibold">Tipo de Conversión</label>
        <select id="tipo_conversion" name="tipo_conversion" class="form-select @error('tipo_conversion') is-invalid @enderror" required>
            <option value="Estática" @selected(old('tipo_conversion', $reglaConversion->tipo_conversion) == 'Estática')>Estática</option>
            <option value="Variable" @selected(old('tipo_conversion', $reglaConversion->tipo_conversion) == 'Variable')>Variable</option>
        </select>
        @error('tipo_conversion') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-12">
        <label for="observacion" class="form-label fw-semibold">Observación General</label>
        <textarea name="observacion" id="observacion" class="form-control" rows="2" placeholder="Notas sobre esta regla...">{{ old('observacion', $reglaConversion->observacion) }}</textarea>
    </div>

    <!-- Receta de Conversión (Detalles) -->
    <div class="col-12 mt-4">
        <hr>
        <h5 class="fw-bold text-primary mb-3"><i class="fa-solid fa-list-check me-2"></i>Receta de Conversión (Insumos Adicionales)</h5>
        
        <div id="detalles-container">
            @php
                $detalles = old('detalles', $reglaConversion->recetaConversiones ?? []);
            @endphp

            @forelse ($detalles as $index => $detalle)
                <div class="row g-2 mb-2 detalle-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Insumo</label>
                        <select name="detalles[{{ $index }}][insumo_id]" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach ($insumos as $insumo)
                                <option value="{{ $insumo->id }}" @selected(($detalle['insumo_id'] ?? $detalle->insumo_id) == $insumo->id)>
                                    {{ $insumo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Cantidad</label>
                        <input type="number" step="0.01" name="detalles[{{ $index }}][cantidad]" class="form-control" value="{{ $detalle['cantidad'] ?? $detalle->cantidad }}" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Unidad</label>
                        <select name="detalles[{{ $index }}][unidad_medida_id]" class="form-select" required>
                            @foreach ($unidades as $unidad)
                                <option value="{{ $unidad->id }}" @selected(($detalle['unidad_medida_id'] ?? $detalle->unidad_medida_id) == $unidad->id)>
                                    {{ $unidad->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Observación</label>
                        <input type="text" name="detalles[{{ $index }}][observacion]" class="form-control" value="{{ $detalle['observacion'] ?? $detalle->observacion }}">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-detalle w-100"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            @empty
                <div class="row g-2 mb-2 detalle-row align-items-end">
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Insumo</label>
                        <select name="detalles[0][insumo_id]" class="form-select" required>
                            <option value="">Seleccionar</option>
                            @foreach ($insumos as $insumo)
                                <option value="{{ $insumo->id }}">{{ $insumo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Cantidad</label>
                        <input type="number" step="0.01" name="detalles[0][cantidad]" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted">Unidad</label>
                        <select name="detalles[0][unidad_medida_id]" class="form-select" required>
                            <option value="">Unidad</option>
                            @foreach ($unidades as $unidad)
                                <option value="{{ $unidad->id }}">{{ $unidad->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted">Observación</label>
                        <input type="text" name="detalles[0][observacion]" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-detalle w-100" disabled><i class="fa-solid fa-trash"></i></button>
                    </div>
                </div>
            @endforelse
        </div>

        <button type="button" id="add-detalle" class="btn btn-sm btn-outline-primary mt-2">
            <i class="fa-solid fa-plus me-1"></i> Agregar Insumo a la Receta
        </button>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="{{ route('regla-conversiones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar Regla' }}</button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('detalles-container');
        const addButton = document.getElementById('add-detalle');
        let rowCount = container.querySelectorAll('.detalle-row').length;

        addButton.addEventListener('click', () => {
            const firstRow = container.querySelector('.detalle-row');
            const newRow = firstRow.cloneNode(true);
            
            newRow.querySelectorAll('input').forEach(i => i.value = '');
            newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
            
            newRow.querySelectorAll('[name*="detalles["]').forEach(field => {
                const name = field.name;
                const newName = name.replace(/detalles\[\d+\]/, `detalles[${rowCount}]`);
                field.name = newName;
                field.id = newName.replace(/\[/g, '_').replace(/\]/g, '');
            });

            newRow.querySelector('.remove-detalle').disabled = false;
            container.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
        });

        container.addEventListener('click', (e) => {
            if (e.target.closest('.remove-detalle')) {
                const row = e.target.closest('.detalle-row');
                if (container.querySelectorAll('.detalle-row').length > 1) {
                    row.remove();
                    // Opcional: Reindexar después de eliminar para evitar huecos, aunque PHP lo maneja como array
                    updateRemoveButtons();
                }
            }
        });

        function updateRemoveButtons() {
            const buttons = container.querySelectorAll('.remove-detalle');
            buttons.forEach(btn => {
                btn.disabled = buttons.length <= 1;
            });
        }
    });
</script>
@endpush
