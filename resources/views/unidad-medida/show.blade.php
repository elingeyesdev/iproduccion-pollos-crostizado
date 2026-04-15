@extends('layouts.app')

@section('template_title')
    {{ $unidadMedida->name ?? __('Show') . " " . __('Unidad Medida') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Unidad Medida</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('unidad-medidas.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $unidadMedida->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Abreviatura:</strong>
                                    {{ $unidadMedida->abreviatura }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Factor Conversion:</strong>
                                    {{ $unidadMedida->factor_conversion }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $unidadMedida->estado }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
