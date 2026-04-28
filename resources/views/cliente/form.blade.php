<div class="row">
    <div class="col-md-6">
        <x-crud.form-input name="nombre" label="Nombre" :value="$cliente->nombre" placeholder="Nombre completo" required />
    </div>
    <div class="col-md-6">
        <x-crud.form-input name="telefono" label="Teléfono" :value="$cliente->telefono" placeholder="Ejemplo: 987654321" required />
    </div>

    <div class="col-12 d-flex justify-content-end gap-2 mt-2">
        <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary">Cancelar</a>
        <button type="submit" class="btn btn-app-primary"><i class="fa-solid fa-floppy-disk me-1"></i>{{ $submitLabel ?? 'Guardar' }}</button>
    </div>
</div>