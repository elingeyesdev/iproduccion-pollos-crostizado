@extends('layouts.crud')

@section('title', 'Editar Composición de Producto')

@section('content')
    <x-crud.page-header title="Composición del producto" subtitle="Define los componentes del producto final.">
        <x-slot:actions>
            <a href="{{ route('componente-productos.composiciones') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <form method="POST" action="{{ route('componente-productos.composiciones.update', $producto) }}" novalidate>
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Nombre del producto</label>
                        <input type="text" class="form-control" value="{{ $producto->nombre }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Tipo</label>
                        <input type="text" class="form-control" value="{{ $producto->tipoProducto?->nombre ?? '-' }}" readonly>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="mb-0 fw-bold">Composición</h6>
                            <button type="button" id="addRow" class="btn btn-sm btn-app-outline">
                                <i class="fa-solid fa-plus me-1"></i> Agregar componente
                            </button>
                        </div>

                        @error('componentes')
                            <div class="text-danger small mb-2">{{ $message }}</div>
                        @enderror

                        <div class="table-responsive">
                            <table class="table align-middle" id="compositionTable">
                                <thead>
                                    <tr>
                                        <th>Componente</th>
                                        <th style="width: 220px;">Unidad</th>
                                        <th style="width: 180px;">Cantidad</th>
                                        <th class="text-end">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($componentRows as $index => $row)
                                        @php
                                            $selected = $componentesDisponibles->firstWhere('id', (int) ($row['componente_producto_id'] ?? 0));
                                        @endphp
                                        <tr>
                                            <td>
                                                <select name="componentes[{{ $index }}][componente_producto_id]" class="form-select @error('componentes.' . $index . '.componente_producto_id') is-invalid @enderror" required>
                                                    <option value="">Selecciona componente</option>
                                                    @foreach ($componentesDisponibles as $componente)
                                                        <option value="{{ $componente->id }}" data-unidad="{{ $componente->equivalencia ?? '-' }}" @selected((string) ($row['componente_producto_id'] ?? '') === (string) $componente->id)>
                                                            {{ $componente->nombre }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('componentes.' . $index . '.componente_producto_id')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control component-unit" value="{{ $selected?->equivalencia ?? '-' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="number" step="0.01" min="0.01" name="componentes[{{ $index }}][cantidad]" class="form-control @error('componentes.' . $index . '.cantidad') is-invalid @enderror" value="{{ $row['cantidad'] ?? '' }}" required>
                                                @error('componentes.' . $index . '.cantidad')
                                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </td>
                                            <td class="text-end">
                                                <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fa-solid fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                        <a href="{{ route('componente-productos.composiciones') }}" class="btn btn-outline-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>Guardar composición</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tableBody = document.querySelector('#compositionTable tbody');
        const addButton = document.getElementById('addRow');

        if (!tableBody || !addButton) return;

        const optionHtml = `@foreach ($componentesDisponibles as $componente)<option value="{{ $componente->id }}" data-unidad="{{ $componente->equivalencia ?? '-' }}">{{ $componente->nombre }}</option>@endforeach`;

        function renumberRows() {
            Array.from(tableBody.querySelectorAll('tr')).forEach((row, index) => {
                const select = row.querySelector('select');
                const qty = row.querySelector('input[name*="[cantidad]"]');

                if (select) select.name = `componentes[${index}][componente_producto_id]`;
                if (qty) qty.name = `componentes[${index}][cantidad]`;
            });
        }

        function refreshUnit(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const unitInput = selectElement.closest('tr').querySelector('.component-unit');
            unitInput.value = selectedOption ? (selectedOption.getAttribute('data-unidad') || '-') : '-';
        }

        addButton.addEventListener('click', function () {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    <select class="form-select" required>
                        <option value="">Selecciona componente</option>
                        ${optionHtml}
                    </select>
                </td>
                <td>
                    <input type="text" class="form-control component-unit" value="-" readonly>
                </td>
                <td>
                    <input type="number" step="0.01" min="0.01" class="form-control" required>
                </td>
                <td class="text-end">
                    <button type="button" class="btn btn-sm btn-danger removeRow"><i class="fa-solid fa-trash"></i></button>
                </td>
            `;

            tableBody.appendChild(row);
            renumberRows();
        });

        tableBody.addEventListener('change', function (event) {
            if (event.target.matches('select')) {
                refreshUnit(event.target);
            }
        });

        tableBody.addEventListener('click', function (event) {
            const button = event.target.closest('.removeRow');
            if (!button) return;

            if (tableBody.querySelectorAll('tr').length === 1) {
                const row = tableBody.querySelector('tr');
                row.querySelector('select').value = '';
                row.querySelector('.component-unit').value = '-';
                row.querySelector('input[name*="[cantidad]"]').value = '';
                return;
            }

            button.closest('tr').remove();
            renumberRows();
        });

        tableBody.querySelectorAll('select').forEach(refreshUnit);
        renumberRows();
    });
</script>
@endpush
