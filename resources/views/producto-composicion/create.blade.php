@extends('layouts.crud')

@section('title', 'Crear Composición de Producto')

@section('content')
    <x-crud.page-header title="Crear Composición" subtitle="Define por primera vez los ingredientes que conforman un producto.">
        <x-slot:actions>
            <a href="{{ route('productos-composicion.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="row fade-slide">
        <div class="col-md-12">
            <div class="card card-elevated">
                <div class="card-body">
                    <form action="{{ route('productos-composicion.store') }}" method="POST">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label for="producto_id" class="form-label fw-semibold">Producto (Combo)</label>
                                <select id="producto_id" name="producto_id" class="form-select" required>
                                    <option value="">Seleccionar producto</option>
                                    @foreach ($productosActivos as $producto)
                                        <option value="{{ $producto->id }}" @selected(old('producto_id') == $producto->id)>
                                            {{ $producto->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('producto_id')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <h5 class="fw-bold mb-3 text-primary"><i class="fa-solid fa-box-open me-2"></i>Ingredientes (Productos Componentes)</h5>

                        <div id="ingredientes-container">
                            @php
                                $ingredientesActuales = old('ingredientes', [['id' => '', 'cantidad' => '']]);
                            @endphp

                            @foreach ($ingredientesActuales as $index => $item)
                                <div class="row g-2 mb-2 ingrediente-row align-items-end">
                                    <div class="col-md-7">
                                        <label class="form-label small text-muted">Producto Componente</label>
                                        <select name="ingredientes[{{ $index }}][id]" class="form-select" required>
                                            <option value="">Seleccionar producto</option>
                                            @foreach ($productosActivos as $p)
                                                <option value="{{ $p->id }}" @selected(($item['id'] ?? '') == $p->id)>
                                                    {{ $p->nombre }} (Stock: {{ (int) $p->cantidad_actual }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Cantidad en el plato</label>
                                        <input type="number" step="0.01" name="ingredientes[{{ $index }}][cantidad]" class="form-control" value="{{ $item['cantidad'] ?? '' }}" placeholder="1.00" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger remove-ingrediente w-100" @disabled(count($ingredientesActuales) <= 1)>
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @error('ingredientes')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror

                        <button type="button" id="add-ingrediente" class="btn btn-sm btn-outline-primary mt-3">
                            <i class="fa-solid fa-plus me-1"></i> Agregar otro componente
                        </button>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('productos-composicion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-app-primary px-4">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Composición
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('ingredientes-container');
            const addButton = document.getElementById('add-ingrediente');
            let rowCount = container.querySelectorAll('.ingrediente-row').length;

            addButton.addEventListener('click', () => {
                const firstRow = container.querySelector('.ingrediente-row');
                const newRow = firstRow.cloneNode(true);

                newRow.querySelectorAll('input').forEach(i => i.value = '');
                newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);

                newRow.querySelectorAll('[name*="ingredientes["]').forEach(field => {
                    field.name = field.name.replace(/ingredientes\[\d+\]/, `ingredientes[${rowCount}]`);
                });

                newRow.querySelector('.remove-ingrediente').disabled = false;
                container.appendChild(newRow);
                rowCount++;
                updateRemoveButtons();
            });

            container.addEventListener('click', (e) => {
                if (e.target.closest('.remove-ingrediente')) {
                    const row = e.target.closest('.ingrediente-row');
                    if (container.querySelectorAll('.ingrediente-row').length > 1) {
                        row.remove();
                        updateRemoveButtons();
                    }
                }
            });

            function updateRemoveButtons() {
                const buttons = container.querySelectorAll('.remove-ingrediente');
                buttons.forEach(btn => {
                    btn.disabled = buttons.length <= 1;
                });
            }
        });
    </script>
    @endpush
@endsection
