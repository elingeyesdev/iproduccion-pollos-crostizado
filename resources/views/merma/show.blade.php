@extends('layouts.crud')

@section('title', 'Detalle Merma')

@section('content')
    <x-crud.page-header title="Detalle de merma" subtitle="Información del registro de merma.">
        <x-slot:actions>
            <a href="{{ route('mermas.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
            <a href="{{ route('mermas.edit', $merma) }}" class="btn btn-app-outline"><i class="fa-solid fa-pen me-1"></i> Editar</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated">
        <div class="card-body">
            <dl class="row mb-0">
                <dt class="col-sm-3">Producción</dt>
                <dd class="col-sm-9">{{ $merma->produccion?->ordenProduccion?->numero_orden ?? ('Producción #' . $merma->produccion_id) }}</dd>

                <dt class="col-sm-3">Cantidad</dt>
                <dd class="col-sm-9">{{ number_format((float) $merma->cantidad, 2) }}</dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    <span class="badge rounded-pill {{ (int) $merma->estado === 1 ? 'badge-state' : 'text-bg-secondary' }}">
                        {{ (int) $merma->estado === 1 ? 'Activo' : 'Inactivo' }}
                    </span>
                </dd>
            </dl>
        </div>
    </div>
@endsection
