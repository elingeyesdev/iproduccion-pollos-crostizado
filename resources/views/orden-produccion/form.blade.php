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
        <x-crud.form-input name="numero_orden" label="Número de orden" :value="$ordenProduccion->numero_orden" placeholder="Ejemplo: OP-2026-001" required />
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

        if (!tableBody || !addButton) return;

        function renumberRows() {
            Array.from(tableBody.querySelectorAll('tr')).forEach((row, index) => {
                row.querySelectorAll('select, input').forEach((field) => {
                    field.name = field.name.replace(/insumos\[\d+\]/, `insumos[${index}]`);
                });
            });
        }

        addButton.addEventListener('click', function () {
            const template = tableBody.querySelector('tr').cloneNode(true);
            template.querySelectorAll('input').forEach((input) => input.value = '');
            template.querySelectorAll('select').forEach((select) => select.value = '');
            tableBody.appendChild(template);
            renumberRows();
        });

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
