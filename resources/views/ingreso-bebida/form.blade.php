<div class="row g-3">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$ingresoBebida->nombre" placeholder="Ejemplo: Lote bebidas mayo" required />
    </div>

    <div class="col-md-6">
        <label for="fecha_ingreso" class="form-label fw-semibold">Fecha de ingreso</label>
        <input id="fecha_ingreso" type="date" name="fecha_ingreso"
               value="{{ old('fecha_ingreso', $ingresoBebida->fecha_ingreso ? \Illuminate\Support\Carbon::parse($ingresoBebida->fecha_ingreso)->format('Y-m-d') : now()->format('Y-m-d')) }}"
               class="form-control @error('fecha_ingreso') is-invalid @enderror" required>
        @error('fecha_ingreso')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
        <input id="descripcion" type="text" name="descripcion"
               value="{{ old('descripcion', $ingresoBebida->descripcion) }}"
               class="form-control @error('descripcion') is-invalid @enderror"
               placeholder="Detalle del ingreso">
        @error('descripcion')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    {{-- Detalle de productos bebida --}}
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0 fw-bold">Bebidas ingresadas</h6>
            <button type="button" id="addProductoRow" class="btn btn-sm btn-app-outline">
                <i class="fa-solid fa-plus me-1"></i> Agregar bebida
            </button>
        </div>

        <div class="table-responsive">
            <table class="table align-middle" id="productoTable">
                <thead>
                    <tr>
                        <th>Bebida (Producto)</th>
                        <th style="width:180px;">Cantidad</th>
                        <th style="width:180px;">Precio</th>
                        <th class="text-end">Eliminar</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach (old('productos', $productoRows ?? [['producto_id' => '', 'cantidad_ingresa' => '', 'precio' => '']]) as $index => $row)
                        <tr>
                            <td style="min-width:240px;">
                                <select name="productos[{{ $index }}][producto_id]"
                                        class="form-select @error('productos.' . $index . '.producto_id') is-invalid @enderror" required>
                                    <option value="">Selecciona una bebida</option>
                                    @foreach ($productos as $producto)
                                        <option value="{{ $producto->id }}"
                                            @selected((string) ($row['producto_id'] ?? '') === (string) $producto->id)>
                                            {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01"
                                       name="productos[{{ $index }}][cantidad_ingresa]"
                                       value="{{ $row['cantidad_ingresa'] ?? '' }}"
                                       class="form-control @error('productos.' . $index . '.cantidad_ingresa') is-invalid @enderror"
                                       placeholder="0" required>
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0"
                                       name="productos[{{ $index }}][precio]"
                                       value="{{ $row['precio'] ?? '' }}"
                                       class="form-control @error('productos.' . $index . '.precio') is-invalid @enderror"
                                       placeholder="0.00" required>
                            </td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-danger removeProductoRow">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @error('productos')
            <div class="text-danger small">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('ingreso-bebidas.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary">
            <i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}
        </button>
    </div>
</div>

@push('scripts')
<script>
(function () {
    const tableBody = document.querySelector('#productoTable tbody');
    const addButton = document.getElementById('addProductoRow');

    if (!tableBody || !addButton) return;

    function renumberRows() {
        Array.from(tableBody.querySelectorAll('tr')).forEach((row, index) => {
            row.querySelectorAll('select, input').forEach((field) => {
                field.name = field.name.replace(/productos\[\d+\]/, `productos[${index}]`);
            });
        });
    }

    addButton.addEventListener('click', function () {
        const template = tableBody.querySelector('tr').cloneNode(true);
        template.querySelectorAll('input').forEach((f) => (f.value = ''));
        template.querySelectorAll('select').forEach((f) => (f.value = ''));
        tableBody.appendChild(template);
        renumberRows();
    });

    tableBody.addEventListener('click', function (event) {
        const button = event.target.closest('.removeProductoRow');
        if (!button) return;

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
