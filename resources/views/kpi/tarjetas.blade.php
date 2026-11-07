<div class="row g-3 mb-4" role="list" aria-label="Indicadores KPI">
    @foreach ($tarjetas as [$clave, $etiqueta, $valor, $icono, $subirEsBueno, $ayuda])
        @php
            $pct = $clave ? $variaciones[$clave] : null;
            $color = $pct === null || $pct == 0 ? 'text-muted' : (($pct > 0) === $subirEsBueno ? 'text-success' : 'text-danger');
        @endphp
        <div class="col-12 col-sm-6 col-xl-3" role="listitem">
            <div class="card card-elevated h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1"><i class="{{ $icono }} me-1"></i> {{ $etiqueta }}</p>
                    <h3 class="fw-bold mb-1">{{ $valor }}</h3>
                    @if ($clave)
                        <span class="small fw-semibold {{ $color }}">
                            @if ($pct === null)
                                Sin datos del período anterior
                            @else
                                <i class="fa-solid {{ $pct > 0 ? 'fa-arrow-trend-up' : ($pct < 0 ? 'fa-arrow-trend-down' : 'fa-minus') }}"></i>
                                {{ $pct > 0 ? '+' : '' }}{{ $pct }}% vs período anterior
                            @endif
                        </span>
                    @endif
                    <p class="text-muted small mb-0 mt-1">{{ $ayuda }}</p>
                </div>
            </div>
        </div>
    @endforeach
</div>
