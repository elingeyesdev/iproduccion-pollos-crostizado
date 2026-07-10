<div class="row g-3">
    <div class="col-md-12">
        <x-crud.form-input name="nombre" label="Nombre de la Receta" :value="$receta->nombre" placeholder="Ej: Producción de Pollo a la Brasa" required />
    </div>

    <div class="col-md-6">
        <label for="producto_id" class="form-label fw-semibold">Producto Final Obtenido</label>
        <select id="producto_id" name="producto_id" class="form-select @error('producto_id') is-invalid @enderror" required>
            <option value="">Selecciona el producto</option>
            @foreach ($productos as $producto)
                <option value="{{ $producto->id }}" @selected(old('producto_id', $receta->producto_id) == $producto->id)>
                    {{ $producto->nombre }}
                </option>
            @endforeach
        </select>
        @error('producto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-3">
        <x-crud.form-input name="cantidad_base" label="Cantidad Base (Insumo)" type="number" step="0.01" :value="$receta->cantidad_base ?? 1" placeholder="1" required />
    </div>

    <div class="col-md-3">
        <x-crud.form-input name="rendimiento_esperado" label="Rendimiento (Piezas/Porciones)" type="number" step="0.01" :value="$receta->rendimiento_esperado" placeholder="Ej: 8" required />
    </div>

    <div class="col-12">
        <hr>
        <h5 class="fw-bold text-danger mb-3"><i class="fa-solid fa-box-open me-2"></i>Insumos Requeridos</h5>
        
        <div id="insumos-container">
            @php
                $oldInsumos = old('insumos', $receta->insumos->map(fn($i) => ['id' => $i->id, 'cantidad' => $i->pivot->cantidad_requerida])->toArray());
                if (empty($oldInsumos)) {
                    $oldInsumos = [['id' => '', 'cantidad' => '']];
                }
            @endphp

            @foreach ($oldInsumos as $index => $oldInsumo)
                <div class="row g-2 mb-2 insumo-row align-items-end">
                    <div class="col-md-7">
                        <label class="form-label small text-muted">Insumo</label>
                        <select name="insumos[{{ $index }}][id]" class="form-select" required>
                            <option value="">Seleccionar insumo</option>
                            @foreach ($insumos as $insumo)
                                <option value="{{ $insumo->id }}" @selected(($oldInsumo['id'] ?? '') == $insumo->id)>
                                    {{ $insumo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted">Cantidad Requerida</label>
                        <input type="number" step="0.01" name="insumos[{{ $index }}][cantidad]" class="form-control" value="{{ $oldInsumo['cantidad'] ?? '' }}" placeholder="0.00" required>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger remove-insumo w-100" @if(count($oldInsumos) <= 1) disabled @endif>
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <button type="button" id="add-insumo" class="btn btn-sm btn-outline-primary mt-2">
            <i class="fa-solid fa-plus me-1"></i> Agregar otro insumo
        </button>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
        <a href="{{ route('recetas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar Receta' }}</button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('insumos-container');
        const addButton = document.getElementById('add-insumo');
        let rowCount = {{ count($oldInsumos) }};

        addButton.addEventListener('click', () => {
            const firstRow = container.querySelector('.insumo-row');
            const newRow = firstRow.cloneNode(true);
            
            // Limpiar inputs
            newRow.querySelectorAll('input').forEach(i => i.value = '');
            newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
            
            // Actualizar nombres de campos
            newRow.querySelectorAll('[name*="insumos["]').forEach(field => {
                field.name = field.name.replace(/insumos\[\d+\]/, `insumos[${rowCount}]`);
            });

            // Habilitar botón eliminar
            newRow.querySelector('.remove-insumo').disabled = false;
            
            container.appendChild(newRow);
            rowCount++;
            updateRemoveButtons();
        });

        container.addEventListener('click', (e) => {
            if (e.target.closest('.remove-insumo')) {
                const row = e.target.closest('.insumo-row');
                if (container.querySelectorAll('.insumo-row').length > 1) {
                    row.remove();
                    updateRemoveButtons();
                }
            }
        });

        function updateRemoveButtons() {
            const buttons = container.querySelectorAll('.remove-insumo');
            buttons.forEach(btn => {
                btn.disabled = buttons.length <= 1;
            });
        }
    });
</script>
@endpush
