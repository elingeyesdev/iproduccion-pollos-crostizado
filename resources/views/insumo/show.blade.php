@extends('layouts.app')

@section('template_title')
    {{ $insumo->name ?? __('Show') . " " . __('Insumo') }}
@endsection

@section('content')
    <section class="content container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="float-left">
                            <span class="card-title">{{ __('Show') }} Insumo</span>
                        </div>
                        <div class="float-right">
                            <a class="btn btn-primary btn-sm" href="{{ route('insumos.index') }}"> {{ __('Back') }}</a>
                        </div>
                    </div>

                    <div class="card-body bg-white">
                        
                                <div class="form-group mb-2 mb20">
                                    <strong>Nombre:</strong>
                                    {{ $insumo->nombre }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Categoria Insumo Id:</strong>
                                    {{ $insumo->categoria_insumo_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Unidad Medida Id:</strong>
                                    {{ $insumo->unidad_medida_id }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Preciounitario:</strong>
                                    {{ $insumo->precioUnitario }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Stockactual:</strong>
                                    {{ $insumo->stockActual }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Stockminimo:</strong>
                                    {{ $insumo->stockMinimo }}
                                </div>
                                <div class="form-group mb-2 mb20">
                                    <strong>Estado:</strong>
                                    {{ $insumo->estado }}
                                </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
