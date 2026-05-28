<div class="row g-3">
    <div class="col-md-6">
        <label for="fecha_ingreso" class="form-label fw-semibold">Fecha de ingreso</label>
        <input id="fecha_ingreso" type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', isset($ingreso) ? optional($ingreso->fecha_ingreso)->format('Y-m-d') : now()->format('Y-m-d')) }}" class="form-control @error('fecha_ingreso') is-invalid @enderror" required>
        @error('fecha_ingreso')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
        <input id="descripcion" type="text" name="descripcion" value="{{ old('descripcion', $ingreso->descripcion ?? '') }}" class="form-control @error('descripcion') is-invalid @enderror" placeholder="Descripción del ingreso">
        @error('descripcion')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 fw-bold">Detalle de insumos</h6>
            <button type="button" id="addDetalleRow" class="btn btn-sm btn-app-outline"><i class="fa-solid fa-plus me-1"></i> Agregar insumo</button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="detalleTable">
                <thead>
                    <tr>
                        <th>Insumo</th>
                        <th>Cantidad</th>
                        <th>Precio unitario</th>
                        <th class="text-end">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (old('detalles', $detalleRows ?? [['insumo_id' => '', 'cantidad' => '', 'precio_unitario' => '']]) as $index => $detalle)
                        <tr>
                            <td style="min-width: 240px;">
                                <select name="detalles[{{ $index }}][insumo_id]" class="form-select" required>
                                    <option value="">Selecciona un insumo</option>
                                    @foreach ($insumos as $insumo)
                                        <option value="{{ $insumo->id }}" @selected((string) ($detalle['insumo_id'] ?? '') === (string) $insumo->id)>
                                            {{ $insumo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="width: 160px;">
                                <input type="number" step="0.01" min="0.01" name="detalles[{{ $index }}][cantidad]" value="{{ $detalle['cantidad'] ?? '' }}" class="form-control" required>
                            </td>
                            <td style="width: 180px;">
                                <input type="number" step="0.01" min="0" name="detalles[{{ $index }}][precio_unitario]" value="{{ $detalle['precio_unitario'] ?? '' }}" class="form-control" required>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-danger removeDetalleRow"><i class="fa-solid fa-trash"></i></button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @error('detalles')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @error('detalles.*.insumo_id')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @error('detalles.*.cantidad')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
        @error('detalles.*.precio_unitario')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('ingreso-insumos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const tableBody = document.querySelector('#detalleTable tbody');
    const addButton = document.getElementById('addDetalleRow');

    if (!tableBody || !addButton) {
        return;
    }

    function renumberRows() {
        Array.from(tableBody.querySelectorAll('tr')).forEach((row, index) => {
            row.querySelectorAll('select, input').forEach((field) => {
                field.name = field.name.replace(/detalles\[\d+\]/, `detalles[${index}]`);
            });
        });
    }

    addButton.addEventListener('click', function () {
        const template = tableBody.querySelector('tr').cloneNode(true);

        template.querySelectorAll('input').forEach((field) => field.value = '');
        template.querySelectorAll('select').forEach((field) => field.value = '');
        tableBody.appendChild(template);
        renumberRows();
    });

    tableBody.addEventListener('click', function (event) {
        const button = event.target.closest('.removeDetalleRow');
        if (!button) {
            return;
        }

        if (tableBody.querySelectorAll('tr').length === 1) {
            tableBody.querySelector('input').value = '';
            tableBody.querySelector('select').value = '';
            return;
        }

        button.closest('tr').remove();
        renumberRows();
    });
})();
</script>
@endpush
