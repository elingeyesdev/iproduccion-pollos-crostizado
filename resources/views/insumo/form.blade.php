<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $insumo?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="categoria_insumo_id" class="form-label">{{ __('Categoria Insumo Id') }}</label>
            <input type="text" name="categoria_insumo_id" class="form-control @error('categoria_insumo_id') is-invalid @enderror" value="{{ old('categoria_insumo_id', $insumo?->categoria_insumo_id) }}" id="categoria_insumo_id" placeholder="Categoria Insumo Id">
            {!! $errors->first('categoria_insumo_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="unidad_medida_id" class="form-label">{{ __('Unidad Medida Id') }}</label>
            <input type="text" name="unidad_medida_id" class="form-control @error('unidad_medida_id') is-invalid @enderror" value="{{ old('unidad_medida_id', $insumo?->unidad_medida_id) }}" id="unidad_medida_id" placeholder="Unidad Medida Id">
            {!! $errors->first('unidad_medida_id', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="precio_unitario" class="form-label">{{ __('Preciounitario') }}</label>
            <input type="text" name="precioUnitario" class="form-control @error('precioUnitario') is-invalid @enderror" value="{{ old('precioUnitario', $insumo?->precioUnitario) }}" id="precio_unitario" placeholder="Preciounitario">
            {!! $errors->first('precioUnitario', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="stock_actual" class="form-label">{{ __('Stockactual') }}</label>
            <input type="text" name="stockActual" class="form-control @error('stockActual') is-invalid @enderror" value="{{ old('stockActual', $insumo?->stockActual) }}" id="stock_actual" placeholder="Stockactual">
            {!! $errors->first('stockActual', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="stock_minimo" class="form-label">{{ __('Stockminimo') }}</label>
            <input type="text" name="stockMinimo" class="form-control @error('stockMinimo') is-invalid @enderror" value="{{ old('stockMinimo', $insumo?->stockMinimo) }}" id="stock_minimo" placeholder="Stockminimo">
            {!! $errors->first('stockMinimo', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estado" class="form-label">{{ __('Estado') }}</label>
            <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', $insumo?->estado) }}" id="estado" placeholder="Estado">
            {!! $errors->first('estado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>