@php
    $existingRows = $ordenProduccion->exists
        ? $ordenProduccion->insumos?->map(fn ($item) => [
            'insumo_id' => $item->insumo_id,
            'cantidad' => $item->cantidad,
        ])->values()->all()
        : [];

    $insumoRows = old('insumos', !empty($existingRows) ? $existingRows : [['insumo_id' => '', 'cantidad' => '']]);
@endphp

<div class="row g-3">
    <div class="col-md-6">
        <x-crud.form-input name="numero_orden" label="N° Orden de Producción" :value="$ordenProduccion->numero_orden" placeholder="Ejemplo: OR-PRO-2026-08-0001" />
        @if (!$ordenProduccion->exists)
            <small class="text-muted d-block mt-1"><i class="fa-solid fa-wand-magic-sparkles me-1"></i>Número generado automáticamente. Puedes modificarlo si lo deseas.</small>
        @endif
    </div>

    <div class="col-md-6">
        <label for="fecha_programada" class="form-label fw-semibold">Fecha programada</label>
        <input id="fecha_programada" type="date" name="fecha_programada"
               value="{{ old('fecha_programada', $ordenProduccion->fecha_programada ? \Illuminate\Support\Carbon::parse($ordenProduccion->fecha_programada)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="form-control @error('fecha_programada') is-invalid @enderror" required>
        @error('fecha_programada')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="regla_conversion_id" class="form-label fw-semibold">Receta / Regla de conversión</label>
        <select id="regla_conversion_id" name="regla_conversion_id" class="form-select @error('regla_conversion_id') is-invalid @enderror" required>
            <option value="">Selecciona la receta que se producirá con esta orden</option>
            @foreach ($reglas as $regla)
                <option
                    value="{{ $regla->id }}"
                    data-insumo-id="{{ (int) ($regla->insumo_id ?? 0) }}"
                    data-detalle-insumo-ids="{{ $regla->recetaConversiones->pluck('insumo_id')->implode(',') }}"
                    @selected((string) old('regla_conversion_id', $ordenProduccion->regla_conversion_id) === (string) $regla->id)
                >
                    {{ $regla->insumo?->nombre }}
                    @if ($regla->recetaConversiones->isNotEmpty())
                        + {{ $regla->recetaConversiones->map(fn ($d) => $d->insumo?->nombre)->filter()->implode(', ') }}
                    @endif
                    &rarr; {{ $regla->producto?->nombre ?? $regla->producto_resultante }}
                </option>
            @endforeach
        </select>
        @error('regla_conversion_id')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
        <small class="text-muted d-block mt-1">Al seleccionar una receta se sugieren automáticamente sus insumos; ajustá las cantidades según lo que vas a producir.</small>
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 fw-bold">Insumos para esta orden</h6>
            <button type="button" id="addInsumoRow" class="btn btn-sm btn-app-outline">
                <i class="fa-solid fa-plus me-1"></i> Agregar insumo
            </button>
        </div>

        @error('insumos')
            <div class="text-danger small mb-2">{{ $message }}</div>
        @enderror

        <div class="table-responsive">
            <table class="table align-middle" id="insumoTable">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th style="width: 200px;">Cantidad</th>
                        <th class="text-end">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($insumoRows as $index => $row)
                        <tr>
                            <td style="min-width:260px;">
                                <select name="insumos[{{ $index }}][insumo_id]" class="form-select @error('insumos.' . $index . '.insumo_id') is-invalid @enderror" required>
                                    <option value="">Selecciona un insumo</option>
                                    @foreach ($insumos as $insumo)
                                        <option value="{{ $insumo->id }}" @selected((string) ($row['insumo_id'] ?? '') === (string) $insumo->id)>
                                            {{ $insumo->nombre }} {{ $insumo->unidadMedida?->abreviatura ? '(' . $insumo->unidadMedida->abreviatura . ')' : '' }} - Stock actual: {{ number_format((float) $insumo->stock_actual, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('insumos.' . $index . '.insumo_id')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="insumos[{{ $index }}][cantidad]" value="{{ $row['cantidad'] ?? '' }}" class="form-control @error('insumos.' . $index . '.cantidad') is-invalid @enderror" placeholder="0" required>
                                @error('insumos.' . $index . '.cantidad')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-danger removeInsumoRow">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('orden-producciones.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar Orden' }}</button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('#insumoTable tbody');
        const addButton = document.getElementById('addInsumoRow');
        const reglaSelect = document.getElementById('regla_conversion_id');

        if (!tableBody || !addButton) return;

        function renumberRows() {
            Array.from(tableBody.querySelectorAll('tr')).forEach((row, index) => {
                row.querySelectorAll('select, input').forEach((field) => {
                    field.name = field.name.replace(/insumos\[\d+\]/, `insumos[${index}]`);
                });
            });
        }

        function addRow(insumoId) {
            const template = tableBody.querySelector('tr').cloneNode(true);
            template.querySelectorAll('input').forEach((input) => input.value = '');
            template.querySelectorAll('select').forEach((select) => select.value = insumoId ? String(insumoId) : '');
            tableBody.appendChild(template);
            renumberRows();
        }

        addButton.addEventListener('click', function () {
            addRow(null);
        });

        if (reglaSelect) {
            reglaSelect.addEventListener('change', function () {
                const option = reglaSelect.selectedOptions[0];
                if (!option || !option.value) return;

                const yaHayInsumosCargados = Array.from(tableBody.querySelectorAll('select'))
                    .some((select) => select.value);

                if (yaHayInsumosCargados) return;

                const insumoPrincipalId = option.dataset.insumoId;
                const detalleIds = (option.dataset.detalleInsumoIds || '')
                    .split(',')
                    .map((id) => id.trim())
                    .filter(Boolean);

                if (insumoPrincipalId && insumoPrincipalId !== '0') {
                    tableBody.querySelector('select').value = insumoPrincipalId;
                }

                detalleIds.forEach((id) => addRow(id));
            });
        }

        tableBody.addEventListener('click', function (event) {
            const button = event.target.closest('.removeInsumoRow');
            if (!button) return;

            if (tableBody.querySelectorAll('tr').length === 1) {
                tableBody.querySelector('input').value = '';
                tableBody.querySelector('select').value = '';
                return;
            }

            button.closest('tr').remove();
            renumberRows();
        });
    });
</script>
@endpush
