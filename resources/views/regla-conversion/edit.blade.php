@extends('layouts.crud')

@section('title', 'Editar Regla de Conversión')

@section('content')
    <x-crud.page-header title="Editar Regla" :subtitle="'Modificando regla de: ' . ($reglaConversion->insumo?->nombre ?? 'N/A')">
        <x-slot:actions>
            <a href="{{ route('regla-conversiones.index') }}" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="row fade-slide">
        <div class="col-md-9">
            <div class="card card-elevated">
                <div class="card-body">
                    <form method="POST" action="{{ route('regla-conversiones.update', $reglaConversion) }}" role="form">
                        @csrf
                        @method('PUT')
                        @include('regla-conversion.form', ['submitLabel' => 'Actualizar Regla'])
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card card-elevated border-danger">
                <div class="card-body">
                    <h5 class="card-title text-danger fw-bold mb-3">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>Opciones peligrosas
                    </h5>
                    <form method="POST" action="{{ route('regla-conversiones.destroy', $reglaConversion) }}" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-outline-danger w-100" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                            <i class="fa-solid fa-trash me-2"></i>Eliminar regla
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fa-solid fa-triangle-exclamation me-2"></i>Confirmar eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">¿Estás seguro de que deseas eliminar esta regla de conversión?</p>
                    <div class="alert alert-warning mb-0">
                        <small><i class="fa-solid fa-circle-info me-1"></i>Esta acción no se puede deshacer. La regla será marcada como inactiva.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('deleteForm').submit();">
                        <i class="fa-solid fa-trash me-1"></i>Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection
