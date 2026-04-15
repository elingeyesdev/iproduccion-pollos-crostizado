<div class="row padding-1 p-1">
    <div class="col-md-12">
        
        <div class="form-group mb-2 mb20">
            <label for="nombre" class="form-label">{{ __('Nombre') }}</label>
            <input type="text" name="nombre" class="form-control @error('nombre') is-invalid @enderror" value="{{ old('nombre', $unidadMedida?->nombre) }}" id="nombre" placeholder="Nombre">
            {!! $errors->first('nombre', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="abreviatura" class="form-label">{{ __('Abreviatura') }}</label>
            <input type="text" name="abreviatura" class="form-control @error('abreviatura') is-invalid @enderror" value="{{ old('abreviatura', $unidadMedida?->abreviatura) }}" id="abreviatura" placeholder="Abreviatura">
            {!! $errors->first('abreviatura', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="factor_conversion" class="form-label">{{ __('Factor Conversion') }}</label>
            <input type="text" name="factor_conversion" class="form-control @error('factor_conversion') is-invalid @enderror" value="{{ old('factor_conversion', $unidadMedida?->factor_conversion) }}" id="factor_conversion" placeholder="Factor Conversion">
            {!! $errors->first('factor_conversion', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>
        <div class="form-group mb-2 mb20">
            <label for="estado" class="form-label">{{ __('Estado') }}</label>
            <input type="text" name="estado" class="form-control @error('estado') is-invalid @enderror" value="{{ old('estado', $unidadMedida?->estado) }}" id="estado" placeholder="Estado">
            {!! $errors->first('estado', '<div class="invalid-feedback" role="alert"><strong>:message</strong></div>') !!}
        </div>

    </div>
    <div class="col-md-12 mt20 mt-2">
        <button type="submit" class="btn btn-primary">{{ __('Submit') }}</button>
    </div>
</div>