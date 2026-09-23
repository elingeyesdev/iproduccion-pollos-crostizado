@extends('layouts.crud')

@section('title', 'Configurar Combo: ' . $producto->nombre)

@section('content')
    <x-crud.page-header title="Configurar Combo" :subtitle="'Definiendo los componentes de: ' . $producto->nombre">
        <x-slot:actions>
            <a href="{{ route('productos-composicion.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="row fade-slide">
        <div class="col-md-4">
            <div class="card card-elevated mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-3 text-danger"><i class="fa-solid fa-circle-info me-2"></i>Información del Plato</h5>
                    <div class="mb-3">
                        <small class="text-muted d-block">Nombre del Producto</small>
                        <span class="fw-bold fs-5">{{ $producto->nombre }}</span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Tipo</small>
                        <span class="fw-semibold">{{ $producto->tipoProducto?->nombre }}</span>
                    </div>
                    <div class="mb-0">
                        <small class="text-muted d-block">Precio de Venta</small>
                        <span class="fw-bold fs-4 text-success">S/ {{ number_format($producto->precio, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-elevated">
                <div class="card-body">
                    <h5 class="fw-bold mb-4 text-primary"><i class="fa-solid fa-box-open me-2"></i>Ingredientes (Productos Componentes)</h5>
                    
                    <form action="{{ route('productos-composicion.update', $producto) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div id="ingredientes-container">
                            @php
                                $ingredientesActuales = old('ingredientes', $producto->ingredientes->map(fn($i) => ['id' => $i->id, 'cantidad' => $i->pivot->cantidad])->toArray());
                            @endphp

                            @forelse ($ingredientesActuales as $index => $item)
                                <div class="row g-2 mb-2 ingrediente-row align-items-end">
                                    <div class="col-md-7">
                                        <label class="form-label small text-muted">Producto Componente</label>
                                        <select name="ingredientes[{{ $index }}][id]" class="form-select" required>
                                            <option value="">Seleccionar producto</option>
                                            @foreach ($todosLosProductos as $p)
                                                <option value="{{ $p->id }}" @selected(($item['id'] ?? '') == $p->id)>
                                                    {{ $p->nombre }} (Stock: {{ (int)$p->cantidad_actual }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Cantidad en el plato</label>
                                        <input type="number" step="0.01" name="ingredientes[{{ $index }}][cantidad]" class="form-control" value="{{ $item['cantidad'] ?? '' }}" placeholder="1.00" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger remove-ingrediente w-100">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="row g-2 mb-2 ingrediente-row align-items-end">
                                    <div class="col-md-7">
                                        <label class="form-label small text-muted">Producto Componente</label>
                                        <select name="ingredientes[0][id]" class="form-select" required>
                                            <option value="">Seleccionar producto</option>
                                            @foreach ($todosLosProductos as $p)
                                                <option value="{{ $p->id }}">
                                                    {{ $p->nombre }} (Stock: {{ (int)$p->cantidad_actual }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label small text-muted">Cantidad en el plato</label>
                                        <input type="number" step="0.01" name="ingredientes[0][cantidad]" class="form-control" placeholder="1.00" required>
                                    </div>
                                    <div class="col-md-1">
                                        <button type="button" class="btn btn-outline-danger remove-ingrediente w-100" disabled>
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        <button type="button" id="add-ingrediente" class="btn btn-sm btn-outline-primary mt-3">
                            <i class="fa-solid fa-plus me-1"></i> Agregar otro componente
                        </button>

                        <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-2">
                            <a href="{{ route('productos-composicion.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-app-primary px-4">
                                <i class="fa-solid fa-floppy-disk me-2"></i>Guardar Configuración de Combo
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
            let rowCount = {{ count($ingredientesActuales) > 0 ? count($ingredientesActuales) : 1 }};

            addButton.addEventListener('click', () => {
                const firstRow = container.querySelector('.ingrediente-row');
                const newRow = firstRow.cloneNode(true);
                
                // Limpiar inputs
                newRow.querySelectorAll('input').forEach(i => i.value = '');
                newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
                
                // Actualizar nombres
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
