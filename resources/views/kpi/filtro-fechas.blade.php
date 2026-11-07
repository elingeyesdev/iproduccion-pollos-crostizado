<div class="card card-elevated mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route($ruta) }}" class="row g-2 align-items-end" aria-label="Filtrar indicadores por fecha">
            <div class="col-md-4">
                <label for="desde" class="form-label fw-semibold">Desde</label>
                <input id="desde" type="date" name="desde" value="{{ $desde->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-4">
                <label for="hasta" class="form-label fw-semibold">Hasta</label>
                <input id="hasta" type="date" name="hasta" value="{{ $hasta->toDateString() }}" class="form-control">
            </div>
            <div class="col-md-4 d-flex gap-2">
                <button class="btn btn-app-primary" type="submit"><i class="fa-solid fa-magnifying-glass me-1"></i> Consultar</button>
                <a href="{{ route($ruta) }}" class="btn btn-outline-secondary">Mes actual</a>
            </div>
        </form>
        <p class="text-muted small mb-0 mt-2">
            Comparación contra el período anterior de igual duración
            ({{ $antDesde->format('d/m/Y') }} – {{ $antHasta->format('d/m/Y') }}).
        </p>
    </div>
</div>
