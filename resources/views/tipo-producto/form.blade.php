<div class="row">
    <div class="col-md-6">
        <x-crud.form-input
            name="nombre"
            label="Nombre"
            :value="$tipoProducto->nombre"
            placeholder="Ejemplo: Plato principal"
            required
        />
    </div>

    <div class="col-md-6">
        <x-crud.form-input
            name="descripcion"
            label="Descripcion"
            :value="$tipoProducto->descripcion"
            placeholder="Breve descripcion del tipo de producto"
        />
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('tipo-productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary">
            <i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}
        </button>
    </div>
</div>
