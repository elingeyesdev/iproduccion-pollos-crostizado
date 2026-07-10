<div class="row g-3">
    <div class="col-md-8">
        <label for="orden_produccion_insumo_id" class="form-label fw-semibold">Detalle de orden a ejecutar</label>
        <select id="orden_produccion_insumo_id" name="orden_produccion_insumo_id" class="form-select @error('orden_produccion_insumo_id') is-invalid @enderror" required>
            <option value="">Selecciona un detalle</option>
            @foreach ($detallesOrden as $detalle)
                <option value="{{ $detalle->id }}"
                    data-cantidad="{{ (float) $detalle->cantidad }}"
                    data-insumo-id="{{ (int) $detalle->insumo_id }}"
                    data-insumo-nombre="{{ mb_strtolower(trim((string) ($detalle->insumo?->nombre ?? ''))) }}"
                    @selected((string) old('orden_produccion_insumo_id', $produccionRecord->orden_produccion_insumo_id) === (string) $detalle->id)>
                    OP: {{ $detalle->ordenProduccion?->numero_orden ?? '-' }} | Insumo: {{ $detalle->insumo?->nombre ?? '-' }} | Cantidad: {{ number_format((float) $detalle->cantidad, 2) }}
                </option>
            @endforeach
        </select>
        @error('orden_produccion_insumo_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label for="cantidad_insumo_ref" class="form-label fw-semibold">Cantidad de insumo (orden)</label>
        <input type="text" id="cantidad_insumo_ref" class="form-control" value="-" readonly>
    </div>

    <div class="col-md-8">
        <label for="regla_conversion_id" class="form-label fw-semibold">Regla de conversión</label>
        <select id="regla_conversion_id" name="regla_conversion_id" class="form-select @error('regla_conversion_id') is-invalid @enderror" required disabled>
            <option value="">Selecciona una regla</option>
            @foreach ($reglas as $regla)
                <option value="{{ $regla->id }}"
                    data-insumo-id="{{ (int) ($regla->insumo_id ?? 0) }}"
                    data-insumo-nombre="{{ mb_strtolower(trim((string) ($regla->nombre_insumo ?? ($regla->insumo?->nombre ?? ''))) ) }}"
                    data-cantidad-entrada="{{ (float) $regla->cantidad_entrada }}"
                    data-rend-min="{{ (float) $regla->rendimiento_min }}"
                    data-rend-max="{{ (float) $regla->rendimiento_max }}"
                    data-tipo="{{ $regla->tipo_conversion }}"
                    @selected((string) old('regla_conversion_id') === (string) $regla->id)>
                    {{ $regla->nombre_insumo ?? ($regla->insumo?->nombre ?? 'Insumo') }} -> {{ $regla->producto?->nombre ?? ($regla->producto_resultante ?? 'Resultado') }}
                    ({{ $regla->tipo_conversion }})
                </option>
            @endforeach
        </select>
        @error('regla_conversion_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        @if($reglas->isEmpty())
            <small class="text-danger">No hay reglas activas para los insumos de órdenes pendientes.</small>
        @endif
    </div>

    <div class="col-md-4">
        <label for="rango_estimado" class="form-label fw-semibold">Rango estimado</label>
        <input type="text" id="rango_estimado" class="form-control" value="-" readonly>
    </div>

    <div class="col-md-6">
        <label for="cantidad_obtenida" class="form-label fw-semibold">Cantidad obtenida (solo variable)</label>
        <input type="number" step="0.01" min="0.01" name="cantidad_obtenida" id="cantidad_obtenida" class="form-control @error('cantidad_obtenida') is-invalid @enderror" value="{{ old('cantidad_obtenida', $produccionRecord->cantidad_obtenida) }}" placeholder="Ej: 25.00">
        @error('cantidad_obtenida')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="text-muted">Para reglas estáticas, el sistema calcula automáticamente.</small>
    </div>

    <div class="col-md-6 d-flex align-items-end">
        <div class="alert alert-info w-100 mb-0">
            La ejecución crea una producción asociada al detalle seleccionado (1 a 1).
        </div>
    </div>

    @if($errors->has('error'))
        <div class="col-12">
            <div class="alert alert-danger">{{ $errors->first('error') }}</div>
        </div>
    @endif

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('producciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-play me-1"></i>{{ $submitLabel ?? 'Ejecutar producción' }}</button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const detalleSelect = document.getElementById('orden_produccion_insumo_id');
        const cantidadRef = document.getElementById('cantidad_insumo_ref');
        const reglaSelect = document.getElementById('regla_conversion_id');
        const rangoEstimado = document.getElementById('rango_estimado');
        const cantidadObtenida = document.getElementById('cantidad_obtenida');

        function updateCantidadRef() {
            const selected = detalleSelect.options[detalleSelect.selectedIndex];
            const cantidad = selected?.dataset?.cantidad;
            cantidadRef.value = cantidad ? Number(cantidad).toFixed(2) : '-';
        }

        function filtrarReglasPorInsumo() {
            const detalle = detalleSelect.options[detalleSelect.selectedIndex];
            const insumoId = detalle?.dataset?.insumoId || '';
            const insumoNombre = String(detalle?.dataset?.insumoNombre || '').trim().toLowerCase();
            const hasDetalle = Boolean(insumoId);

            reglaSelect.disabled = !hasDetalle;

            Array.from(reglaSelect.options).forEach((opt, idx) => {
                if (idx === 0) {
                    opt.hidden = false;
                    return;
                }

                const reglaInsumoId = String(opt.dataset.insumoId || '');
                const reglaInsumoNombre = String(opt.dataset.insumoNombre || '').trim().toLowerCase();

                const coincidePorId = reglaInsumoId !== '' && reglaInsumoId !== '0' && reglaInsumoId === String(insumoId);
                const coincidePorNombre = reglaInsumoNombre !== '' && reglaInsumoNombre === insumoNombre;

                opt.hidden = !(coincidePorId || coincidePorNombre);
            });

            if (reglaSelect.selectedIndex > 0 && reglaSelect.options[reglaSelect.selectedIndex].hidden) {
                reglaSelect.value = '';
            }
        }

        function updateRango() {
            const detalle = detalleSelect.options[detalleSelect.selectedIndex];
            const regla = reglaSelect.options[reglaSelect.selectedIndex];

            if (!detalle?.dataset?.cantidad || !regla?.value) {
                rangoEstimado.value = '-';
                return;
            }

            const cantidad = Number(detalle.dataset.cantidad || 0);
            const entrada = Math.max(Number(regla.dataset.cantidadEntrada || 1), 0.0001);
            const rMin = Number(regla.dataset.rendMin || 0);
            const rMax = Number(regla.dataset.rendMax || 0);
            const tipo = String(regla.dataset.tipo || 'estatica').toLowerCase();

            const min = (cantidad / entrada) * rMin;
            const max = (cantidad / entrada) * rMax;

            if (tipo === 'estatica') {
                rangoEstimado.value = `${min.toFixed(2)} (fijo)`;
                cantidadObtenida.value = min.toFixed(2);
                cantidadObtenida.readOnly = true;
            } else {
                const low = Math.min(min, max);
                const high = Math.max(min, max);
                rangoEstimado.value = `${low.toFixed(2)} - ${high.toFixed(2)}`;
                cantidadObtenida.readOnly = false;
            }
        }

        detalleSelect.addEventListener('change', () => {
            updateCantidadRef();
            filtrarReglasPorInsumo();
            updateRango();
        });

        reglaSelect.addEventListener('change', updateRango);

        updateCantidadRef();
        filtrarReglasPorInsumo();
        updateRango();
    });
</script>
@endpush
