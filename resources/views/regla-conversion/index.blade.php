@extends('layouts.crud')

@section('title', 'Reglas de Conversión')

@section('content')
    <x-crud.page-header title="Reglas de conversión" subtitle="Define rendimientos para la ejecución de producción.">
        <x-slot:actions>
            <a href="{{ route('regla-conversiones.create') }}" class="btn btn-regla-primary"><i class="fa-solid fa-plus me-1"></i> Crear regla</a>
        </x-slot:actions>
    </x-crud.page-header>

    <div class="card card-elevated mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('regla-conversiones.index') }}" class="row g-2 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label fw-semibold">Buscar regla</label>
                    <input id="search" type="text" name="search" value="{{ $search ?? '' }}" class="form-control" placeholder="Insumo o producto resultado">
                </div>
                <div class="col-md-6 d-flex gap-2">
                    <button class="btn btn-regla-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Buscar</button>
                    <a href="{{ route('regla-conversiones.index') }}" class="btn btn-regla-neutral"><i class="fa-solid fa-rotate-left me-1"></i> Limpiar</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-elevated">
        <div class="card-body table-responsive">
            <table class="table align-middle table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Insumo</th>
                        <th>Unidad entrada</th>
                        <th class="text-end">Cantidad entrada</th>
                        <th>Producto resultante</th>
                        <th>Unidad salida</th>
                        <th class="text-end">Rendimiento min</th>
                        <th class="text-end">Rendimiento max</th>
                        <th>Tipo</th>
                        <th class="text-center" style="width: 220px;">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reglas as $regla)
                        <tr>
                            <td>{{ $reglas->firstItem() + $loop->index }}</td>
                            <td>{{ $regla->insumo?->nombre ?? $regla->nombre_insumo ?? '-' }}</td>
                            <td>{{ $regla->unidadEntrada?->abreviatura ?? $regla->unidadEntrada?->nombre ?? $regla->unidad_entrada_id ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $regla->cantidad_entrada, 2) }}</td>
                            <td>{{ $regla->producto?->nombre ?? $regla->producto_resultante ?? '-' }}</td>
                            <td>{{ $regla->unidadSalida?->abreviatura ?? $regla->unidadSalida?->nombre ?? $regla->unidad_salida_id ?? '-' }}</td>
                            <td class="text-end">{{ number_format((float) $regla->rendimiento_min, 2) }}</td>
                            <td class="text-end">{{ number_format((float) $regla->rendimiento_max, 2) }}</td>
                            <td>
                                <span class="badge rounded-pill {{ strtolower((string) $regla->tipo_conversion) === 'variable' ? 'text-bg-warning' : 'badge-state' }}">
                                    {{ $regla->tipo_conversion }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('regla-conversiones.edit', $regla) }}" class="btn btn-sm btn-action-edit" title="Editar">
                                        <i class="fa-solid fa-pen-to-square me-1"></i>Editar
                                    </a>
                                    <form method="POST" action="{{ route('regla-conversiones.destroy', $regla) }}" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-action-delete" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar esta regla?');">
                                            <i class="fa-solid fa-trash-can me-1"></i>Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="text-center py-4 text-muted">No hay reglas registradas.</td></tr>
                    @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-end">{{ $reglas->links() }}</div>
        </div>
    </div>
@endsection
