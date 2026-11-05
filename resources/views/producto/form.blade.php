<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$producto->nombre" placeholder="Ejemplo: Pollo entero" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="precio" label="Precio" type="number" step="0.01" :value="$producto->precio" placeholder="0.00" required />
    </div>

    <div class="col-md-6">
        <div class="mb-3">
            <label for="tipo_producto_id" class="form-label fw-semibold">Tipo de producto</label>
            <select id="tipo_producto_id" name="tipo_producto_id" class="form-select @error('tipo_producto_id') is-invalid @enderror" required>
                <option value="">Selecciona un tipo</option>
                @foreach ($tipoProductos as $tipo)
                    <option value="{{ $tipo->id }}" @selected((string) old('tipo_producto_id', (string) $producto->tipo_producto_id) === (string) $tipo->id)>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
            @error('tipo_producto_id')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        @if ($producto->exists)
            <x-crud.form-input name="cantidad_actual" label="Cantidad actual" type="number" :value="$producto->cantidad_actual" placeholder="0" required />
        @else
            <x-crud.form-input name="cantidad_actual" label="Cantidad actual" type="number" value="0" readonly required />
        @endif
    </div>

    <div class="col-12">
        <div class="mb-3">
            <label for="imagen" class="form-label fw-semibold">Foto del producto <span class="text-muted fw-normal">(opcional)</span></label>
            <div class="d-flex align-items-start gap-3">
                <div class="producto-foto-preview border rounded d-flex align-items-center justify-content-center bg-light overflow-hidden flex-shrink-0" style="width: 120px; height: 120px;">
                    <img id="imagen-preview" src="{{ $producto->imagen_url }}" alt="Vista previa" class="w-100 h-100 {{ $producto->imagen_url ? '' : 'd-none' }}" style="object-fit: cover;">
                    <i id="imagen-placeholder" class="fa-solid fa-image fa-2x text-muted opacity-50 {{ $producto->imagen_url ? 'd-none' : '' }}"></i>
                </div>
                <div class="flex-grow-1">
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/png,image/webp" class="form-control @error('imagen') is-invalid @enderror">
                    <div class="form-text">JPG, PNG o WEBP. Máximo 2 MB. Se mostrará en el punto de venta.</div>
                    @error('imagen')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @if ($producto->imagen)
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" value="1" id="quitar_imagen" name="quitar_imagen">
                            <label class="form-check-label" for="quitar_imagen">Quitar foto actual</label>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('imagen');
        const preview = document.getElementById('imagen-preview');
        const placeholder = document.getElementById('imagen-placeholder');
        const quitar = document.getElementById('quitar_imagen');
        const original = preview.getAttribute('src');

        function mostrar(src) {
            preview.classList.toggle('d-none', !src);
            placeholder.classList.toggle('d-none', !!src);
            if (src) preview.src = src;
        }

        input.addEventListener('change', function () {
            const file = input.files[0];
            if (file) {
                if (quitar) quitar.checked = false;
                mostrar(URL.createObjectURL(file));
            } else {
                mostrar(quitar?.checked ? null : original);
            }
        });

        quitar?.addEventListener('change', function () {
            if (quitar.checked) {
                input.value = '';
                mostrar(null);
            } else {
                mostrar(original);
            }
        });
    });
</script>
@endpush
