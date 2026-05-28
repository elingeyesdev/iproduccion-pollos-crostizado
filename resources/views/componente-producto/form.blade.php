<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$componenteProducto->nombre" placeholder="Ejemplo: Muslo" required />
    </div>

    <div class="col-md-6">
        <x-crud.form-input name="equivalencia" label="Equivalencia" :value="$componenteProducto->equivalencia" placeholder="Ejemplo: 1/4 de pollo" />
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('componente-productos.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>
